<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_type' => $this->product_type,
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total_price' => (float) ($this->price * $this->quantity),
            'product' => new ProductResource($this->product),
            'color' => $this->product_type === 'mobile'
                ? new ColorResource($this->mobileColor->color)
                : new ColorResource($this->accessoryColor->color),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
