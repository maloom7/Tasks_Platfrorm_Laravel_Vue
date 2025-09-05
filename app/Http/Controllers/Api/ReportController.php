<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $from = $request->date('from', now()->startOfMonth());
        $to = $request->date('to', now());
        $base = Task::whereBetween('created_at', [$from, $to]);

        $totals = [
            'total' => (clone $base)->count(),
            'done' => (clone $base)->where('status','done')->count(),
            'in_progress' => (clone $base)->where('status','in_progress')->count(),
            'blocked' => (clone $base)->where('status','blocked')->count(),
            'new' => (clone $base)->where('status','new')->count(),
        ];

        $byUser = Task::select('assignee_id', DB::raw('count(*) as c'))
            ->whereBetween('created_at', [$from,$to])
            ->groupBy('assignee_id')->with('assignee:id,name')->get();

        return compact('totals','byUser');
    }
}
