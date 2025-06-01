<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AccessoryColorVariantResource;
class AccessoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'battery'      => $this->battery,
            'image'        => $this->image,
            'total_quantity' =>$this->total_quantity ,
            'brand'        => new BrandResource($this->whenLoaded('brand')),
            'price'        => $this->price,
            'discount'     => $this->discount,
            'final_price'  => $this->final_price,
            'status'       => $this->status,
            'product_type' => $this->product_type,
            'colors'       => AccessoryColorVariantResource::collection($this->whenLoaded('colorVariants')),
        ];
    }
}