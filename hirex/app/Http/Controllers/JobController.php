<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Category;
use App\Models\JobType;
use App\Models\JobStatus;
use App\Models\Employer;
use App\Models\Comment;
use App\Models\PdfDocument; // Import the PdfDocument model
use Illuminate\Http\Request;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf; // Import the PDF-to-text package
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // Get the authenticated user ID

        // Check if the user ID exists
        if (!$userId || !User::find($userId)) {
            abort(404); // Redirect to 404 if user ID is not found
        }

        $jobs = Job::with('jobType')
            ->withCount('applications')
            ->whereHas('status', function ($query) {
                $query->where('name', 'accepted');
            })
            ->paginate(10);
        $categories = Category::all();

        return view('jobs.alljobs', compact('jobs', 'categories'));
    }

    public function create()
    {
        $types = JobType::all();
        $statuses = JobStatus::all();
        $categories = Category::all();

        return view('jobs.createjob', compact('categories', 'types', 'statuses'));
    }

    public function store(StoreJobRequest $request)
    {
        $user = Auth::user();
        if ($user->role != 2) {
            return redirect()->route('home')->with('error', 'Access Denied. Only employers can post jobs.');
        }

        $emp_id = Employer::where('user_id', $user->id)->value('id');

        if (!$emp_id) {
            return redirect()->route('home')->with('error', 'Employer profile not found. Please complete your employer profile.');
        }

        $validatedData = $request->validated();

        // Save the job details
        $job = new Job();
        $job->title = $validatedData['title'];
        $job->description = $validatedData['description'];
        $job->requirements = $validatedData['requirements'];
        $job->location = $validatedData['location'];
        $job->category_id = $validatedData['category_id'];
        $job->job_status = $validatedData['job_status'];
        $job->job_type = $validatedData['job_type'];
        $job->responsibilities = $validatedData['responsibilities'];
        $job->salary = $validatedData['salary'];
        $job->benefits = $validatedData['benefits'];
        $job->deadline = $validatedData['deadline'];
        $job->emp_id = $emp_id;
        $job->save();

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdfs', 'public');

            // Extract text from the PDF
            $pdfText = Pdf::getText(Storage::disk('public')->path($pdfPath));

            // Save the PDF and extracted text
            PdfDocument::create([
                'title' => $validatedData['title'],
                'content' => Str::limit($pdfText, 60000), // Limiting to prevent exceeding MySQL TEXT column size
                'job_id' => $job->id,
            ]);
        }

        return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
    }

    public function show($id)
    {
        $job = Job::with('employer', 'jobType', 'status', 'comments.user')->findOrFail($id);

        return view('jobs.jobdetails', compact('job'));
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);

        // Ensure the user is an employer
        if (Auth::user()->role != 2) {
            return redirect()->route('home')->with('error', 'Access Denied. Only employers can edit jobs.');
        }

        $categories = Category::all();
        $statuses = JobStatus::all();
        $jobTypes = JobType::all();

        return view('jobs.edit', compact('job', 'categories', 'statuses', 'jobTypes'));
    }

    public function update(UpdateJobRequest $request, $id)
    {
        $job = Job::findOrFail($id);

        // Validate and update job data
        $job->update($request->validated());

        return redirect()->route('jobs.myjobs', Auth::id())->with('success', 'Job updated successfully.');
    }

    public function search(Request $request)
    {
        $query = Job::query();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('keyword')) {
            $query->where(function($subQuery) use ($request) {
                $subQuery->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('description', 'like', '%' . $request->keyword . '%')
                    ->orWhereHas('pdfDocuments', function($q) use ($request) {
                        $q->where('content', 'like', '%' . $request->keyword . '%');
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $jobs = $query->with('jobType', 'status', 'category')->paginate(10);
        $categories = Category::all();

        return view('jobs.search', compact('jobs', 'categories'));
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        // Ensure the user is an employer
        if (Auth::user()->role != 2) {
            return redirect()->route('home')->with('error', 'Access Denied. Only employers can delete jobs.');
        }

        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function showAnalytics($id)
    {
        $job = Job::with('applications')->findOrFail($id);
        $applicationCount = $job->applications->count();

        return view('employers.job.analytics', compact('job', 'applicationCount'));
    }

    public function showEmployerJobs()
    {
        $user = auth()->user();

        // Ensure the user is an employer
        if ($user->role != 2) {
            return redirect()->route('home')->with('error', 'Access Denied. Only employers can view their job postings.');
        }

        $employer = Employer::where('user_id', $user->id)->first();

        if (!$employer) {
            return redirect()->route('home')->with('error', 'Employer profile not found.');
        }

        $jobs = Job::where('emp_id', $employer->id)->get();

        return view('jobs.myjobs', compact('jobs'));
    }

    public function showByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $jobs = Job::where('category_id', $categoryId)->get();

        return view('jobs.jobbycategory', compact('jobs', 'category'));
    }
}
