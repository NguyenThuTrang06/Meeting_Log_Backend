<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::orderBy('meeting_date', 'desc')->paginate(20);
        return response()->json($meetings);
    }

    public function show($id)
    {
        $meeting = Meeting::findOrFail($id);
        return response()->json($meeting);
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->update($request->all());
        return response()->json($meeting);
    }

    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function webhook(Request $request)
    {
        $data = $request->validate([
            'week' => 'nullable|string',
            'meeting_date' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'project_id' => 'nullable|string',
            'team' => 'nullable|string',
            'leader' => 'nullable|string',
            'name' => 'required|string',
            'duration_minutes' => 'nullable|integer',
            'video_link' => 'nullable|string',
            'short_summary' => 'nullable|string',
            'overview' => 'nullable|string',
            'action_items' => 'nullable|string',
            'decisions' => 'nullable|string',
            'issues' => 'nullable|string',
            'next_steps' => 'nullable|string',
        ]);

        $meeting = Meeting::create($data);
        return response()->json($meeting, 201);
    }
}
