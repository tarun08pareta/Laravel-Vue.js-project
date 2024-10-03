<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConditionController extends Controller
{
    public function index()
    {
        try {
            $conditions = Condition::with('products')->get();
            return response()->json([
                'status' => 200,
                'conditions' => $conditions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
