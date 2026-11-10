@extends('layouts.app')
@section('title', 'Fitness Dashboard | MRK Digital')
@section('robots', 'noindex,nofollow')
@section('content')
<style>
.fitness-wrap{max-width:1120px;margin:auto;padding:2rem 1rem 4rem}.fitness-hero{padding:2.5rem;border-radius:24px;background:linear-gradient(120deg,#073b75,#087f8c);color:#fff;box-shadow:0 16px 40px #0b477322}.fitness-hero h1{font-size:clamp(2rem,5vw,3rem);margin:.3rem 0}.fitness-hero p{opacity:.9;max-width:650px}.fitness-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin:1.4rem 0}.fitness-stat,.fitness-panel{background:#fff;border:1px solid #e5edf5;border-radius:18px;padding:1.2rem;box-shadow:0 8px 24px #102a4310}.fitness-stat span{display:block;color:#60758a;font-size:.88rem}.fitness-stat strong{display:block;font-size:1.8rem;color:#123c65;margin-top:.25rem}.fitness-grid{display:grid;grid-template-columns:minmax(280px,.85fr) minmax(0,1.4fr);gap:1rem}.fitness-panel h2{margin:.1rem 0 1rem;color:#123c65}.fitness-form{display:grid;gap:.8rem}.fitness-form label{display:grid;gap:.35rem;font-weight:600;color:#334b60}.fitness-form input,.fitness-form select,.fitness-form textarea{width:100%;padding:.75rem;border:1px solid #d7e2ed;border-radius:10px;background:#fbfdff;font:inherit}.fitness-form button{border:0;border-radius:10px;padding:.85rem 1rem;background:#087f8c;color:white;font-weight:700;cursor:pointer}.fitness-table{width:100%;border-collapse:collapse}.fitness-table th,.fitness-table td{text-align:left;padding:.75rem .5rem;border-bottom:1px solid #edf1f5;vertical-align:top}.fitness-table th{color:#60758a;font-size:.83rem}.fitness-tag{display:inline-block;padding:.25rem .55rem;border-radius:99px;background:#e7f5f5;color:#08717b;font-size:.78rem;font-weight:700}.fitness-muted{color:#708397;font-size:.9rem}.fitness-empty{padding:2rem;text-align:center;color:#708397}@media(max-width:760px){.fitness-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.fitness-grid{grid-template-columns:1fr}.fitness-hero{padding:1.5rem}.fitness-table th:nth-child(4),.fitness-table td:nth-child(4){display:none}}
</style>
<div class="fitness-wrap">
<section class="fitness-hero"><span style="font-weight:700;letter-spacing:.12em;font-size:.78rem">MRK PERSONAL FITNESS</span><h1>Your progress, in one place.</h1><p>Log workouts, running sessions and body weight. Your records are private to your signed-in account and stored in the portal database.</p></section>
<div class="fitness-stats">
<div class="fitness-stat"><span>Run sessions</span><strong>{{ $stats['runs'] }}</strong></div>
<div class="fitness-stat"><span>Total distance</span><strong>{{ number_format((float)$stats['distance'],1) }} <small style="font-size:.8rem">km</small></strong></div>
<div class="fitness-stat"><span>Workouts logged</span><strong>{{ $stats['workouts'] }}</strong></div>
<div class="fitness-stat"><span>Latest weight</span><strong>{{ $stats['latest_weight'] ? number_format((float)$stats['latest_weight'],1).' kg' : '—' }}</strong></div>
</div>
<div class="fitness-grid">
<section class="fitness-panel"><h2>Add a record</h2>
<form class="fitness-form" method="POST" action="{{ route('fitness.store') }}">@csrf
<label>Record type<select name="type" id="fitness-type" required><option value="workout">Workout</option><option value="run">Running</option><option value="weight">Body weight</option></select></label>
<label>Date<input type="date" name="entry_date" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></label>
<div id="fitness-title"><label>Workout name<input name="title" maxlength="120" placeholder="e.g. Upper body strength"></label></div>
<div id="fitness-distance" hidden><label>Distance (km)<input type="number" name="distance_km" min=".01" max="9999.99" step=".01" placeholder="e.g. 3.5"></label></div>
<div id="fitness-weight" hidden><label>Weight (kg)<input type="number" name="weight_kg" min="1" max="500" step=".1" placeholder="e.g. 60"></label></div>
<label>Duration (minutes, optional)<input type="number" name="duration_minutes" min="1" max="1440" placeholder="e.g. 35"></label>
<label>Notes (optional)<textarea name="notes" rows="3" maxlength="2000" placeholder="How did it go?"></textarea></label>
<button type="submit">Save fitness record</button>
</form>
</section>
<section class="fitness-panel"><h2>Recent activity</h2>
@if($isAdmin)<p class="fitness-muted">Admin view: all users’ fitness records are visible only to the administrator.</p>@endif
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($entries->isEmpty())<div class="fitness-empty">No records yet. Add your first workout, run or weight entry.</div>
@else<div style="overflow-x:auto"><table class="fitness-table"><thead><tr>@if($isAdmin)<th>Account</th>@endif<th>Date</th><th>Type</th><th>Details</th><th>Duration</th><th>Notes</th><th>Manage</th></tr></thead><tbody>
@foreach($entries as $entry)<tr>
@if($isAdmin)<td>{{ $entry->owner_name }}</td>@endif
<td>{{ \Illuminate\Support\Carbon::parse($entry->entry_date)->format('d M Y') }}</td><td><span class="fitness-tag">{{ ucfirst($entry->type) }}</span></td><td>@if($entry->type === 'run'){{ number_format((float)$entry->distance_km,2) }} km @elseif($entry->type === 'weight'){{ number_format((float)$entry->weight_kg,1) }} kg @else{{ $entry->title }}@endif</td><td>{{ $entry->duration_minutes ? $entry->duration_minutes.' min' : '—' }}</td><td class="fitness-muted">{{ $entry->notes ?: '—' }}</td><td>
<details><summary>Edit</summary>
<form class="fitness-form" method="POST" action="{{ route('fitness.update', $entry->id) }}">@csrf @method('PUT')
<label>Type<select name="type"><option value="workout" @selected($entry->type==='workout')>Workout</option><option value="run" @selected($entry->type==='run')>Running</option><option value="weight" @selected($entry->type==='weight')>Weight</option></select></label>
<label>Date<input type="date" name="entry_date" value="{{ $entry->entry_date }}" max="{{ now()->toDateString() }}" required></label>
<label>Workout title<input name="title" maxlength="120" value="{{ $entry->title }}"></label>
<label>Distance (km)<input type="number" name="distance_km" min=".01" max="9999.99" step=".01" value="{{ $entry->distance_km }}"></label>
<label>Weight (kg)<input type="number" name="weight_kg" min="1" max="500" step=".1" value="{{ $entry->weight_kg }}"></label>
<label>Duration (minutes)<input type="number" name="duration_minutes" min="1" max="1440" value="{{ $entry->duration_minutes }}"></label>
<label>Notes<textarea name="notes" rows="2" maxlength="2000">{{ $entry->notes }}</textarea></label><button type="submit">Update</button></form>
<form method="POST" action="{{ route('fitness.destroy', $entry->id) }}" onsubmit="return confirm('Delete this fitness record?')">@csrf @method('DELETE')<button type="submit">Delete</button></form>
</details></td></tr>@endforeach
</tbody></table></div>@endif
</section>
</div>
<section class="fitness-panel" style="margin-top:1rem">
<h2>Google Sheets Backup</h2>
<p class="fitness-muted">MySQL remains your primary database. Sync a separate copy to your private Google Sheet, or let the daily Apps Script backup run automatically after setup.</p>
@if($errors->has('backup'))<p role="alert" style="color:#b42318">{{ $errors->first('backup') }}</p>@endif
@if(session('success'))<p role="status" style="color:#08717b">{{ session('success') }}</p>@endif
<form method="POST" action="{{ route('fitness.backup.sync') }}">@csrf<button type="submit">Sync Google Sheets Backup</button></form>
</section>
</div>
<script>
(() => {
 const type=document.getElementById('fitness-type');
 const title=document.getElementById('fitness-title');
 const distance=document.getElementById('fitness-distance');
 const weight=document.getElementById('fitness-weight');
 const titleInput=title.querySelector('input');
 const distanceInput=distance.querySelector('input');
 const weightInput=weight.querySelector('input');
 function update(){
  const v=type.value;
  title.hidden=v!=='workout'; distance.hidden=v!=='run'; weight.hidden=v!=='weight';
  titleInput.required=v==='workout'; distanceInput.required=v==='run'; weightInput.required=v==='weight';
 }
 type.addEventListener('change',update); update();
})();
</script>
@endsection
