<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'gateway_response' => $this->gateway_response,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'receipt_url' => $this->getReceiptUrl(),
            'proof_of_payment_url' => $this->getProofOfPaymentUrl(),
            'document_url' => $this->getDocumentUrl(),
            'formatted_amount' => $this->formatted_amount,
            'is_completed' => $this->isCompleted(),
            'is_pending' => $this->isPending(),
            'is_failed' => $this->isFailed(),
            'is_refunded' => $this->isRefunded(),
            'has_transaction_id' => $this->hasTransactionId(),
            'has_gateway_response' => $this->hasGatewayResponse(),
            'order' => $this->whenLoaded('order', function () {
                return new OrderResource($this->order);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
