# MRK Quran Murajaah – third-party notices

The offline speech-recognition runtime is built from [sherpa-onnx](https://github.com/k2-fsa/sherpa-onnx), licensed under Apache-2.0.

The Quran recitation recognition model and its code are from [Abdou/quran-asr-small](https://huggingface.co/Abdou/quran-asr-small), licensed under MIT. The published int8 ONNX model is downloaded during the deployment build; it is not committed to this repository.

The VAD asset is the Silero VAD model distributed by the sherpa-onnx project. Its original license and attribution are retained by that project.

No paid API or hosted inference service is used. Audio recognition is performed locally in the visitor's browser using WebAssembly. The model and runtime are downloaded on first use and cached by the browser service worker. First-time download requires an internet connection and sufficient device storage.
