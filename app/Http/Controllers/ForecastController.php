<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function index()
    {
        $output = shell_exec("python3 analytics/forecast.py");
        $predictions = json_decode($output, true);

        return view('forecast', compact('predictions'));
    }
}
