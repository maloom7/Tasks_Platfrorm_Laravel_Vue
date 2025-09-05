<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use App\Notifications\TaskCommented;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Task $task)
    { $this->authorize('view',$task); return $task->comments()->with('user:id,name')->latest()->paginate(20); }

    public function store(Request $request, Task $task)
    {
        $this->authorize('update',$task);
        $data = $request->validate(['body'=>'required|string']);
        $comment = $task->comments()->create(['user_id'=>auth()->id(),'body'=>$data['body']]);
        // إشعار المعنيين
        if ($task->assignee) $task->assignee->notify(new \App\Notifications\TaskCommented($task, $comment));
        $task->creator->notify(new \App\Notifications\TaskCommented($task, $comment));
        return $comment->load('user:id,name');
    }
}