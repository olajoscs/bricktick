@extends('admin.layout')

@section('title', $user->name)

@section('content')
    <p class="meta" style="margin-bottom: 20px">
        <a href="{{ route('admin.users.index') }}">&larr; Back to users</a>
    </p>

    <div class="panel">
        <h2>User details</h2>
        <p class="meta">
            Created {{ $user->created_at->format('Y-m-d H:i') }}<br>
            Updated {{ $user->updated_at->format('Y-m-d H:i') }}
        </p>

        <form method="post" action="{{ route('admin.users.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $user->id }}">
            <div class="field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255">
                @error('name')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255">
                @error('email')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $user->title) }}" maxlength="255">
                @error('title')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" placeholder="Leave blank to keep current password">
                @error('password')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
            </div>
            <div class="field field--checkbox">
                @if ($user->id === auth()->id())
                    <input type="hidden" name="is_active" value="1">
                @endif
                <label for="is_active">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', $user->is_active))
                        @disabled($user->id === auth()->id())
                    >
                    Active
                </label>
                @if ($user->id === auth()->id())
                    <p class="meta">Your own account must stay active.</p>
                @endif
                @error('is_active')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror
                
                <label for="has_admin">
                    <input
                        type="checkbox"
                        id="has_admin"
                        name="has_admin"
                        value="1"
                        @checked(old('has_admin', $user->has_admin))
                        @disabled($user->id === auth()->id())
                    >
                    Admin
                </label>                
                @if ($user->id === auth()->id())
                    <p class="meta">Your own account must stay an admin.</p>
                @endif
                @error('has_admin')
                    <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                @enderror

            </div>
            <div class="actions">
                <button type="submit" class="btn">Save changes</button>
            </div>
        </form>
    </div>

    @if (! $user->has_admin && $user->id !== auth()->id())
        <div class="panel">
            <h2>Delete user</h2>
            <form method="post" action="{{ route('admin.users.destroy') }}" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $user->id }}">
                <button type="submit" class="btn btn--danger">Delete user</button>
            </form>
        </div>
    @endif
@endsection
