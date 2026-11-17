# MRK Quran AI Server

A small FastAPI service that runs the same free Arabic Quran ASR model used by MRK's browser-side feature:

- Model: [Abdou/quran-asr-small](https://huggingface.co/Abdou/quran-asr-small)
- Runtime: [sherpa-onnx](https://github.com/k2-fsa/sherpa-onnx)
- Inference: CPU, model loaded once when the service starts.
- Audio: accepted as short mono WAV, processed in memory, and not saved by this application.

## API

- `GET /` — service information
- `GET /health` — model readiness
- `POST /transcribe` — multipart form upload with the field name `audio`

The transcription endpoint expects mono 16-bit PCM WAV. It accepts sample rates other than 16 kHz and resamples them to 16 kHz. The default maximum recording duration is 15 seconds and the default upload limit is 2 MiB.

Example response:

```json
{
  "text": "recognized Arabic words",
  "duration_ms": 4200,
  "sample_rate": 16000,
  "model": "Abdou/quran-asr-small",
  "audio_saved": false
}
```

## Deploy on Render (free test deployment)

1. Push this folder to GitHub.
2. In Render, create **New → Web Service** and connect this repository.
3. Set **Root Directory** to `quran-ai-server`.
4. Select the Python runtime.
5. Build command: `pip install -r requirements.txt`
6. Start command: `uvicorn app:app --host 0.0.0.0 --port $PORT`
7. Select the Free instance type.
8. Set the environment variable `ALLOWED_ORIGINS` to the exact MRK website origin, for example `https://meeladrazakhokhar786.freehosting.dev`.

Render's free web services sleep after 15 minutes without inbound traffic and typically need about a minute to wake up. They can also restart, and their filesystem is temporary. The model is therefore downloaded again after a fresh instance start. This is a test/hobby deployment, not a guaranteed always-on production server.

## Privacy and limitations

- The audio is transmitted from the visitor's browser to the configured API host for inference.
- This app does not intentionally write uploaded audio to disk or retain recordings.
- Hosting providers may still process request metadata and transient infrastructure data under their own policies.
- Do not send private recordings without the user's informed consent.
- The model can make recognition errors. It does not verify tajweed, makharij, waqf/ibtida, or qira'at and must not replace a qualified Quran teacher or the Mushaf.
- Public free hosting has quotas, cold starts, resource limits, and no uptime guarantee. Protect the endpoint against abuse before using it for a large public audience.

## Local run

```bash
pip install -r requirements.txt
uvicorn app:app --host 127.0.0.1 --port 8000
```
