<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Notification;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Applicant::with('user', 'jobPosting')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'cover_letter' => 'nullable|string',
            'phone' => 'nullable|string',
            'resume_link' => 'nullable|string',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Check if user already applied for this job
        $existingApplication = Applicant::where('user_id', auth()->id())
            ->where('job_posting_id', $validated['job_posting_id'])
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job'
            ], 409);
        }

        // Handle file upload
        $resumePath = null;
        if ($request->hasFile('resume_file')) {
            $file = $request->file('resume_file');
            $resumePath = $file->store('resumes', 'public');
        }

        $applicantData = [
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'Pending',
        ];

        // Remove resume_file from array since it's handled above
        unset($applicantData['resume_file']);

        // Add resume_path if file was uploaded
        if ($resumePath) {
            $applicantData['resume_path'] = $resumePath;
        }

        $applicant = Applicant::create($applicantData);

        $applicant->load('user', 'jobPosting');

        // Notify the job poster that they received a new application
        $jobPosting = $applicant->jobPosting;
        $applicantUser = $applicant->user;
        
        if ($jobPosting) {
            \Log::info('Job posting found for notification', [
                'job_posting_id' => $jobPosting->id,
                'job_posting_user_id' => $jobPosting->user_id,
                'job_posting_title' => $jobPosting->title,
                'applicant_user_id' => $applicant->user_id,
            ]);

            if ($jobPosting->user_id) {
                try {
                    Notification::create([
                        'user_id' => $jobPosting->user_id,
                        'title' => 'New Application Received',
                        'type' => 'new_application',
                        'message' => "{$applicantUser->name} has applied for your job posting: {$jobPosting->title}",
                        'is_read' => false,
                    ]);
                    \Log::info('Notification created successfully', [
                        'user_id' => $jobPosting->user_id,
                        'title' => 'New Application Received',
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create notification', [
                        'error' => $e->getMessage(),
                        'user_id' => $jobPosting->user_id,
                    ]);
                }
            } else {
                \Log::warning('Job posting has no user_id', ['job_posting_id' => $jobPosting->id]);
            }
        } else {
            \Log::warning('Job posting not found for application', ['job_posting_id' => $validated['job_posting_id']]);
        }

        return response()->json($applicant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $applicant = Applicant::with('user', 'jobPosting')->find($id);
        
        if (!$applicant) {
            return response()->json(['message' => 'Applicant not found'], 404);
        }

        return response()->json($applicant, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $applicant = Applicant::with('user', 'jobPosting')->find($id);
        
        if (!$applicant) {
            return response()->json(['message' => 'Applicant not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $oldStatus = $applicant->status;
        $applicant->update($validated);

        // Create notification for the applicant if status changed
        if ($oldStatus !== $validated['status']) {
            $message = '';
            $title = '';
            if ($validated['status'] === 'Approved') {
                $title = 'Application Approved';
                $message = "Congratulations! Your application for {$applicant->jobPosting->title} has been approved!";
            } elseif ($validated['status'] === 'Rejected') {
                $title = 'Application Rejected';
                $message = "Your application for {$applicant->jobPosting->title} has been rejected. Better luck next time!";
            }

            if ($message) {
                Notification::create([
                    'user_id' => $applicant->user_id,
                    'title' => $title,
                    'type' => 'application_' . strtolower($validated['status']),
                    'message' => $message,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json($applicant, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $applicant = Applicant::find($id);
        
        if (!$applicant) {
            return response()->json(['message' => 'Applicant not found'], 404);
        }

        $applicant->delete();

        return response()->json(['message' => 'Applicant removed successfully'], 200);
    }

    /**
     * Get the current user's applications
     */
    public function myApplications()
    {
        $applications = Applicant::where('user_id', auth()->id())
            ->with('jobPosting', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($applications, 200);
    }
}
