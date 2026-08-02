@extends('admin.layout')

@section('title', 'Sign in')

@section('header')
    <header class="admin-header">
        <h1>Bricktick Admin</h1>
    </header>
@endsection

@section('content')
    <div class="panel" style="max-width: 400px; margin: 0 auto">
        <h2>Sign in</h2>
        <form method="post" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Sign in</button>
        </form>
    </div>
@endsection
