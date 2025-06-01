<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_proof' => $this->payment_proof,
            'total_price' => (float) $this->total_price,
            'note' => $this->note,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Show CartItems when pending, OrderItems when confirmed
        if ($this->payment_status === 'pending') {
            $data['items'] = CartItemResource::collection($this->user->cart->items);
        } else {
            $data['items'] = OrderItemResource::collection($this->whenLoaded('items'));
        }

        return $data;
    }
}