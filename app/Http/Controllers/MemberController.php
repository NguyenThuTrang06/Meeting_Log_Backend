<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'team' => 'nullable|string|max:255',
        ]);
        $member = Member::create($request->all());
        return response()->json($member, 201);
    }

    public function show(string $id)
    {
        $member = Member::findOrFail($id);
        return response()->json($member);
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'team' => 'nullable|string|max:255',
        ]);
        $member->update($request->all());
        return response()->json($member);
    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
        return response()->json(null, 204);
    }
}
