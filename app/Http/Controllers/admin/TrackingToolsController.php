<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingTool;

class TrackingToolsController extends Controller
{
    public function trackingtool()
    {
        $tools = TrackingTool::all();
        return view('admin.settings.tracking‑tools', compact('tools'));
    }

  

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        TrackingTool::create([
            'title' => $request->title,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.settings.tracking‑tools.index')->with('success', 'Tracking tool added successfully.');
    }

    public function edit($id)
    {
        $tool = TrackingTool::findOrFail($id);
        return view('admin.settings.tracking‑tools.edit', compact('tool'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $tool = TrackingTool::findOrFail($id);
        $tool->update([
            'title' => $request->title,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.tracking‑tools.index')->with('success', 'Tracking tool updated successfully.');
    }

    public function destroy($id)
    {
        $tool = TrackingTool::findOrFail($id);
        $tool->delete();

        return redirect()->route('admin.settings.tracking‑tools.index')->with('success', 'Tracking tool removed.');
    }
}
