<?php

// namespace App\Http\Controllers;

// use App\Models\File;
// use Illuminate\Http\Request;
// use ProcessFileUpload;

// class FileUploadController extends Controller
// {
//     public function upload(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|file|mimes:csv,txt|max:10240'
//         ]);

//         $file = $request->file('file');

//         $path = $file->store('imports','public');

//         $record = File::create([
//             'file_name'=>$file->getClientOriginalName(),
//             'file_path'=>$path,
//             'status'=>'pending'
//         ]);

//         ProcessFileUpload::dispatch($record);

//         return response()->json([
//             'message'=>'File uploaded & processing started'
//         ]);
//     }
// }





// for excell


namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use ProcessFileUpload;

class FileUploadController extends Controller
{

    public function upload(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);

        $path = $request->file('file')
                ->store('uploads','public');

        $file = File::create([
            'file_path'=>$path,
            'status'=>'pending'
        ]);

        ProcessFileUpload::dispatch($file);

        return back()->with('success','Import Started');

    }

}

