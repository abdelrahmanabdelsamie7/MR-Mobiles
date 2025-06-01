<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount' => $this->discount,
            'final_price' => (float) $this->final_price,
            'status' => $this->status,
            'product_type' => $this->product_type,
            'image' => $this->product_type === 'mobile' ? $this->image_cover : $this->image,
            'brand' => new BrandResource($this->brand),
        ];

        if ($this->product_type === 'mobile') {
            $data = array_merge($data, [
                'model_number' => $this->model_number,
                'battery' => $this->battery,
                'processor' => $this->processor,
                'storage' => $this->storage,
                'display' => $this->display,
                'operating_system' => $this->operating_system,
                'camera' => $this->camera,
                'network_support' => $this->network_support,
                'release_year' => $this->release_year,
            ]);
        } elseif ($this->product_type === 'accessory') {
            $data = array_merge($data, [
                'battery' => $this->battery,
            ]);
        }

        return $data;
    }
}
