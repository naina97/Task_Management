<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendTaskCompletedEmail;

class TaskController extends Controller
{
    public function index()
    {
        $authUcser = Auth::user();
        $user_id  = $authUcser->id;
        $task = Task::with('user')->where('user_id',$user_id)->get();
        return response(['data' => $task, 'status' => 200, 'message' => 'Task List']);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        try {
            
            DB::beginTransaction();
            $task = new Task;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->user_id = $request->user_id;
            $task->save();
            DB::commit();
            return response(['data' => $task,'status' => 200, 'message' => 'Task successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 422, 'message' => $e]);
        }
    }

    public function update(Request $request,$task_id)
    {
        // {{dd($request->all());}};
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        $task = Task::find($task_id);
        try {
            
            DB::beginTransaction();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->is_completed = $request->is_completed ?? $task->is_completed;
            $task->save();
            DB::commit();
            if($task->is_completed == 1)
            {
                // SendTaskCompletedEmail::dispatch($task);
                dispatch(new SendTaskCompletedEmail($task));
            }
            return response(['data' => $task,'status' => 200, 'message' => 'Task Updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 401, 'message' => 'Task not found']);
        }
    }

    public function delete(Request $request,$task_id)
    {
        $task = Task::find($task_id);
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
