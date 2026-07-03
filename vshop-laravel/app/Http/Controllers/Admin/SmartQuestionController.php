<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartQuestion;
use Illuminate\Http\Request;

class SmartQuestionController extends Controller
{
    public function index()
    {
        $questions = SmartQuestion::latest()->get();
        return view('admin.smart.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.smart.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required'
        ]);

        SmartQuestion::create($request->all());

        return redirect()->route('admin.smart.index')->with('success','Created');
    }

    public function edit($id)
    {
        $q = SmartQuestion::findOrFail($id);
        return view('admin.smart.edit', compact('q'));
    }

    public function update(Request $request, $id)
    {
        $q = SmartQuestion::findOrFail($id);
        $q->update($request->all());

        return redirect()->route('admin.smart.index')->with('success','Updated');
    }

    public function destroy($id)
    {
        SmartQuestion::destroy($id);
        return back()->with('success','Deleted');
    }
}