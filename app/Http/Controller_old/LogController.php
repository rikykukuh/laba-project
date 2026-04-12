<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function showOrderLogs()
    {
        // Path to the log file
        $path = storage_path('logs/order.log');

        // Check if the log file exists
        if (!file_exists($path)) {
            abort(404, 'Log file not found.');
        }

        // Read the log file contents
        $logContents = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Parse log lines into an array
        $logs = [];
        foreach ($logContents as $line) {
            // Extract JSON part from log entry
            preg_match('/\{.*\}/', $line, $matches);
            if (isset($matches[0])) {
                $logs[] = json_decode($matches[0], true);
            }
        }
        // dd($logs);

        return view('logs.order', ['logs' => $logs]);
    }
}
