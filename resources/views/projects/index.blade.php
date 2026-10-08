@extends('layouts.app')
@section('title','Projects & Portfolio | MRK Digital')
@section('description','Explore selected websites, web applications, automation and digital systems built by MRK Digital.')
@section('content')
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">PORTFOLIO</span>
        <h1>Projects & practical solutions.</h1>
        <p>Selected work across websites, web applications, automation and digital systems.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        @if($projects->isEmpty())
            <div class="empty project-empty">
                <h2>Portfolio is being prepared.</h2>
                <p>New MRK Digital projects will appear here as they are published.</p>
                <a class="button primary" href="{{ route('contact') }}">Start a Project</a>
            </div>
        @else
            <div class="project-grid">
                @foreach($projects as $project)
                    <article class="project-card">
                        <div class="project-top">
                            <span class="project-category">{{ $project->category }}</span>
                            @if($project->featured)<span class="project-featured">Featured</span>@endif
                        </div>
                        <h2>{{ $project->title }}</h2>
                        <p>{{ $project->summary }}</p>
                        @if($project->technologies)
                            <div class="tech-list">
                                @foreach(array_filter(array_map('trim', explode(',', $project->technologies))) as $tech)
                                    <span>{{ $tech }}</span>
                                @endforeach
                            </div>
                        @endif
                        <a class="text-link" href="{{ route('projects.show', $project->slug) }}">View project →</a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
