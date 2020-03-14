<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\UploadCsvRequest;

class UploadController extends Controller
{
    public function employee(UploadCsvRequest $request)
    {
        // ON HOLD FOR NOW

        if ($request->hasFile('data')) {

            $file = $request->file('data');

            if (!$reader = fopen($file->path(), 'r')) {
                $this->sendErrorResponse('Can not open the file!');
            }

            $header = fgetcsv($reader);

            return [
                'message'   => implode(',', $header),
            ];

            while ($line = fgetcsv($reader)) {
                # code...
            }

        }

        return [
            'message' => 'da man',
        ];
    }

    private function sendErrorResponse($message)
    {
        return response()->json([
            'message'   => $message
        ], 422);
    }
}
