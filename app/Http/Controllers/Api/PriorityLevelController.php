<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriorityLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PriorityLevelController extends Controller
{
    public function index()
    {
        return response()->json([
            'priorityLevels' => PriorityLevel::all()
        ], Response::HTTP_OK);
    }
}
