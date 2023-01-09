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
    //$user = auth()->user();
    //if ($task->due_date <= Carbon::now()->addDays(2)) {
        //$user->notify(new TaskDue($task));
    //}

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

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|in:Todo,Done',
            'file' => 'nullable|file|mimes:pdf,doc,docx'
        ]);

        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status');
        $task->save();

        if ($request->hasFile('file')) {
            if ($task->file) {
                unlink(public_path('files/') . $task->file->path);
                $task->file()->delete();
            }
            $file = $request->file('file');
            $filename = $request->file('file')->getClientOriginalName();
            $file->move('files/', $filename);
            $task->files()->create([
                'path' => $filename,
            ]);
            $task->filename = $filename;
            $task->save();
            }

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
    public function download($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $file = $task->file;
        if (!$file) {
            abort(404);
        }
        return Storage::download("tasks/{$file->file_name}");
    }

    public function sendNotification()
    {
        $tasks = Task::dueSoon()->get();
        Notification::send($tasks->user, new TaskDue());
    }
}

