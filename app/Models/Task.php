<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
protected $fillable = [
'title','description','status','priority','creator_id','assignee_id','due_date','progress'
];


protected $casts = [
'due_date' => 'date',
];


public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
public function assignees() { return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps(); }
public function comments() { return $this->hasMany(Comment::class); }
public function attachments() { return $this->hasMany(TaskAttachment::class); }
}
