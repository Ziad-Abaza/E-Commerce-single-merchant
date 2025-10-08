<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AttributeUpdateRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    $attribute = $this->route('attribute');
    $attributeId = $this->route('id');
    return [
      'name' => ['sometimes', 'required', 'string', 'max:255'],
      'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('attributes', 'slug')->ignore($attributeId)],
      'type' => ['sometimes', 'required', 'string', Rule::in(['text', 'number', 'select', 'checkbox', 'radio', 'textarea'])],
      'options' => ['nullable', 'array'],
      'options.*' => ['string', 'max:255'],
      'is_required' => ['boolean'],
      'is_filterable' => ['boolean'],
      'is_variant' => ['boolean'],
      'is_visible_on_frontend' => ['boolean'],
      'sort_order' => ['integer', 'min:0'],
    ];
  }

  protected function prepareForValidation()
  {
    if ($this->has('name') && !$this->has('slug')) {
      $this->merge(['slug' => Str::slug($this->name)]);
    }
  }
}