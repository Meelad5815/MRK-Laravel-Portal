# MRK VU Companion (prototype)

This is a small Android WebView companion for the MRK VU Smart Study dashboard.

## Current prototype scope

- Opens the official VULMS site inside the app.
- The student signs in directly on the official VULMS page.
- A user-initiated button reads visible page text into a local review box only.
- The student manually confirms course, activity type, title and deadline before sending one study item to MRK.
- Sends only the reviewed study-item fields to the MRK Laravel API over HTTPS.
- Does not read password fields, export cookies, intercept network traffic, or send the VULMS page text to MRK.

VULMS page structure and any automated extraction rules must be verified against the live authenticated interface and VU policies before expanding extraction.

## Build

Open this folder in Android Studio (Giraffe or newer) and use JDK 17 / Android SDK 35. The Gradle project uses Android Gradle Plugin 8.7.3 and Kotlin 2.0.21.

## Setup

1. Sign in to the MRK portal in a normal browser.
2. Open VU Study and generate a Companion API token. Copy it once; MRK stores only its SHA-256 hash.
3. Open the app, enter the MRK portal base URL (for example, your own portal domain), and paste the token.
4. Open VULMS in the app and sign in directly with VU.
5. Use **Read visible page** to inspect the page text locally. This does not send page text.
6. Enter the study item fields and press **Send reviewed item**.

The token is kept in app memory only in this prototype. It is not saved to disk. Close the app to clear it. Revoke the token from the MRK dashboard if the device is lost.

## Important

This prototype is not an official Virtual University app and is not endorsed by VU. It does not claim full automatic VULMS scraping. Do not automate extraction if it conflicts with VULMS terms or institutional policy.
