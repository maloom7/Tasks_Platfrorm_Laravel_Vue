<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('update',$task);
        $request->validate(['file'=>'required|file|max:10240']);
        $file = $request->file('file');
        $path = $file->store('attachments','public');
        $att = $task->attachments()->create([
            'path'=>$path,
            'original_name'=>$file->getClientOriginalName(),
            'mime_type'=>$file->getClientMimeType(),
            'size'=>$file->getSize()
        ]);
        return $att;
    }
}