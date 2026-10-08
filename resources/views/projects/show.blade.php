@extends('layouts.app')
@section('title', $project->title.' | MRK Digital')
@section('description', $project->summary)
@section('content')
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">{{ $project->category }}</span>
        <h1>{{ $project->title }}</h1>
        <p>{{ $project->summary }}</p>
        <div class="actions">
            @if($project->project_url)
                <a class="button primary" href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">Open Project</a>
            @endif
            <a class="button secondary" href="{{ route('contact') }}">Discuss a Similar Project</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="container narrow project-detail">
        <div class="project-meta">
            <div><span>Category</span><strong>{{ $project->category }}</strong></div>
            @if($project->completed_at)<div><span>Completed</span><strong>{{ $project->completed_at->format('M Y') }}</strong></div>@endif
            @if($project->technologies)<div><span>Technology</span><strong>{{ $project->technologies }}</strong></div>@endif
        </div>
        <article class="project-content">
            {!! nl2br(e($project->description)) !!}
        </article>
    </div>
</section>
@endsection
