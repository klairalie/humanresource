<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentToken;


class AssessmentController extends Controller
{
    public function create()
    {
        return view('Assessments.createassessment');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|in:Helper,Assistant Technician,Technician,Human Resource Manager,Administrative Manager,Finance Manager',
            'title'         => 'required|string',
            'description' => 'nullable|string',
        ]);


        Assessment::create([
            'position_name' => $request->position_name,
            'title' => $request->title,
            'description' => $request->description,
        ]);


        return redirect()->back()->with('success', 'Assessment added successfully!');
    }

    

}
