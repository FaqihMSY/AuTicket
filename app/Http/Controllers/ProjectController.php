<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AssignmentType;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Project::query();
        $user = auth()->user();

        // 1. Authorization Filter (Show only relevant projects)
        if (!$user->isAdmin()) {
            if ($user->canManageProjects()) {
                // Manager/Pengawas: See projects in their department
                $query->where('department_id', $user->department_id);
            } elseif ($user->isAuditor()) {
                // Auditor Visibility Logic:
                // 1. Assigned Projects (Any status)
                // 2. Created by me (Drafts)
                // 3. Published in my department (Open for taking)
                if ($user->auditor) {
                    $query->where(function ($q) use ($user) {
                        // 1. Assigned
                        $q->whereHas('auditors', function ($subQ) use ($user) {
                            $subQ->where('auditors.id', $user->auditor->id);
                        })
                            // 2. Created by Me
                            ->orWhere('created_by', $user->id)
                            ->orWhere('status', 'PUBLISHED');
                    });
                } else {
                    abort(403, 'User is staff but has no auditor profile.');
                }
            }
        }

        // 2. Status Filter (from Dashboard Click)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Overdue Filter
        if ($request->get('filter') === 'overdue') {
            $query->overdue();
        }

        // 4. Sorting (Default: Latest created) -- Will update to Deadline later per request #12
        // 4. Sorting
        if ($request->get('sort') === 'deadline') {
            $projects = $query->with(['department', 'assignmentType', 'creator'])
                ->orderBy('end_date', 'asc')
                ->paginate(10)
                ->withQueryString();
        } else {
            $projects = $query->with(['department', 'assignmentType', 'creator'])
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return view('projects.index', compact('projects'));
    }

    public function export(Request $request)
    {
        // Reuse index logic for filtering
        $query = Project::with(['department', 'assignmentType', 'auditors.user', 'creator']);

        if (auth()->user()->isAuditor()) {
            $auditorId = auth()->user()->auditor->id;
            $query->whereHas('auditors', function ($q) use ($auditorId) {
                $q->where('auditor_id', $auditorId);
            });
        }

        // Export ALL projects (ignore filters)
        $projects = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Title',
                'Assignment Type',
                'Department',
                'Status',
                'Priority',
                'Planned Start',
                'Actual Start',
                'Planned End',
                'Actual End',
                'Published By',
                'Auditor Emails'
            ]);

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->title,
                    $project->assignmentType->name ?? '-',
                    $project->department->name ?? '-',
                    $project->status,
                    $project->priority,
                    Carbon::parse($project->start_date)->format('Y-m-d'),
                    $project->started_at ? Carbon::parse($project->started_at)->format('Y-m-d H:i') : '-',
                    Carbon::parse($project->end_date)->format('Y-m-d'),
                    $project->closed_at ? Carbon::parse($project->closed_at)->format('Y-m-d H:i') : '-',
                    $project->publisher->name ?? '-',
                    $project->auditors->pluck('user.email')->join(','),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        $file = $request->file('csv_file');

        DB::beginTransaction();
        try {
            $reader = SimpleExcelReader::create($file->getPathname(), $file->getClientOriginalExtension());
            $rows = $reader->getRows();

            $index = 0;
            foreach ($rows as $row) {
                $index++;
                $rowNum = $index + 1; // Assuming header is row 1

                // Extract data
                $title = $row['Title'] ?? null;
                $assignmentTypeName = $row['Assignment Type'] ?? null;
                $departmentName = $row['Department'] ?? null;
                $status = $row['Status'] ?? 'DRAFT';
                $priority = $row['Priority'] ?? 'MEDIUM';
                $plannedStart = $row['Planned Start'] ?? null;
                $actualStart = $row['Actual Start'] ?? null;
                $plannedEnd = $row['Planned End'] ?? null;
                $actualEnd = $row['Actual End'] ?? null;
                $auditorEmailsStr = $row['Auditor Emails'] ?? '';

                if (empty($title)) {
                    continue; // Skip empty rows
                }

                // Lookups
                $assignmentType = AssignmentType::where('name', $assignmentTypeName)->first();
                if (!$assignmentType) {
                    throw new \Exception("Row {$rowNum}: Assignment Type '{$assignmentTypeName}' not found.");
                }

                $department = Department::where('name', $departmentName)->first();
                $deptId = $department ? $department->id : auth()->user()->department_id;

                // Auditors
                $auditorEmails = array_map('trim', explode(',', $auditorEmailsStr));
                $auditorIds = User::whereIn('email', $auditorEmails)
                    ->whereHas('auditor')
                    ->with('auditor')
                    ->get()
                    ->pluck('auditor.id')
                    ->toArray();

                // Validation for auditors? Maybe only if provided.

                // Parse Dates
                try {
                    $startDate = $plannedStart ? Carbon::parse($plannedStart) : now();
                    $endDate = $plannedEnd ? Carbon::parse($plannedEnd) : now()->addDays(7);
                    $startedAt = ($actualStart && $actualStart !== '-') ? Carbon::parse($actualStart) : null;
                    $closedAt = ($actualEnd && $actualEnd !== '-') ? Carbon::parse($actualEnd) : null;
                } catch (\Exception $e) {
                    throw new \Exception("Row {$rowNum}: Invalid date format.");
                }

                // Create Project
                $project = Project::create([
                    'title' => $title,
                    'assignment_type_id' => $assignmentType->id,
                    'department_id' => $deptId,
                    'status' => $status,
                    'priority' => $priority,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'started_at' => $startedAt,
                    'closed_at' => $closedAt,
                    'created_by' => auth()->id(),
                ]);

                if (!empty($auditorIds)) {
                    $project->auditors()->sync($auditorIds);
                }
            }

            DB::commit();
            return back()->with('success', 'Projects imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('create', Project::class);

        $assignmentTypes = AssignmentType::all();
        $departments = Department::all();

        return view('projects.create', compact('assignmentTypes', 'departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assignment_type_id' => 'required|exists:assignment_types,id',
            'department_id' => 'required|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'auditor_ids' => 'required|array|min:1',
            'auditor_ids.*' => 'exists:auditors,id',
            'instruction_files.*' => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
        ]);

        // Logic Status: FORCE DRAFT for everyone initially
        // (Managers can Publish later, Auditors must wait for Manager)
        $status = 'DRAFT';

        $project = Project::create([
            'department_id' => $validated['department_id'],
            'assignment_type_id' => $validated['assignment_type_id'],
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'priority' => $validated['priority'],
            'status' => $status,
        ]);

        $project->auditors()->attach($validated['auditor_ids']);

        if ($request->hasFile('instruction_files')) {
            foreach ($request->file('instruction_files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('attachments/instructions', $filename);

                ProjectAttachment::create([
                    'project_id' => $project->id,
                    'uploaded_by' => auth()->id(),
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'category' => 'INSTRUCTION',
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        ActivityLog::log('project_created', $project->id, "Created project: {$project->title}");

        $message = auth()->user()->canManageProjects()
            ? 'Project created successfully.'
            : 'Project draft submitted. Please wait for Manager approval.';

        return redirect()->route('projects.show', $project)
            ->with('success', $message);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'department',
            'assignmentType',
            'auditors.user',
            'creator.department',
            'attachments.uploader',
            'reviews'
        ]);

        return view('projects.show', compact('project'));
    }

    public function publish(Project $project)
    {
        $this->authorize('publish', $project);

        $project->update([
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'published_by' => auth()->id(),
        ]);

        ActivityLog::log('project_published', $project->id, "Published project: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project published. Waiting for auditors to start.');
    }

    public function start(Project $project)
    {
        $this->authorize('start', $project);

        $project->update([
            'status' => 'ON_PROGRESS',
            'started_at' => now(),
        ]);

        ActivityLog::log('project_started', $project->id, "Started project: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project started successfully.');
    }

    public function markAsDone(Project $project)
    {
        $this->authorize('markAsDone', $project);

        $project->markAsDone();

        ActivityLog::log('project_submitted', $project->id, "Marked project as done: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project marked as done. Waiting for approval.');
    }

    public function cancelSubmission(Project $project)
    {
        $this->authorize('cancelSubmission', $project);

        $project->update([
            'status' => 'DRAFT',
            'submitted_at' => null,
        ]);

        ActivityLog::log('project_cancelled', $project->id, "Cancelled project submission: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project submission cancelled. Project is now in Draft.');
    }

    public function cancelReviewSubmission(Project $project)
    {
        $this->authorize('cancelReviewSubmission', $project);

        $project->update([
            'status' => 'ON_PROGRESS',
            'submitted_at' => null, // Clear submission date so it can be re-submitted
        ]);

        ActivityLog::log('review_submission_cancelled', $project->id, "Cancelled review submission: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Review submission cancelled. You can continue working on the project.');
    }

    public function uploadResult(Request $request, Project $project)
    {
        $this->authorize('uploadResult', $project);

        $request->validate([
            'result_files.*' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
        ]);

        foreach ($request->file('result_files') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('attachments/results', $filename);

            ProjectAttachment::create([
                'project_id' => $project->id,
                'uploaded_by' => auth()->id(),
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'category' => 'RESULT',
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        ActivityLog::log('result_uploaded', $project->id, "Uploaded result files");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Result files uploaded successfully');
    }

    public function downloadAttachment(ProjectAttachment $attachment)
    {
        $this->authorize('download', $attachment);

        return Storage::download($attachment->path, $attachment->original_filename);
    }

    public function destroyAttachment(ProjectAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        // Delete file from storage
        if (Storage::exists($attachment->path)) {
            Storage::delete($attachment->path);
        }

        // Delete record from DB
        $attachment->delete();

        ActivityLog::log('attachment_deleted', $attachment->project_id, "Deleted attachment: {$attachment->original_filename}");

        return back()->with('success', 'Attachment deleted successfully.');
    }
}
