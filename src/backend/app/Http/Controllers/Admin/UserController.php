<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'has_admin' => ['nullable', 'boolean'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'] ?? null,
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active'),
            'has_admin' => $request->boolean('has_admin'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created.');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::query()->findOrFail($validated['id']);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($request->input('id')),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'has_admin' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->findOrFail($validated['id']);

        if ($user->id === $request->user()->id && ! $request->boolean('is_active')) {
            return redirect()
                ->route('admin.users.show', ['id' => $user->id])
                ->with('error', 'You cannot deactivate your own account.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'has_admin' => $request->boolean('has_admin'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.show', ['id' => $user->id])
            ->with('success', 'User updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::query()->findOrFail($validated['id']);

        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('admin.users.show', ['id' => $user->id])
                ->with('error', 'You cannot delete your own account.');
        }

        if ($user->has_admin) {
            return redirect()
                ->route('admin.users.show', ['id' => $user->id])
                ->with('error', 'Admin accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted.');
    }
}
