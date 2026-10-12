<?php

namespace App\Http\Controllers;

class TaskController extends Controller
{
    public function index()
    {
        return inertia('Tasks/Index');
    }

    public function create()
    {
        return inertia('Tasks/Create');
    }

    public function edit($taskId)
    {
        return inertia('Tasks/Edit', ['taskId' => $taskId]);
    }
}
