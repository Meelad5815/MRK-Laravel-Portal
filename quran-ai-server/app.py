import asyncio
import os
import tempfile
import urllib.request
import wave
from pathlib import Path
from typing import Optional

import numpy as np
import sherpa_onnx
from fastapi import FastAPI, File, HTTPException, Request, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from starlette.concurrency import run_in_threadpool

APP_VERSION = "0.1.1"
SAMPLE_RATE = 16000
MAX_AUDIO_SECONDS = int(os.getenv("MAX_AUDIO_SECONDS", "15"))
MAX_UPLOAD_BYTES = int(os.getenv("MAX_UPLOAD_BYTES", str(2 * 1024 * 1024)))
# Allow a small multipart boundary/header overhead above the file-size limit.
MAX_REQUEST_BYTES = MAX_UPLOAD_BYTES + 64 * 1024
MODEL_DIR = Path(os.getenv("MODEL_DIR", "/tmp/mrk-quran-asr"))
MODEL_URLS = {
    "model.int8.onnx": "https://huggingface.co/Abdou/quran-asr-small/resolve/main/model.int8.onnx",
    "tokens.txt": "https://huggingface.co/Abdou/quran-asr-small/resolve/main/tokens.txt",
}
ALLOWED_ORIGINS = [
    item.strip()
    for item in os.getenv(
        "ALLOWED_ORIGINS",
        "https://meeladrazakhokhar786.freehosting.dev,http://localhost:8000",
    ).split(",")
    if item.strip()
]


class RequestBodyLimitMiddleware:
    """Bound multipart request bodies before Starlette parses/spools uploads."""

    def __init__(self, app, max_bytes: int):
        self.app = app
        self.max_bytes = max_bytes

    async def __call__(self, scope, receive, send):
        if scope["type"] != "http" or scope.get("method") != "POST" or scope.get("path") != "/transcribe":
            await self.app(scope, receive, send)
            return

        headers = dict(scope.get("headers") or [])
        raw_length = headers.get(b"content-length")
        if raw_length:
            try:
                if int(raw_length) > self.max_bytes:
                    await self._too_large(scope, receive, send)
                    return
            except ValueError:
                pass

        body = bytearray()
        more_body = True
        while more_body:
            message = await receive()
            if message["type"] == "http.disconnect":
                return
            if message["type"] != "http.request":
                continue
            body.extend(message.get("body", b""))
            if len(body) > self.max_bytes:
                await self._too_large(scope, receive, send)
                return
            more_body = message.get("more_body", False)

        delivered = False

        async def replay_receive():
            nonlocal delivered
            if not delivered:
                delivered = True
                return {"type": "http.request", "body": bytes(body), "more_body": False}
            return {"type": "http.disconnect"}

        await self.app(scope, replay_receive, send)

    async def _too_large(self, scope, receive, send):
        payload = b'{"detail":"Request body is too large."}'
        await send({
            "type": "http.response.start",
            "status": 413,
            "headers": [(b"content-type", b"application/json"), (b"content-length", str(len(payload)).encode())],
        })
        await send({"type": "http.response.body", "body": payload})


app = FastAPI(
    title="MRK Quran ASR API",
    description="Arabic Quran recitation transcription using the free Abdou Quran ASR model and sherpa-onnx.",
    version=APP_VERSION,
)
app.add_middleware(RequestBodyLimitMiddleware, max_bytes=MAX_REQUEST_BYTES)
app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS,
    allow_credentials=False,
    allow_methods=["GET", "POST", "OPTIONS"],
    allow_headers=["*"],
)

recognizer: Optional[sherpa_onnx.OfflineRecognizer] = None
model_error: Optional[str] = None
inference_lock = asyncio.Lock()


def ensure_model_files() -> None:
    MODEL_DIR.mkdir(parents=True, exist_ok=True)
    for filename, url in MODEL_URLS.items():
        destination = MODEL_DIR / filename
        if destination.exists() and destination.stat().st_size > 0:
            continue
        temporary = destination.with_suffix(destination.suffix + ".part")
        request = urllib.request.Request(url, headers={"User-Agent": "MRK-Quran-ASR/0.1"})
        with urllib.request.urlopen(request, timeout=90) as response, temporary.open("wb") as output:
            while True:
                chunk = response.read(1024 * 1024)
                if not chunk:
                    break
                output.write(chunk)
        temporary.replace(destination)


