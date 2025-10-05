<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Http\Requests\AttributeCategoryAssignRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AttributeCategoryController extends Controller
{
  public function allCategories()
  {
    try {
      $categories = Category::with(['parent', 'children'])
        ->orderBy('sort_order')
        ->get()
        ->map(function ($category) {
          return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'parent_name' => $category->parent?->name,
            'thumbnail_url' => $category->getThumbnailUrl(),
          ];
        });

      return response()->json([
        'success' => true,
        'message' => 'Categories retrieved successfully.',
        'data' => $categories,
        'errors' => null,
        'code' => 200,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve categories.',
        'data' => null,
        'errors' => ['server' => [$e->getMessage()]],
        'code' => 500,
      ], 500);
    }
  }


  public function assign(AttributeCategoryAssignRequest $request)
  {
    try {
      $attributeId = $request->integer('attribute_id');
      $categoryId = $request->integer('category_id');
      $isRequired = $request->boolean('is_required', false);
      $sortOrder = $request->integer('sort_order', 0);

      $attribute = Attribute::findOrFail($attributeId);
      $category = Category::findOrFail($categoryId);

      DB::beginTransaction();

      $attribute->categories()->syncWithoutDetaching([
        $categoryId => [
          'is_required' => $isRequired,
          'sort_order' => $sortOrder,
        ],
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Attribute assigned to category successfully.',
        'data' => null,
        'errors' => null,
        'code' => 200,
      ]);
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Attribute or category not found.',
        'data' => null,
        'errors' => ['input' => ['Invalid attribute_id or category_id.']],
        'code' => 404,
      ], 404);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'success' => false,
        'message' => 'Failed to assign attribute to category.',
        'data' => null,
        'errors' => ['server' => [$e->getMessage()]],
        'code' => 500,
      ], 500);
    }
  }

  public function detach(Attribute $attribute, Category $category)
  {
    try {
      $attribute->categories()->detach($category->id);

      return response()->json([
        'success' => true,
        'message' => 'Attribute detached from category successfully.',
        'data' => null,
        'errors' => null,
        'code' => 200,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to detach attribute from category.',
        'data' => null,
        'errors' => ['server' => [$e->getMessage()]],
        'code' => 500,
      ], 500);
    }
  }


  public function forCategory(Category $category)
  {
    try {
      $attributes = $category->attributes()
        ->withPivot(['is_required', 'sort_order'])
        ->orderBy('attribute_category.sort_order')
        ->get();

      return response()->json([
        'success' => true,
        'message' => 'Attributes for category retrieved successfully.',
        'data' => $attributes->map(function ($attr) {
          return [
            'id' => $attr->id,
            'name' => $attr->name,
            'slug' => $attr->slug,
            'type' => $attr->type,
            'options' => $attr->options,
            'is_required_in_category' => (bool) $attr->pivot->is_required,
            'sort_order_in_category' => (int) $attr->pivot->sort_order,
          ];
        }),
        'errors' => null,
        'code' => 200,
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve attributes for category.',
        'data' => null,
        'errors' => ['server' => [$e->getMessage()]],
        'code' => 500,
      ], 500);
    }
  }
}
