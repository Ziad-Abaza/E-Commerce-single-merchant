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
            'paid_at' => $this->paid_at,
            'receipt_url' => $this->getReceiptUrl(),
            'proof_of_payment_url' => $this->getProofOfPaymentUrl(),
            'document_url' => $this->getDocumentUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 