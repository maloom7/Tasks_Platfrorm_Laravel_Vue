<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $q = Task::query()->with(['creator:id,name','assignee:id,name']);

        // فلاتر اختيارية
        if ($status = $request->get('status')) $q->where('status', $status);
        if ($priority = $request->get('priority')) $q->where('priority', $priority);
        if ($assignee = $request->get('assignee_id')) $q->where('assignee_id', $assignee);
        if ($search = $request->get('search')) $q->where('title','like',"%$search%");

        // صلاحيات الرؤية
        if (auth()->user()->role !== User::ROLE_MANAGER) {
            $userId = auth()->id();
            $q->where(function($s) use ($userId){
                $s->where('creator_id',$userId)
                  ->orWhere('assignee_id',$userId)
                  ->orWhereExists(function($sq) use ($userId){
                      $sq->select(DB::raw(1))
                         ->from('task_assignees as ta')
                         ->whereColumn('ta.task_id','tasks.id')
                         ->where('ta.user_id',$userId);
                  });
            });
        }

        return $q->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);
        $data['creator_id'] = auth()->id();
        $task = Task::create($data);

        // تعيينات إضافية
        if ($request->filled('assignees')) {
            $ids = collect($request->assignees)->filter()->unique();
            foreach ($ids as $uid) {
                DB::table('task_assignees')->updateOrInsert(
                    ['task_id'=>$task->id,'user_id'=>$uid],
                    ['assigned_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]
                );
            }
        }

        // إشعار المكلّف
        if ($task->assignee_id) {
            $task->assignee->notify(new \App\Notifications\TaskAssigned($task));
        }

        return response()->json($task->load('assignee','creator'), 201);
    }

    public function show(Task $task)
    { $this->authorize('view', $task); return $task->load(['creator','assignee','comments.user','attachments']); }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:new,in_progress,blocked,done,archived',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100'
        ]);
        $task->update($data);

        if ($task->wasChanged('status')) {
            // إشعار صاحب العلاقة بتغيير الحالة
            if ($task->assignee) $task->assignee->notify(new \App\Notifications\TaskStatusChanged($task));
            $task->creator->notify(new \App\Notifications\TaskStatusChanged($task));
        }

        return $task->fresh()->load('assignee','creator');
    }

    public function destroy(Task $task)
    { $this->authorize('delete', $task); $task->delete(); return response()->noContent(); }
}