# MRK VU Study Companion (Android)

A lightweight Android companion for the MRK VU Study dashboard.

## Features
- Opens the official VULMS website inside Android WebView.
- You sign in directly on VULMS; the app does not read or send your password.
- Adds an MRK capture button to VU pages.
- Lets you review the title, category, course code and optional deadline before sending.
- Sends only the selected visible page text to the MRK Laravel API over HTTPS.
- Encrypts the MRK companion token with Android Keystore (AES-GCM).

## First-time setup
1. Sign in to the MRK Portal in Chrome and open /vu-study.
2. Generate a Companion Token in the dashboard Android Companion section.
3. Copy the token once and paste it into this app. The plain token is shown only once.
4. Save the token, then open VULMS in the app.
5. Log in on the official VULMS page. Open an announcement, assignment, quiz, GDB or exam page.
6. Tap MRK میں بھیجیں, review the details and save.

The token can be revoked from the MRK dashboard. Creating a new token revokes the previous one.

## Build
GitHub Actions builds a debug APK and uploads it as the mrk-vu-study-android-apk artifact. The project uses Gradle 8.9, Android Gradle Plugin 8.7.3 and JDK 17.

This is a companion app, not an official Virtual University app. It does not bypass VULMS security controls and does not perform background scraping or automatic login.
