<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Http\Requests\ContactMessageStoreRequest;
use App\Http\Requests\ContactMessageUpdateRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages with pagination, filters and sorting.
     */
    public function index(Request $request)
    {
        try {
            $query = ContactMessage::query();

            // Filtering by status
            if ($request->has('status') && in_array($request->status, array_keys(ContactMessage::getStatusOptions()))) {
                $query->where('status', $request->status);
            }

            // Search by name, email, subject, or message
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $allowedSortColumns = ['name', 'email', 'subject', 'status', 'created_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $messages = $query->paginate($perPage);

            // Statistics
            $statistics = [
                'total_messages' => ContactMessage::count(),
                'unread_messages' => ContactMessage::where('status', 'unread')->count(),
                'read_messages' => ContactMessage::where('status', 'read')->count(),
                'replied_messages' => ContactMessage::where('status', 'replied')->count(),
                'spam_messages' => ContactMessage::where('status', 'spam')->count(),
                'trashed_messages' => ContactMessage::onlyTrashed()->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Contact messages retrieved successfully.',
                'data' => ContactMessageResource::collection($messages),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'last_page' => $messages->lastPage(),
                ],
                'statistics' => $statistics,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact messages.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        try {
            $message = ContactMessage::withTrashed()->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Contact message retrieved successfully.',
                'data' => new ContactMessageResource($message),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Store a newly created contact message.
     */
    public function store(ContactMessageStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['ip_address'] = $request->ip();
            $data['status'] = $data['status'] ?? 'unread';

            DB::beginTransaction();
            $message = ContactMessage::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact message created successfully.',
                'data' => new ContactMessageResource($message),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact message.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified contact message.
     */
    public function update(ContactMessageUpdateRequest $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $data = $request->validated();

            DB::beginTransaction();
            $message->update($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact message updated successfully.',
                'data' => new ContactMessageResource($message),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact message.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Soft delete the specified contact message.
     */
    public function destroy($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact message deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact message.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get trashed contact messages.
     */
    public function trashed(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $messages = ContactMessage::onlyTrashed()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Trashed contact messages retrieved successfully.',
            'data' => ContactMessageResource::collection($messages),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'last_page' => $messages->lastPage(),
            ],
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Restore a trashed contact message.
     */
    public function restore($id)
    {
        try {
            $message = ContactMessage::onlyTrashed()->findOrFail($id);
            $message->restore();

            return response()->json([
                'success' => true,
                'message' => 'Contact message restored successfully.',
                'data' => new ContactMessageResource($message),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found in trash.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found in trash.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Force delete a contact message permanently.
     */
    public function forceDelete($id)
    {
        try {
            $message = ContactMessage::onlyTrashed()->findOrFail($id);
            $message->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Contact message permanently deleted.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found in trash.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found in trash.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Update the status of a contact message (e.g., mark as read, replied, spam).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'status' => 'required|string|in:unread,read,replied,spam',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                    'code' => 422,
                ], 422);
            }

            $message->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Contact message status updated successfully.',
                'data' => new ContactMessageResource($message),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact message not found.',
                'data' => null,
                'errors' => ['contact_message' => ['Contact message could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact message status.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
