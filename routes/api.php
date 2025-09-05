<?php
use App\Http\Controllers\Api\{TaskController, CommentController, AttachmentController, ReportController, UserController};

Route::middleware(['auth:sanctum'])->group(function(){
    Route::apiResource('tasks', TaskController::class);
    Route::get('tasks/{task}/comments', [CommentController::class,'index']);
    Route::post('tasks/{task}/comments', [CommentController::class,'store']);

    Route::post('tasks/{task}/attachments', [AttachmentController::class,'store']);

    Route::get('reports/summary', [ReportController::class,'summary']);

    Route::apiResource('users', UserController::class)->only(['index','show','update']);
});
Route::get('me/notifications', function(){
    return auth()->user()->unreadNotifications()->limit(20)->get();
})->middleware('auth:sanctum');

Route::post('me/notifications/{id}/read', function($id){
    $n = auth()->user()->notifications()->findOrFail($id); $n->markAsRead(); return response()->noContent();
})->middleware('auth:sanctum');