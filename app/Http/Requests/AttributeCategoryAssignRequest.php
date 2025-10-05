<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeCategoryAssignRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'category_id' => ['required', 'exists:categories,id'],
      'is_required' => ['boolean'],
      'sort_order' => ['integer', 'min:0'],
    ];
  }
}
