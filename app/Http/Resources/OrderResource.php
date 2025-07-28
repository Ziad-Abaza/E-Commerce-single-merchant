<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'shipping_amount' => $this->shipping_amount,
            'shipping_cost' => $this->shipping_cost,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'currency' => $this->currency,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'delivered_at' => $this->delivered_at,
            'cancelled_at' => $this->cancelled_at,
            'receipt_url' => $this->getReceiptUrl(),
            'invoice_url' => $this->getInvoiceUrl(),
            'attachment_url' => $this->getAttachmentUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 