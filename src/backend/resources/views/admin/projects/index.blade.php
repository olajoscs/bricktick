@extends('admin.layout')

@section('title', 'Projects')

@section('content')
    <div class="panel">
        <h2>Projects</h2>
        <details class="collapsible" @if ($errors->hasAny(['name', 'description'])) open @endif>
            <summary class="btn btn--secondary">New project</summary>
            <form method="post" action="{{ route('admin.projects.store') }}" class="collapsible__body">
                @csrf
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                    @error('name')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Create project</button>
            </form>
        </details>
        @if ($projects->isEmpty())
            <p class="meta">No projects yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td class="meta">{{ $project->created_at->format('Y-m-d H:i') }}</td>
                            <td class="meta">{{ $project->updated_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.projects.show', ['id' => $project->id]) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
