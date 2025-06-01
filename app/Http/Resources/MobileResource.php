<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class MobileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'image_cover'  => $this->image_cover,
            'model_number' => $this->model_number,
            'battery'      => $this->battery,
            'processor'    => $this->processor,
            'storage'      => $this->storage,
            'display'      => $this->display,
            'operating_system' => $this->operating_system,
            'camera'       => $this->camera,
            'network_support' => $this->network_support,
            'total_quantity' =>$this->total_quantity ,
            'brand'        => new BrandResource($this->whenLoaded('brand')),
            'price'        => $this->price,
            'discount'     => $this->discount,
            'final_price'  => $this->final_price,
            'release_year' => $this->release_year,
            'status'       => $this->status,
            'product_type' => $this->product_type,
            'colors'       => MobileColorVariantResource::collection($this->whenLoaded('colorVariants')),

        ];
    }
}