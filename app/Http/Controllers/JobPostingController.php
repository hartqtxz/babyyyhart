<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobPosting::with('user')
            ->where('status', 'active')
            ->latest();

        if ($limit = $request->query('limit')) {
            $query->limit((int) $limit);
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'workers_needed' => 'required|integer',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'location' => 'nullable|string',
            'job_type' => 'nullable|string',
            'experience_level' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        try {
            $job = JobPosting::create([
                ...$validated,
                'user_id' => auth()->id(),
                'status' => $validated['status'] ?? 'active',
            ]);

            return response()->json($job, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating job',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = JobPosting::with('user', 'applicants')
            ->where('status', 'active')
            ->find($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json($job, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = JobPosting::find($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'workers_needed' => 'nullable|integer',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'location' => 'nullable|string',
            'job_type' => 'nullable|string',
            'experience_level' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $job->update($validated);

        return response()->json($job, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = JobPosting::find($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully'], 200);
    }
}