def initialize_recognizer() -> None:
    global recognizer, model_error
    try:
        ensure_model_files()
        recognizer = sherpa_onnx.OfflineRecognizer.from_zipformer_ctc(
            model=str(MODEL_DIR / "model.int8.onnx"),
            tokens=str(MODEL_DIR / "tokens.txt"),
            num_threads=max(1, min(2, os.cpu_count() or 1)),
            sample_rate=SAMPLE_RATE,
            feature_dim=80,
            decoding_method="greedy_search",
            provider="cpu",
            debug=False,
        )
        model_error = None
    except Exception as exc:
        recognizer = None
        model_error = f"{type(exc).__name__}: {exc}"
        raise


@app.on_event("startup")
async def startup_event() -> None:
    await run_in_threadpool(initialize_recognizer)


@app.get("/")
async def root():
    return {
        "service": "MRK Quran ASR API",
        "version": APP_VERSION,
        "health": "/health",
        "transcribe": "POST /transcribe (multipart WAV file field: audio)",
    }


@app.get("/health")
async def health():
    return {
        "status": "ok" if recognizer is not None else "starting_or_unavailable",
        "model_loaded": recognizer is not None,
        "model": "Abdou/quran-asr-small",
        "sample_rate": SAMPLE_RATE,
        "max_audio_seconds": MAX_AUDIO_SECONDS,
        "error": model_error,
    }


def decode_wav_bytes(data: bytes) -> tuple[np.ndarray, int]:
    try:
        with tempfile.SpooledTemporaryFile() as temp:
            temp.write(data)
            temp.seek(0)
            with wave.open(temp, "rb") as wav:
                channels = wav.getnchannels()
                sample_width = wav.getsampwidth()
                sample_rate = wav.getframerate()
                frame_count = wav.getnframes()
                compression = wav.getcomptype()
                if compression != "NONE":
                    raise ValueError("Compressed WAV is not supported.")
                if channels != 1:
                    raise ValueError("Audio must be mono.")
                if sample_width != 2:
                    raise ValueError("Audio must use 16-bit PCM.")
                if frame_count <= 0:
                    raise ValueError("Audio is empty.")
                if sample_rate <= 0:
                    raise ValueError("Invalid sample rate.")
                duration = frame_count / float(sample_rate)
                if duration > MAX_AUDIO_SECONDS:
                    raise ValueError(f"Audio is too long; maximum is {MAX_AUDIO_SECONDS} seconds.")
                pcm = wav.readframes(frame_count)
        samples = np.frombuffer(pcm, dtype="<i2").astype(np.float32) / 32768.0
        if sample_rate != SAMPLE_RATE:
            old_times = np.arange(samples.size, dtype=np.float64) / sample_rate
            new_length = max(1, int(round(samples.size * SAMPLE_RATE / sample_rate)))
            new_times = np.arange(new_length, dtype=np.float64) / SAMPLE_RATE
            samples = np.interp(new_times, old_times, samples).astype(np.float32)
        return samples, int(round(duration * 1000))
    except (wave.Error, EOFError, OSError) as exc:
        raise ValueError("Please upload a valid WAV audio file.") from exc


def transcribe_samples(samples: np.ndarray) -> str:
    if recognizer is None:
        raise RuntimeError("The Quran ASR model is not ready.")
    stream = recognizer.create_stream()
    try:
        stream.accept_waveform(SAMPLE_RATE, samples)
        recognizer.decode_stream(stream)
        result = stream.result
        return (result.text if result else "").strip()
    finally:
        del stream


@app.post("/transcribe")
async def transcribe(audio: UploadFile = File(...)):
    if recognizer is None:
        raise HTTPException(status_code=503, detail="Quran AI model is not ready. Please retry shortly.")
    if audio.content_type not in {"audio/wav", "audio/x-wav", "audio/wave", "application/octet-stream"}:
        await audio.close()
        raise HTTPException(status_code=415, detail="Please send a WAV audio file.")
    data = await audio.read(MAX_UPLOAD_BYTES + 1)
    await audio.close()
    if not data:
        raise HTTPException(status_code=400, detail="Audio file is empty.")
    if len(data) > MAX_UPLOAD_BYTES:
        raise HTTPException(status_code=413, detail="Audio file is too large.")
    try:
        samples, duration_ms = decode_wav_bytes(data)
    except ValueError as exc:
        raise HTTPException(status_code=400, detail=str(exc)) from exc

    try:
        async with inference_lock:
            text = await run_in_threadpool(transcribe_samples, samples)
    except Exception as exc:
        raise HTTPException(status_code=503, detail="Audio recognition failed. Please try again.") from exc

    return {
        "text": text,
        "duration_ms": duration_ms,
        "sample_rate": SAMPLE_RATE,
        "model": "Abdou/quran-asr-small",
        "audio_saved": False,
    }
