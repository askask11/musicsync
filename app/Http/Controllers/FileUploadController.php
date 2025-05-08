<?php

namespace App\Http\Controllers;

use App\Services\OssService;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    protected OssService $oss;

    public function __construct(OssService $oss)
    {
        $this->oss = $oss;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:3072', // max 3MB
        ]);

        $file = $request->file('file');

        $path = $this->oss->upload($file);

        if ($path) {
            return response()->json([
                'success' => true,
                'file_id' => $path
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed. Check logs for details.'
            ], 500);
        }
    }
}
