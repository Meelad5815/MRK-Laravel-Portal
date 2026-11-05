/**
 * MRK Google Automation Hub
 * Add this as a NEW file named Hub.gs in the existing Apps Script project.
 * It intentionally does not replace the Fitness backup Code.gs.
 *
 * Script Properties:
 *   SPREADSHEET_ID       Existing MRK Fitness/Hub spreadsheet ID
 * Optional:
 *   MRK_REPORT_EMAIL     Recipient for report drafts (never auto-sends)
 */

function onOpen() {
  SpreadsheetApp.getUi()
    .createMenu('MRK Automation')
    .addItem('Initialize Hub tabs', 'initializeMrkHub')
    .addItem('Check Google services', 'checkMrkGoogleServices')
    .addSeparator()
    .addItem('Create email report draft', 'createMrkReportDraft')
    .addToUi();
}

function initializeMrkHub() {
  const ss = getMrkHubSpreadsheet_();
  const config = getOrCreateMrkSheet_(ss, 'MRK Hub Config');
  if (config.getLastRow() === 0) {
    config.getRange(1, 1, 1, 3).setValues([['Setting', 'Value', 'Description']]);
    config.getRange(2, 1, 4, 3).setValues([
      ['Timezone', 'Asia/Karachi', 'Project timezone; also set in Apps Script Project Settings'],
      ['Report recipient', '', 'Optional email address; reports are created as drafts only'],
      ['Automation mode', 'MANUAL', 'Keep MANUAL until each workflow is tested'],
      ['Last service check', '', 'Updated by Check Google services']
    ]);
    config.setFrozenRows(1);
    config.autoResizeColumns(1, 3);
  }

  const log = getOrCreateMrkSheet_(ss, 'MRK Automation Log');
  if (log.getLastRow() === 0) {
    log.appendRow(['Timestamp', 'Workflow', 'Status', 'Details']);
    log.setFrozenRows(1);
    log.autoResizeColumns(1, 4);
  }

  logMrkEvent_('Hub initialization', 'SUCCESS', 'Hub tabs are ready.');
  SpreadsheetApp.getUi().alert('MRK Automation Hub تیار ہے۔ اگلا مرحلہ Google services کی اجازت چیک کرنا ہے۔');
}

function checkMrkGoogleServices() {
  const checks = [];
  checks.push(checkMrkService_('Google Sheets', function () {
    return getMrkHubSpreadsheet_().getName();
  }));
  checks.push(checkMrkService_('Google Drive', function () {
    return DriveApp.getRootFolder().getName();
  }));
  checks.push(checkMrkService_('Gmail', function () {
    GmailApp.getAliases();
    return 'Gmail service accessible; no email sent';
  }));
  checks.push(checkMrkService_('Google Calendar', function () {
    return CalendarApp.getDefaultCalendar().getName();
  }));
  checks.push(checkMrkService_('Google Docs', function () {
    return DocumentApp.getActiveDocument() ? 'Bound document available' : 'Service permission checked; standalone project has no active document';
  }));
  checks.push(checkMrkService_('Google Forms', function () {
    return FormsApp.getActiveForm() ? 'Bound form available' : 'Service permission checked; standalone project has no active form';
  }));

  const ss = getMrkHubSpreadsheet_();
  const sheet = getOrCreateMrkSheet_(ss, 'MRK Service Status');
  sheet.clearContents();
  sheet.getRange(1, 1, 1, 3).setValues([['Service', 'Status', 'Details']]);
  const rows = checks.map(function (x) { return [x.service, x.ok ? 'AVAILABLE' : 'NEEDS ATTENTION', x.details]; });
  if (rows.length) sheet.getRange(2, 1, rows.length, 3).setValues(rows);
  sheet.setFrozenRows(1);
  sheet.autoResizeColumns(1, 3);
  logMrkEvent_('Google service check', 'COMPLETED', checks.filter(function (x) { return x.ok; }).length + '/' + checks.length + ' services accessible.');
  SpreadsheetApp.getUi().alert('Service check مکمل۔ تفصیل MRK Service Status شیٹ میں موجود ہے۔');
}

function createMrkReportDraft() {
  const props = PropertiesService.getScriptProperties();
  const recipient = props.getProperty('MRK_REPORT_EMAIL');
  if (!recipient) throw new Error('پہلے Script Properties میں MRK_REPORT_EMAIL سیٹ کریں۔');

  const ss = getMrkHubSpreadsheet_();
  const log = ss.getSheetByName('MRK Automation Log');
  const lines = [
    'MRK Automation Hub Report',
    'Generated: ' + new Date().toISOString(),
    '',
    'یہ ایک خودکار رپورٹ ڈرافٹ ہے۔ اسے بھیجنے سے پہلے خود جائزہ لیں۔',
    '',
    'Spreadsheet: ' + ss.getUrl(),
    'Automation log rows: ' + (log ? Math.max(0, log.getLastRow() - 1) : 0)
  ];
  GmailApp.createDraft(recipient, 'MRK Automation Hub - Status Report', lines.join('\n'));
  logMrkEvent_('Email report draft', 'SUCCESS', 'Draft created for configured recipient; no email sent.');
  SpreadsheetApp.getUi().alert('Gmail میں رپورٹ Draft بن گیا ہے۔ یہ خودکار طور پر Send نہیں ہوا۔');
}

function checkMrkService_(service, callback) {
  try {
    return { service: service, ok: true, details: String(callback()) };
  } catch (err) {
    return { service: service, ok: false, details: String(err && err.message ? err.message : err) };
  }
}

function getMrkHubSpreadsheet_() {
  const id = PropertiesService.getScriptProperties().getProperty('SPREADSHEET_ID');
  if (!id) throw new Error('Script Properties میں SPREADSHEET_ID موجود نہیں۔');
  return SpreadsheetApp.openById(id);
}

function getOrCreateMrkSheet_(ss, name) {
  return ss.getSheetByName(name) || ss.insertSheet(name);
}

function logMrkEvent_(workflow, status, details) {
  const ss = getMrkHubSpreadsheet_();
  const sheet = getOrCreateMrkSheet_(ss, 'MRK Automation Log');
  if (sheet.getLastRow() === 0) sheet.appendRow(['Timestamp', 'Workflow', 'Status', 'Details']);
  sheet.appendRow([new Date(), workflow, status, details]);
}
