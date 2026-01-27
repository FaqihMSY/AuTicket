<?php

namespace App\Http\Controllers;

use App\Models\AssignmentType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssignmentTypeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $assignmentTypes = AssignmentType::with('department')->latest()->paginate(10);
        return view('assignment_types.index', compact('assignmentTypes'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('assignment_types.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:assignment_types,code',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        AssignmentType::create($validated);

        return redirect()->route('assignment-types.index')
            ->with('success', 'Assignment type created successfully.');
    }

    public function edit(AssignmentType $assignmentType)
    {
        $departments = Department::all();
        return view('assignment_types.edit', compact('assignmentType', 'departments'));
    }

    public function update(Request $request, AssignmentType $assignmentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:assignment_types,code,' . $assignmentType->id,
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $assignmentType->update($validated);

        return redirect()->route('assignment-types.index')
            ->with('success', 'Assignment type updated successfully.');
    }

    public function destroy(AssignmentType $assignmentType)
    {
        if ($assignmentType->projects()->exists()) {
            return back()->with('error', 'Cannot delete assignment type with existing projects.');
        }

        $assignmentType->delete();

        return redirect()->route('assignment-types.index')
            ->with('success', 'Assignment type deleted successfully.');
    }
}
