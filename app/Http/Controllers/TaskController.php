<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index', compact('tasks'));
    }

    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        Task::create($validated);

        return redirect()->route('tasks.index');
    }

    public function update(TaskRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました');
    }
}
