<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccessoryColorVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'stock_quantity' => $this->stock_quantity,
            'is_available' => $this->hasStock(),
            'color' => [
                'id' => $this->color?->id,
                'name' => $this->color?->name,
                'hex_code' => $this->color?->hex_code,
            ],
            'images' => AccessoryVariantImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
