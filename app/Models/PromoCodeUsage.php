<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCodeUsage extends Model
{
  use HasFactory;

  protected $fillable = [
    'promo_code_id',
    'user_id',
    'usage_count',
  ];

  protected $casts = [
    'usage_count' => 'integer',
  ];

  public function promoCode()
  {
    return $this->belongsTo(PromoCode::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
