<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('meeting_id')) {
            $query->where('meeting_id', $request->meeting_id);
        }
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'name' => 'required|string|max:255',
            'assignee' => 'nullable|string|max:255',
            'manday' => 'nullable|numeric',
            'deadline' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $task = Task::create($data);
        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
