<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $departments = Department::with('chair')->orderBy('code')->paginate(20);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $potentialChairs = User::whereIn('role', ['teacher', 'admin'])
            ->orderBy('name')
            ->get();

        return view('admin.departments.create', compact('potentialChairs'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:255'],
            'approval_pin' => ['nullable', 'string', 'max:50'],
            'chair_id' => ['nullable', 'exists:users,id'],
        ]);

        Department::create($data);

        return redirect()->route('admin.departments.index')->with('status', 'Department created.');
    }

    public function edit(Department $department)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $potentialChairs = User::whereIn('role', ['teacher', 'admin'])
            ->orderBy('name')
            ->get();

        return view('admin.departments.edit', compact('department', 'potentialChairs'));
    }

    public function update(Request $request, Department $department)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:departments,code,' . $department->id],
            'name' => ['required', 'string', 'max:255'],
            'approval_pin' => ['nullable', 'string', 'max:50'],
            'chair_id' => ['nullable', 'exists:users,id'],
        ]);

        $department->update($data);

        return redirect()->route('admin.departments.index')->with('status', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $department->delete();

        return redirect()->route('admin.departments.index')->with('status', 'Department deleted.');
    }
}


