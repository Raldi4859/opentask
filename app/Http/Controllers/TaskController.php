<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\File;
use Illuminate\Support\Carbon;
use App\Notifications\TaskDue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->get();
        return view('index', ['tasks' => $tasks]);

        foreach ($tasks as $task) {
            $due_date = $task->due_date;
            $now = now();
            // check if the task due date less than 2 days
            if($due_date->diffInDays($now) < 2){
                // notify the task owner about the task
                $task->user->notify(new TaskDue($task));
            }
        }
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = [
            ['label' => 'Todo',
             'value' => 'Todo'],
            ['label' => 'Done',
            'value' => 'Done']
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
    // Validate input
    //$request->validate([
        //'name' => 'required',
        //'description' => 'nullable',
        //'due_date' => 'required|date',
        //'status' => 'required|in:Todo,Done',
        //'file' => 'nullable|file|mimes:pdf,doc,docx|max:1024'
    //]);


    $task = new Task();
    $task->name = $request->input('name');
    $task->description = $request->input('description');
    $task->due_date = $request->input('due_date');
    $task->status = $request->input('status');
    $task->user_id = auth()->id();
    $task->save();

    // Store file if input is present
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = $request->file('file')->getClientOriginalName();
        $file->move('files/', $filename);
        $task->files()->create([
            'path' => $filename,
        ]);
        $task->filename = $filename;
        $task->save();
    }

    // Send notification if due date is less than two days away
    $user = auth()->user();
    if ($task->due_date <= Carbon::now()->addDays(2)) {
        Notification::send($user, new TaskDue($task));
    }

    return redirect()->route('index');
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
        //if ($task->user_id !== auth()->id()) {
            //abort(403, 'Unauthorized action.');
        //}
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
        $statuses = [
            ['label' => 'Todo',
             'value' => 'Todo'],
            ['label' => 'Done',
            'value' => 'Done']
        ];
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');

        }
        return view('edit', compact('statuses', 'task'));
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
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        //$request->validate([
            //'name' => 'required',
            //'description' => 'required',
            //'due_date' => 'required|date',
            //'status' => 'required|in:Todo,Done',
            //'file' => 'nullable|file|mimes:pdf,doc,docx'
        //]);

        if($request->hasFile('file')){
            // Delete the old file
            Storage::delete($task->filename);

            // Get the new file
            $file = $request->file('file');
            // Get file name with extension
            $filenameWithExt = $file->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get extension
            $extension = $file->getClientOriginalExtension();
            // File name to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload File
            $path = $file->storeAs('public/files', $fileNameToStore);
            // update the filename
            $task->filename = $fileNameToStore;
        }

        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status');
        $task->save();


        return redirect()->route('index');
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
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $task->delete();
        return redirect()->route('index');
    }

    /**
     * Download the specified task file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function done($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $task->status = 'Done';
        $task->save();

        return redirect()->route('index');
    }
}

