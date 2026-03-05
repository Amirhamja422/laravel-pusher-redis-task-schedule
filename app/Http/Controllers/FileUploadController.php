<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Jobs\ProcessFileUpload;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $file = $request->file('file');

        $path = $file->store('uploads', 'public');

        $record = File::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending'
        ]);

        // Dispatch Queue Job
        ProcessFileUpload::dispatch($record);

        return response()->json([
            'message' => 'File uploaded & processing started'
        ]);
    }
}
