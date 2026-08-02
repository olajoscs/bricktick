@extends('admin.layout')

@section('title', $project->name)

@section('content')
    <p class="meta" style="margin-bottom: 20px">
        <a href="{{ route('admin.projects.index') }}">&larr; Back to projects</a>
    </p>

    <div class="panel">
        <h2>Project details</h2>
        <p class="meta">
            Created {{ $project->created_at->format('Y-m-d H:i') }}
            · Updated {{ $project->updated_at->format('Y-m-d H:i') }}
        </p>

        <form method="post" action="{{ route('admin.projects.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $project->id }}">
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required maxlength="255">
                @error('name')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="actions">
                <button type="submit" class="btn">Save changes</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Delete project</h2>
        <form method="post" action="{{ route('admin.projects.destroy') }}" onsubmit="return confirm('Delete this project?')">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" value="{{ $project->id }}">
            <button type="submit" class="btn btn--danger">Delete project</button>
        </form>
    </div>
@endsection
