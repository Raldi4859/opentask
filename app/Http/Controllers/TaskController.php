<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\File;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::orderBy('id', 'desc')->get();
        return view('index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = [
            [
                'label' => 'Todo',
                'value' => 'Todo',
            ],
            [
                'label' => 'Done',
                'value' => 'Done',
            ]
        ];
        return view('create', compact('statuses'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,completed',
            'file' => 'required|file|mimes:pdf,doc,docx'
        ]);

        $task = new Task();
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status');
        $task->save();

        $file = $request->file('file');
        $fileName = $task->id . '.' . $file->extension();
        $file->storeAs('tasks', $fileName);

        $taskFile = new File();
        $taskFile->task_id = $task->id;
        $taskFile->file_name = $fileName;
        $taskFile->save();

        return redirect()->route('index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$task = Task::findOrFail($id);
        //return view('task.show', ['task' => $task]);
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return view('edit', ['task' => $task]);
    }
    
    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,completed',
            'file' => 'nullable|file|mimes:pdf,doc,docx'
        ]);

        $task = Task::findOrFail($id);
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status');
        $task->save();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $task->id . '.' . $file->extension();
            $file->storeAs('tasks', $fileName);

            $taskFile = $task->file;
            if (!$taskFile) {
                $taskFile = new File();
                $taskFile->task_id = $task->id;
            }
            $taskFile->file_name = $fileName;
            $taskFile->save();
        }    

        return redirect()->route('Index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect()->route('index')->with('success', 'Task deleted successfully.');
    }

     /**
      * Download the specified task file.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
    public function download($id)
    {
        $task = Task::findOrFail($id);
        $file = $task->file;
        if (!$file) {
            return redirect()->route('index')->with('error', 'File not found.');
        }
        return response()->download(storage_path('app/tasks/' . $file->file_name));
    }

}
    