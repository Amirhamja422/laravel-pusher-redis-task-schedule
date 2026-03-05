<?php

namespace App\Http\Controllers;

use App\Models\ImportFile;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    //
        public function uploadImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'run_at' => 'required|date'
        ]);

        $path = $request->file('file')
            ->store('imports','public');

        ImportFile::create([
            'file_path' => $path,
            'run_at' => $request->run_at
        ]);

        return "File uploaded & scheduled successfully";
    }
}
