<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.projects.index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Project::query()->create($validated);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project created.');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $project = Project::query()->findOrFail($validated['id']);

        return view('admin.projects.show', [
            'project' => $project,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $project = Project::query()->findOrFail($validated['id']);
        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.projects.show', ['id' => $project->id])
            ->with('success', 'Project updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        Project::query()->whereKey($validated['id'])->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project deleted.');
    }
}
