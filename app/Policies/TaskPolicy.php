<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
public function view(User $user, Task $task): bool
{
return $user->role === User::ROLE_MANAGER
|| $task->creator_id === $user->id
|| $task->assignee_id === $user->id
|| $task->assignees()->where('user_id',$user->id)->exists();
}


public function create(User $user): bool
{ return in_array($user->role, [User::ROLE_MANAGER]); }


public function update(User $user, Task $task): bool
{
if ($user->role === User::ROLE_MANAGER) return true;
// الموظف يمكنه تحديث حالته/تعليقه فقط إن كان معنيًا
return $task->assignee_id === $user->id
|| $task->assignees()->where('user_id',$user->id)->exists()
|| $task->creator_id === $user->id;
}


public function delete(User $user, Task $task): bool
{ return $user->role === User::ROLE_MANAGER; }
}
