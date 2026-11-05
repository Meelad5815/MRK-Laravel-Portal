/**
 * MRK Fitness -> Google Sheets backup bridge.
 *
 * Configure Script Properties (Project Settings > Script properties):
 * SPREADSHEET_ID       Google spreadsheet ID
 * LARAVEL_FEED_URL     https://YOUR-DOMAIN/fitness/backup-feed
 * FITNESS_SYNC_TOKEN   Same random secret as Laravel FITNESS_BACKUP_TOKEN
 *
 * Deploy as Web app: Execute as Me; access Anyone.
 * The endpoint is protected by the shared secret in the JSON request body.
 */

function doPost(e) {
  try {
    const body = JSON.parse((e && e.postData && e.postData.contents) || '{}');
    const expected = PropertiesService.getScriptProperties().getProperty('FITNESS_SYNC_TOKEN');
    if (!expected || !body.token || !constantTimeEquals_(String(expected), String(body.token))) {
      return json_({ ok: false, error: 'Unauthorized' });
    }
    if (body.action !== 'sync_now') {
      return json_({ ok: false, error: 'Unsupported action' });
    }

    const result = syncFitnessData_(false);
    return json_({ ok: true, count: result.count, synced_at: result.syncedAt });
  } catch (err) {
    console.error(err);
    return json_({ ok: false, error: String(err && err.message ? err.message : err) });
  }
}

/** Run once manually from the Apps Script editor after setting Script Properties. */
function setupDailyBackup() {
  const props = PropertiesService.getScriptProperties();
  ['SPREADSHEET_ID', 'LARAVEL_FEED_URL', 'FITNESS_SYNC_TOKEN'].forEach(function (key) {
    if (!props.getProperty(key)) throw new Error('Missing Script Property: ' + key);
  });

  // Avoid creating duplicate daily triggers.
  ScriptApp.getProjectTriggers().forEach(function (trigger) {
    if (trigger.getHandlerFunction() === 'dailyFitnessBackup') {
      ScriptApp.deleteTrigger(trigger);
    }
  });

  ScriptApp.newTrigger('dailyFitnessBackup')
    .timeBased()
    .everyDays(1)
    .atHour(2)
    .create();

  // Verify configuration and create the first live sheet snapshot now.
  syncFitnessData_(true);
}

/** Daily trigger: fetch a fresh copy from Laravel and create a dated snapshot tab. */
function dailyFitnessBackup() {
  syncFitnessData_(true);
}

/** Optional manual test from the Apps Script editor. */
function testFitnessConnection() {
  const result = syncFitnessData_(false);
  console.log(JSON.stringify(result));
}

function syncFitnessData_(makeDailySnapshot) {
  const props = PropertiesService.getScriptProperties();
  const spreadsheetId = props.getProperty('SPREADSHEET_ID');
  const feedUrl = props.getProperty('LARAVEL_FEED_URL');
  const token = props.getProperty('FITNESS_SYNC_TOKEN');
  if (!spreadsheetId || !feedUrl || !token) throw new Error('Set all required Script Properties first.');

  const response = UrlFetchApp.fetch(feedUrl, {
    method: 'get',
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
    muteHttpExceptions: true
  });
  const status = response.getResponseCode();
  if (status !== 200) throw new Error('Laravel backup feed returned HTTP ' + status);

  const payload = JSON.parse(response.getContentText());
  if (payload.schema_version !== 1 || !Array.isArray(payload.entries)) {
    throw new Error('Unexpected Laravel backup feed format.');
  }

  const ss = SpreadsheetApp.openById(spreadsheetId);
  const headers = ['ID', 'Type', 'Date', 'Workout', 'Distance (km)', 'Duration (minutes)', 'Weight (kg)', 'Notes', 'Created At', 'Updated At'];
  const rows = payload.entries.map(function (entry) {
    return [
      Number(entry.id) || '',
      safeCell_(entry.type),
      safeCell_(entry.entry_date),
      safeCell_(entry.title),
      entry.distance_km === null ? '' : Number(entry.distance_km),
      entry.duration_minutes === null ? '' : Number(entry.duration_minutes),
      entry.weight_kg === null ? '' : Number(entry.weight_kg),
      safeCell_(entry.notes),
      safeCell_(entry.created_at),
      safeCell_(entry.updated_at)
    ];
  });

  const sheet = getOrCreateSheet_(ss, 'Fitness Entries');
  sheet.clearContents();
  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  if (rows.length) sheet.getRange(2, 1, rows.length, headers.length).setValues(rows);
  sheet.setFrozenRows(1);
  sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold');
  sheet.autoResizeColumns(1, headers.length);

  const syncedAt = new Date();
  const history = getOrCreateSheet_(ss, 'Backup History');
  if (history.getLastRow() === 0) {
    history.appendRow(['Backup Time', 'Records', 'Source', 'Result']);
    history.setFrozenRows(1);
  }
  history.appendRow([syncedAt, rows.length, 'Laravel Fitness', 'Success']);

  if (makeDailySnapshot) {
    const tz = Session.getScriptTimeZone() || 'Asia/Karachi';
    const tabName = 'Daily_' + Utilities.formatDate(syncedAt, tz, 'yyyyMMdd');
    const snapshot = getOrCreateSheet_(ss, tabName);
    snapshot.clearContents();
    snapshot.getRange(1, 1, 1, headers.length).setValues([headers]);
    if (rows.length) snapshot.getRange(2, 1, rows.length, headers.length).setValues(rows);
    snapshot.setFrozenRows(1);
    snapshot.getRange(1, 1, 1, headers.length).setFontWeight('bold');
  }

  return { count: rows.length, syncedAt: syncedAt.toISOString() };
}

function getOrCreateSheet_(spreadsheet, name) {
  return spreadsheet.getSheetByName(name) || spreadsheet.insertSheet(name);
}

function safeCell_(value) {
  if (value === null || value === undefined) return '';
  const text = String(value);
  return /^[=+@\-]/.test(text) ? "'" + text : text;
}

function constantTimeEquals_(a, b) {
  if (a.length !== b.length) return false;
  let mismatch = 0;
  for (let i = 0; i < a.length; i++) mismatch |= a.charCodeAt(i) ^ b.charCodeAt(i);
  return mismatch === 0;
}

function json_(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}
