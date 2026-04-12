<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        return response()->json(Job::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'workers' => 'required|integer|min:1',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'location' => 'nullable|string',
            'job_type' => 'required|string',
            'experience_level' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $job = Job::create([
            'user_id' => auth()->id() ?? 1,
            ...$validated
        ]);

        return response()->json($job, 201);
    }

    public function show(Job $job)
    {
        return response()->json($job, 200);
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'string',
            'description' => 'nullable|string',
            'workers' => 'integer|min:1',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'location' => 'nullable|string',
            'job_type' => 'string',
            'experience_level' => 'string',
            'status' => 'in:active,inactive',
        ]);

        $job->update($validated);

        return response()->json($job, 200);
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return response()->json(['message' => 'Job deleted'], 200);
    }
}
