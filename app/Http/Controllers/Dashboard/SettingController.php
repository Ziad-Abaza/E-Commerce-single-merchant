<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display a listing of settings grouped by category
     */
    public function index(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string|max:50',
        ]);

        $query = Setting::query();

        if ($request->group) {
            $query->byGroup($request->group);
        } else {
            $query->orderBy('group')->orderBy('sort_order');
        }

        $settings = $query->get();

        // Group settings by their group
        $groupedSettings = $settings->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => $groupedSettings,
            'groups' => $settings->pluck('group')->unique()->values(),
        ]);
    }

    /**
     * Store a newly created setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:text,number,boolean,json,file,image,select,textarea',
            'group' => 'required|string|max:50',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'is_public' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->except('value');

            if (in_array($request->type, ['image', 'file']) && $request->hasFile('value')) {
                $data['value'] = $request->file('value');
            } else {
                $data['value'] = $request->input('value');
            }

            $setting = Setting::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'data' => $setting,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified setting
     */
    public function show(Setting $setting)
    {
        return response()->json([
            'success' => true,
            'data' => $setting,
        ]);
    }

    /**
     * Update the specified setting
     */
    public function update(Request $request, Setting $setting)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'type' => 'required|in:text,number,boolean,json,file,image,select,textarea',
            'group' => 'required|string|max:50',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'is_public' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'file' => 'nullable|file|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'key' => $request->input('key'),
                'type' => $request->input('type'),
                'group' => $request->input('group'),
                'label' => $request->input('label'),
                'description' => $request->input('description', $setting->description),
                'options' => $request->input('options', $setting->options),
                'is_public' => $request->boolean('is_public', $setting->is_public),
                'sort_order' => $request->input('sort_order', $setting->sort_order ?? 0),
            ];

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $data['value'] = $request->file('file');
            } else if ($request->has('value')) {
                $data['value'] = $request->input('value');
            } else {
                $data['value'] = $setting->value;
            }

            // Update the setting
            $setting->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => $setting->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating setting: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified setting
     */
    public function destroy(Setting $setting)
    {
        try {
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $updatedSettings = [];

            foreach ($request->settings as $settingData) {
                $setting = Setting::where('key', $settingData['key'])->first();
                if ($setting) {
                    $setting->update(['value' => $settingData['value']]);
                    $updatedSettings[] = $setting->fresh();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $updatedSettings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get public settings (for frontend use)
     */
    public function public()
    {
        $settings = Setting::public()->get();

        $formattedSettings = $settings->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->typed_value];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
        ]);
    }
}
