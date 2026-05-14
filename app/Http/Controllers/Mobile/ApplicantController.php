<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;

class ApplicantController extends Controller
{
    // GET ALL APPLICATIONS OF LOGGED-IN USER
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $applications = Applicant::with('jobPosting')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($applications);
    }

    // APPLY FOR A JOB
    public function store(Request $request)
    {
        $request->validate([
            'job_posting_id' => 'required',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $applicant = Applicant::create([
            'user_id' => $user->id,
            'job_posting_id' => $request->job_posting_id,
            'status' => 'pending',
            'phone' => $request->phone,
            'cover_letter' => $request->cover_letter,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $applicant
        ]);
    }

    // GET SINGLE APPLICATION
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $application = Applicant::with('jobPosting')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($application);
    }
}