<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    //

    public function all($jobTaskId)
    {
        $messages = Message::where('job_task_id', '=', intval($jobTaskId))->get();
        return $messages;
    }

    public function add(Request $request)
    {
        $message = new Message();
        $message->job_task_id = intval($request->jobTaskId);
        $message->user_id = intval($request->userId);
        $message->username = $request->username;
        $message->message = $request->message;
        $message->for_customer = $request->forCustomer;

        try {
            $message->save();
            $messages = Message::where('job_task_id', '=', $request->jobTaskId)->get();
            return $messages;
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "",
                "error" => [$e->getMessage()]], 200);
        }
    }

    public function delete(Request $request)
    {
        $message = Message::find(intval($request->messageId));
        $message->delete();
        $messages = Message::where('job_task_id', '=', intval($request->jobTaskId))->get();
        return $messages;
    }

    public function update(Request $request)
    {
        $message = Message::find(intval($request->updatedMessage['id']));
        $message->message = $request->updatedMessage['message'];
        $message->save();

        $messages = Message::where('job_task_id', '=', intval($request->jobTaskId))->get();
        return $messages;
    }

}
