<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())
                     ->orderBy('is_completed', 'asc')
                     ->orderBy('created_at', 'desc')
                     ->get();
        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'user_id' => Auth::id(),
            'task' => $request->task,
            'is_completed' => false,
        ]);

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $todo->update([
            'is_completed' => $request->is_completed,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $todo->delete();

        return response()->json(['success' => true]);
    }
}
