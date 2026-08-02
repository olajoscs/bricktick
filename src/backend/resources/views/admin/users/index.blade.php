@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="panel">
        <h2>Users</h2>
        <details class="collapsible" @if ($errors->hasAny(['name', 'email', 'password', 'title', 'is_active'])) open @endif>
            <summary class="btn btn--secondary">New user</summary>
            <form method="post" action="{{ route('admin.users.store') }}" class="collapsible__body">
                @csrf
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                    @error('name')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                    @error('email')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="255">
                    @error('title')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    @error('password')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field field--checkbox">
                    <label for="is_active">
                        <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                        Active
                    </label>
                    @error('is_active')
                        <p class="meta" style="color: var(--danger)">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Create user</button>
            </form>
        </details>
        @if ($users->isEmpty())
            <p class="meta">No users yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Title</th>
                        <th>Active</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="meta">{{ $user->title ?? '—' }}</td>
                            <td class="meta">{{ $user->is_active ? 'Yes' : 'No' }}</td>
                            <td class="meta">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="meta">{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', ['id' => $user->id]) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
