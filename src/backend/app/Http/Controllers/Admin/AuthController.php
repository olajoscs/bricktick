<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->has_admin) {
            return redirect()->route('admin.projects.index');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()
                ->route('admin.login')
                ->withInput($request->only('email'))
                ->with('error', 'Invalid credentials.');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withInput($request->only('email'))
                ->with('error', 'This account has been deactivated.');
        }

        if (! $user->has_admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withInput($request->only('email'))
                ->with('error', 'You do not have admin access.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.projects.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
