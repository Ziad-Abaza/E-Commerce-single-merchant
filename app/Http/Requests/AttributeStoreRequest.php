<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AttributeStoreRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'slug' => ['nullable', 'string', 'max:255', 'unique:attributes,slug'],
      'type' => ['required', 'string', Rule::in(['text', 'number', 'select', 'checkbox', 'radio', 'textarea'])],
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
    if (!$this->has('slug')) {
      $this->merge(['slug' => Str::slug($this->name)]);
    }
  }
}
