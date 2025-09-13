<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function image(Request $request)
    {
        // TODO: Implement image upload functionality
        return response()->json(['message' => 'Image upload endpoint - not implemented yet']);
    }

    public function document(Request $request)
    {
        // TODO: Implement document upload functionality
        return response()->json(['message' => 'Document upload endpoint - not implemented yet']);
    }

    public function destroy($file)
    {
        // TODO: Implement file deletion functionality
        return response()->json(['message' => 'File deletion endpoint - not implemented yet']);
    }
}
