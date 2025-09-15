<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Resume_format;


class ResumeController extends Controller
{
    public function showResumeForm()
    {
        $resume = Resume_format::latest('resume_format_id')->first();
        return view('HR.resumeupload', compact('resume'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:doc,docx|max:2048',
        ]);

        $file = $request->file('resume');
        $binaryData = file_get_contents($file->getRealPath());

        // Always keep only one active file
        Resume_format::truncate();

        Resume_format::create([
            'file_name' => $file->getClientOriginalName(),
            'file_data' => $binaryData,
        ]);

        return back()->with('success', 'New resume format uploaded!');
    }

    public function delete($resume_format_id)
    {
        $resume = Resume_format::findOrFail($resume_format_id);
        $resume->delete();

        return back()->with('success', 'Resume format deleted.');
    }

    public function details($resume_format_id)
    {
        $resume = Resume_format::findOrFail($resume_format_id);
        return view('HR.resumedetails', compact('resume'));
    }

    /**
     * Download the latest resume format.
     */
    public function download()
    {
        $resume = DB::table('resume_formats')->latest('resume_format_id')->first();

        if (!$resume) {
            abort(404, 'No resume format found.');
        }

        return response($resume->file_data)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="' . $resume->file_name . '"');
    }
}
