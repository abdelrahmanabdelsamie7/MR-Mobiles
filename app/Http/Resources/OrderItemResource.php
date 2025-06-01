<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'product_type' => $this->product_type,
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total_price' => (float) ($this->price * $this->quantity),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->product_type === 'mobile') {
            $data['product'] = new MobileResource($this->mobileColor->mobile);
            $data['color'] = new ColorResource($this->mobileColor->color);
        } else {
            $data['product'] = new AccessoryResource($this->accessoryColor->accessory);
            $data['color'] = new ColorResource($this->accessoryColor->color);
        }

        return $data;
    }
}
