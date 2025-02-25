<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id());
        
        // Apply status filter (optional)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply sorting (optional)
        if ($request->has('sort_by') && $request->has('order')) {
            $query->orderBy($request->sort_by, $request->order);
        }
        
        // Pagination (10 per page)
        $tasks = $query->paginate(10);
        
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:1,2,3', // Asegurar que sea un número
            'due_date' => 'required|date',
        ]);
        

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? '2',
            'due_date' => $request->due_date,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);
        
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }
        
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);
        
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if status has changed (for notification purposes)
        $oldStatus = $task->status;
        
        $task->update($request->all());
        
        // If you implement email notification (optional)
        if ($oldStatus !== $task->status) {
            // Here you would call a notification class
            // \Notification::send($task->user, new TaskStatusChanged($task));
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);
        
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }
        
        $task->delete();
        
        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}

class TaskStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Task Status Changed')
                ->line('The status of your task "' . $this->task->title . '" has changed.')
                ->line('New status: ' . $this->task->status)
                ->action('View Task', url('/tasks/' . $this->task->id))
                ->line('Thank you for using our application!');
    }
}
