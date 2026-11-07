# MRK Google Automation Hub

## Purpose
A low-cost Google Apps Script automation layer for MRK projects. Google Sheets is used for reporting and secondary backups; Laravel/MySQL remains the primary database for application records.

## Important: preserve the Fitness bridge
The existing `integrations/google-apps-script/Code.gs` is the Fitness-to-Sheets bridge. Do not replace it. Add `Hub.gs` as a separate script file in the same Apps Script project.

## Phase 1: Hub foundation
The initial `Hub.gs` adds:
- A spreadsheet menu named **MRK Automation**
- Safe initialization of `MRK Hub Config` and `MRK Automation Log` tabs
- A service access check for Sheets, Drive, Gmail, Calendar, Docs and Forms
- A Gmail status-report draft function; it creates a draft only and never sends mail

No scheduled Hub triggers are installed in this phase. No email is sent automatically. No existing spreadsheet tabs are deleted.

## Setup
1. Open the Apps Script project already used for the Fitness bridge.
2. Use the plus button beside Files and add a script file named `Hub`.
3. Paste the contents of `Hub.gs` from this folder and save.
4. In Project Settings > Script properties, keep the existing `SPREADSHEET_ID` property for the Fitness spreadsheet. Do not share its value publicly.
5. Set Project Settings > Time zone to `Asia/Karachi`.
6. Reload the linked Google Sheet. Open **MRK Automation > Initialize Hub tabs** and authorize the requested Google permissions.
7. Run **MRK Automation > Check Google services** and inspect the `MRK Service Status` tab.

Optional email draft:
- Add `MRK_REPORT_EMAIL` as a Script Property.
- Run **MRK Automation > Create email report draft**.
- The function creates a draft in Gmail; the user reviews and sends it manually.

## Phase 2 roadmap
1. Laravel Fitness: keep MySQL primary; daily Apps Script pull backup to Sheets.
2. MRK Digital: client/order/income/expense reports after the relevant Laravel tables and fields are confirmed.
3. MRK Madrasa: attendance/admission summaries only after role permissions and data fields are reviewed.
4. Docs/Drive: generate receipts, certificates and PDF reports from approved templates.
5. Forms: create admission, order and feedback forms; route submissions to a controlled Sheet.
6. Calendar: create reminders only from confirmed dates and with user approval.
7. Gmail: create drafts by default; sending automation requires explicit opt-in and recipient validation.

## Security and limits
- Never put passwords, API keys, or bearer tokens in source files or chat. Use Script Properties.
- Use least-privilege OAuth scopes; authorize only the Google services used by enabled workflows.
- Keep automation in MANUAL mode until each workflow has been tested.
- Google Apps Script has execution, trigger, email-recipient and daily service quotas. Free availability and quotas depend on the Google account and can change.
- Sheets is not a replacement for database backups. Keep periodic SQL exports.
- Do not expose private student, customer, or fitness data in public links.
