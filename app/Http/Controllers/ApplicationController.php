<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(Application::with(['user', 'job'])->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'job_id' => 'required|exists:jobs,id',
            'status' => 'required|in:pending,approved,rejected',
            'cover_letter' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $application = Application::create($validated);

        return response()->json($application->load(['user', 'job']), 201);
    }

    public function show(Application $application)
    {
        return response()->json($application->load(['user', 'job']), 200);
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'in:pending,approved,rejected',
            'cover_letter' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $application->update($validated);

        return response()->json($application->load(['user', 'job']), 200);
    }

    public function destroy(Application $application)
    {
        $application->delete();
        return response()->json(['message' => 'Application deleted'], 200);
    }
}
