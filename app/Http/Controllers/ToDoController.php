<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateToDoRecordRequest;
use App\Models\ToDo;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Todo::query();

        if ($request->has('show_all') && $request->show_all == 'true') {
            $todos = $query->get();
            return response()->json($todos);
        } else {
            $todos = $query->where('completed', false)->get();
            return view('index', compact('todos'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateToDoRecordRequest $request)
    {
        return ToDo::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($id) {
            $toDoRecord = Todo::find($id);
            $toDoRecord->completed = true;
            $toDoRecord->save();
            return true;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($id) {
            $toDoRecord = ToDo::find($id);
            if ($toDoRecord) {
                $toDoRecord->delete();
                return true;
            }
        }
        return false;
    }
}
