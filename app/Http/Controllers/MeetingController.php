<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::orderBy('meeting_date', 'desc')->get();
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
        $input = $request->all();
        if (isset($input['duration_minutes']) && is_string($input['duration_minutes'])) {
            $input['duration_minutes'] = (int) preg_replace('/\D/', '', $input['duration_minutes']);
        }
        
        if (isset($input['meeting_date'])) {
            try {
                if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $input['meeting_date'])) {
                    $input['meeting_date'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', trim($input['meeting_date']))->format('Y-m-d H:i:s');
                } else {
                    $input['meeting_date'] = \Carbon\Carbon::parse(trim($input['meeting_date']))->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                // Keep original if parsing fails
            }
        }
        
        $request->replace($input);

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
            'sheet_link' => 'nullable|string',
        ]);

        $meeting = Meeting::create($data);
        return response()->json($meeting, 201);
    }
}
