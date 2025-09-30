<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $attributes = Attribute::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $attributes->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
        }

        $attributes = $attributes->paginate($perPage);

        return response()->json([
            'message' => 'Attributes retrieved successfully.',
            'data' => AttributeResource::collection($attributes),
            'pagination' => [
                'current_page' => $attributes->currentPage(),
                'per_page' => $attributes->perPage(),
                'total' => $attributes->total(),
                'last_page' => $attributes->lastPage(),
                'from' => $attributes->firstItem(),
                'to' => $attributes->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created attribute in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:attributes,slug',
            'type' => 'required|in:text,number,select,multiselect,checkbox,radio,textarea,date,datetime,boolean',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_visible_on_frontend' => 'boolean',
            'is_variant' => 'boolean',
            'sort_order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_required_for_category' => 'nullable|array',
            'is_required_for_category.*' => 'boolean',
            'sort_order_for_category' => 'nullable|array',
            'sort_order_for_category.*' => 'integer',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Create the attribute
        $attribute = Attribute::create($validated);

        // Sync categories if provided
        if (isset($validated['categories'])) {
            $categories = [];
            foreach ($validated['categories'] as $index => $categoryId) {
                $categories[$categoryId] = [
                    'is_required' => $validated['is_required_for_category'][$index] ?? false,
                    'sort_order' => $validated['sort_order_for_category'][$index] ?? 0,
                ];
            }
            $attribute->categories()->sync($categories);
        }

        return response()->json([
            'message' => 'Attribute created successfully.',
            'data' => new AttributeResource($attribute->load('categories')),
        ], 201);
    }

    /**
     * Display the specified attribute.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $attribute = Attribute::with('categories')->findOrFail($id);
        
        return response()->json([
            'message' => 'Attribute retrieved successfully.',
            'data' => new AttributeResource($attribute),
        ]);
    }

    /**
     * Update the specified attribute in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'slug')->ignore($attribute->id),
            ],
            'type' => 'sometimes|required|in:text,number,select,multiselect,checkbox,radio,textarea,date,datetime,boolean',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_visible_on_frontend' => 'boolean',
            'is_variant' => 'boolean',
            'sort_order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_required_for_category' => 'nullable|array',
            'is_required_for_category.*' => 'boolean',
            'sort_order_for_category' => 'nullable|array',
            'sort_order_for_category.*' => 'integer',
        ]);

        // Update the attribute
        $attribute->update($validated);

        // Sync categories if provided
        if (isset($validated['categories'])) {
            $categories = [];
            foreach ($validated['categories'] as $index => $categoryId) {
                $categories[$categoryId] = [
                    'is_required' => $validated['is_required_for_category'][$index] ?? false,
                    'sort_order' => $validated['sort_order_for_category'][$index] ?? 0,
                ];
            }
            $attribute->categories()->sync($categories);
        }

        return response()->json([
            'message' => 'Attribute updated successfully.',
            'data' => new AttributeResource($attribute->load('categories')),
        ]);
    }

    /**
     * Remove the specified attribute from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        
        // Detach from categories first
        $attribute->categories()->detach();
        
        // Delete the attribute
        $attribute->delete();

        return response()->json([
            'message' => 'Attribute deleted successfully.',
        ]);
    }

    /**
     * Get attributes for a specific category.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $attributes = $category->allAttributes();
        
        return response()->json([
            'message' => 'Attributes retrieved successfully.',
            'data' => AttributeResource::collection($attributes),
        ]);
    }
}
