<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;

class TaskApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($tasks);
    }

    public function show(Task $task): JsonResponse
    {
        // Ensure the user is the owner of the task
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'You are not the owner of this task.'], 403);
        }

        return response()->json($task);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $taskData = $request->validated();

        // Automatically associate the task with the authenticated user
        $taskData['user_id'] = $request->user()->id;

        try {
            $task = Task::create($taskData);

            return response()->json($task, 201);
        } catch (\Exception $e) {
            return $this->handleException($e, 'creating', new Task());
        }
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        // Ensure the user is the owner of the task
        if ($this->isOwner($request, $task) === false) {
            return response()->json(['error' => 'You are not the owner of this task.'], 403);
        }

        $taskData = $request->validated();

        try {
            $task->update($taskData);

            return response()->json($task);
        } catch (\Exception $e) {
            return $this->handleException($e, 'updating', $task);
        }
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        // Ensure the user is the owner of the task
        if ($this->isOwner($request, $task) === false) {
            return response()->json(['error' => 'You are not the owner of this task.'], 403);
        }

        try {
            $task->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return $this->handleException($e, 'deleting', $task);
        }
    }

    private function isOwner(Request $request, Task $task): bool
    {
        return $task->user_id === $request->user()->id;
    }

    private function handleException(\Exception $e, string $action, Task $task): JsonResponse
    {
        logger()->error("Error {$action} task: ", [
            'task_id' => $task->id ?? null,
            'user_id' => $task->user_id ?? null,
            'code' => $e->getCode(),
            'exception_class' => get_class($e),
        ]);

        return response()->json(['error' => "There was an error {$action} the task. Please try again."], 500);
    }
}
