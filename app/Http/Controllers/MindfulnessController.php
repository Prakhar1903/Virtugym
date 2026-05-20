<?php

namespace App\Http\Controllers;

use App\Models\MindfulnessContent;
use Illuminate\Http\Request;

class MindfulnessController extends Controller
{
    public function index(Request $request)
    {
        $query = MindfulnessContent::query();
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $contents = $query->get();
        $categories = collect(MindfulnessContent::raw()->distinct('category'))->filter()->values();
        
        return view('mindfulness.index', compact('contents', 'categories'));
    }

    public function show($id)
    {
        $content = MindfulnessContent::findOrFail($id);
        return view('mindfulness.show', compact('content'));
    }
}
