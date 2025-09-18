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
            'phone' => $this->phone,
            'total_amount' => $this->total_amount,
            'shipping_amount' => $this->shipping_amount,
            'shipping_cost' => $this->shipping_cost,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'currency' => $this->currency,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'delivered_at' => $this->delivered_at?->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),
            'receipt_url' => $this->getReceiptUrl(),
            'invoice_url' => $this->getInvoiceUrl(),
            'attachment_url' => $this->getAttachmentUrl(),
            'subtotal' => $this->getSubtotal(),
            'final_total' => $this->getFinalTotal(),
            'is_delivered' => $this->isDelivered(),
            'is_cancelled' => $this->isCancelled(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'items' => $this->whenLoaded('items', function () {
                return OrderItemResource::collection($this->items);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
