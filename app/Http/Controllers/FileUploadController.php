<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\ImportSummary;
use Illuminate\Http\Request;
use App\Jobs\ProcessFileUpload;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ExcelException;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        $file = $request->file('file');
        $path = $file->store('imports', 'public');

        // Step 1: Create File record
        $record = File::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending'
        ]);

        // Step 2: Count total rows
        $filePath = storage_path('app/public/' . $path);
        $totalRows = 0;

        try {
            $reader = Excel::toArray([], $filePath);
            $sheet = $reader[0]; // first sheet
            $totalRows = count($sheet) - 1; // exclude header
        } catch (ExcelException $e) {
            return response()->json(['message' => 'Invalid Excel file'], 422);
        }

        // Step 3: Create ImportSummary
        ImportSummary::create([
            'upload_history_id' => $record->id,
            'total_rows' => $totalRows
        ]);

        // Step 4: Dispatch queue job
        ProcessFileUpload::dispatch($record);

        return response()->json([
            'message' => 'Excel uploaded & processing started'
        ]);
    }
}

// namespace App\Http\Controllers;

// use App\Models\File;
// use App\Models\ImportSummary;
// use Illuminate\Http\Request;
// use App\Jobs\ProcessFileUpload;

// class FileUploadController extends Controller
// {
//     public function upload(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|file|max:10240'
//         ]);

//         $file = $request->file('file');
//         $path = $file->store('uploads', 'public');

//         // Step 1: Create file record
//         $record = File::create([
//             'file_name' => $file->getClientOriginalName(),
//             'file_path' => $path,
//             'status' => 'pending'
//         ]);

//         // Step 2: Count total rows for ImportSummary
//         $filePath = storage_path('app/public/' . $path);
//         $totalRows = 0;
//         if (($handle = fopen($filePath, 'r')) !== false) {
//             fgetcsv($handle); // skip header
//             while (fgetcsv($handle) !== false) {
//                 $totalRows++;
//             }
//             fclose($handle);
//         }

//         // Step 3: Create ImportSummary
//         ImportSummary::create([
//             'upload_history_id' => $record->id,
//             'total_rows' => $totalRows
//         ]);

//         // Step 4: Dispatch job to process file in queue
//         ProcessFileUpload::dispatch($record);

//         return response()->json([
//             'message' => 'File uploaded & processing started'
//         ]);
//     }
// }
