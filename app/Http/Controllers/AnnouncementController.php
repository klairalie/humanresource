<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // Display list page (with create & edit modals)
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('HR.announcement', compact('announcements'));
    }

    // Store new announcement
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Announcement::create([
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Announcement created successfully!');
    }

    // Update existing announcement
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::findOrFail($id);

        $announcement->update([
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Announcement updated successfully!');
    }

    // Delete announcement
    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();

        return back()->with('success', 'Announcement deleted successfully!');
    }
}
