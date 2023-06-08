<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class TasksController extends Controller
{
    /**
     * [GET] - Returns the list of all tasks
     *
     * @author Lucas Tanaka
     * @return Task[]
     */

    public function listAll()
    {
        # Get user from jwt token
        $user = JWTAuth::user();

        # Using Redis to store task list
        $redisKey = 'taskList-' . $user->id;
        $taskList = json_decode(Redis::get($redisKey));

        if ($taskList) {
            # If exist key in Redis
            return $taskList;
        }

        # Get all tasks for specific user
        $taskList = Task::where('user_id', $user->id)->get();

        # Set taskList key on Redis -  Expiration 2h
        Redis::set($redisKey, json_encode($taskList), 'EX', 7200);
        return $taskList;
    }

    /**
     * [GET] - : Returns the details of a specific task
     *
     * @param id task id
     * 
     * @throws Response status code 400 with error message
     * @author Lucas Tanaka
     * @return Task
     */

    public function taskDetail(string $id)
    {
        # Request Validaton
        try {
            request()->merge(['id' => request()->route('id')]);
            $this->validate(request(), [
                'id' => 'required|uuid|exists:tasks,id',
            ]);
        } catch (\Exception $e) {
            abort(400, "Invalid uuid or task does not exist");
        }

        # Get task by id
        return Task::where('id', $id)->first();
    }

    /**
     * [POST] - Creates a new task
     *
     * @param Request request with title, description, completed (optional) and file (optional)
     * 
     * @throws Response status code 400 with error message
     * @author Lucas Tanaka
     * @return Task
     */

    public function store()
    {
        try {
            # Request validation
            $this->validate(request(), [
                'title' => 'required|max:100',
                'description' => 'required|max:255',
                'completed' => 'nullable|boolean',
                'file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
            ]);
        } catch (\Exception $e) {
            # Abort with status code 400 with error message missing some information
            abort(400, $e->getMessage());
        }

        # Get user from jwt token
        $user = JWTAuth::user();

        $taskInformation = request()->only(['title', 'description']);
        $taskInformation['user_id'] = $user->id;

        # Threating file
        $file = request('file');
        if ($file) {
            $imageName = Uuid::uuid4() . '.'  . $file->extension();
            $file->move(public_path('files'), $imageName);
            $taskInformation['file_name'] = $imageName;
        }
        $completed = request('completed');
        if ($completed) {
            $taskInformation['completed'] = $completed;
        }

        # Create new Task
        try {
            $task = Task::create($taskInformation);
        } catch (\Exception $e) {
            abort(400, "Failed to create Task");
        }

        # Clear redis key taskList
        Redis::set('taskList-' .  $user->id, null);

        return $task;
    }

    /**
     * [PUT] - Updates the details of a specific task
     *
     * @param Request request with id, title, description, completed
     * 
     * @throws Response status code 400 with error message
     * @author Lucas Tanaka
     * @return Task
     */

    public function edit(string $id)
    {
        try {
            # Request validation
            request()->merge(['id' => request()->route('id')]);
            $this->validate(request(), [
                'id' => 'required|uuid|exists:tasks,id',
                'title' => 'required|max:100',
                'description' => 'required|max:255',
                'completed' => 'required|boolean',
            ]);
        } catch (\Exception $e) {
            # Abort with status code 400 with error message missing some information
            abort(400, $e->getMessage());
        }
        # Get user from jwt token
        $user = JWTAuth::user();

        $taskInformation = request()->only(['title', 'description', 'completed']);

        # Update Task
        Task::where('id', $id)->update($taskInformation);

        # Clear redis key taskList
        Redis::set('taskList-' . $user->id, null);

        return response("Task updated", 200);
    }

    /**
     * [DELETE] - Removes a specific task
     *
     * @param Request request with id
     * 
     * @throws Response status code 400 with error message
     * @author Lucas Tanaka
     * @return Response status code 200 "Task deleted"
     */

    public function delete(string $id)
    {
        try {
            request()->merge(['id' => request()->route('id')]);
            $this->validate(request(), [
                'id' => 'required|uuid|exists:tasks,id',
            ]);
        } catch (\Exception $e) {
            # Abort with status code 400 with error message missing some information
            abort(400, 'Invalid uuid or task does not exist');
        }

        # Get user from jwt token
        $user = JWTAuth::user();

        Task::where('id', $id)->delete();

        # Clear redis key taskList
        Redis::set('taskList-' . $user->id, null);

        return response("Task deleted", 200);
    }
}
