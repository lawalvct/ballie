<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json(['notifications' => []]);
    }

    public function markAsRead(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        return response()->json(['success' => true]);
    }
}
