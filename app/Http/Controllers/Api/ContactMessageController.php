<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Notifications\owner\NewContactMessageNotification;
use App\Events\NewContactMessageEvent;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'phone' => $request->phone,
            'ip_address' => $request->ip(),
            'status' => 'unread'
        ]);

        // Get all owners and send notification
        $owners = User::role('owner')->get();

        foreach ($owners as $owner) {
            $owner->notify(new NewContactMessageNotification($message));
        }
        // After sending notifications to owners
        NewContactMessageEvent::dispatch($message);

        return response()->json([
            'message' => 'Contact message submitted successfully',
            'data' => $message
        ], 201);
    }
}
