<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $user = Auth::user();
        abort_unless($user->role === User::ROLE_MANAGER, 403);
        return User::select('id','name','email','role','department')->orderBy('name')->get();
    }

    public function show(User $user)
    {
        $me = Auth::user();
        abort_unless($me->role === User::ROLE_MANAGER || $me->id === $user->id, 403);
        return $user->only(['id','name','email','role','department','created_at']);
    }

    public function update(Request $request, User $user)
    {
        $me = Auth::user();
        abort_unless($me->role === User::ROLE_MANAGER, 403);
        $data = $request->validate([
            'role' => 'sometimes|in:manager,employee',
            'department' => 'nullable|string|max:100',
            'name' => 'sometimes|string|max:255',
        ]);
        $user->update($data);
        return $user->only(['id','name','email','role','department']);
    }
}
