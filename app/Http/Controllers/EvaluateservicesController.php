<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;

class EvaluateservicesController extends Controller
{
    
    public function showEvaluateServices(){
        return view('HR.evaluateservice');
    }

    public function showQuotationForm(){
        
        $services = Services::all();

        return view('HR.quotation', ["services" => $services ]);
    }
}
