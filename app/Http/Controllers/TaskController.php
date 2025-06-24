<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Task::query();  // shows that now querry can take place on table:Task

        // Apply filters (if present)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Only get the authenticated user's tasks
        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved successfully.',
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? 'medium',
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function delete(Request $request, $id)
    {

        $task = Task::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task with the required id is not found!',

            ], 404);
        }

        $this->authorize('delete', $task); // ///POLICEy called here

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task Deleted succesfully',
            'data' => $task,
        ]);

    }

    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task with the required id is not found!',

            ], 404);
        }

        $validated = $request->validate(
            [
                'title' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pending,completed',
                'priority' => 'nullable|in:low,medium,high',
                'due_date' => 'nullable|date',
            ]
        );

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task added succesfully',
            'data' => $task,
        ]);

    }
}
