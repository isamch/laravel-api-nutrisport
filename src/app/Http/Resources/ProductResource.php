<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $siteId = $request->query('site_id');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $siteId ? $this->prices->where('site_id', $siteId)->first()?->price : null,
            'stock' => $siteId ? $this->stock->where('site_id', $siteId)->first()?->quantity : null,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => $this->whenLoaded('images', fn() => $this->images->pluck('url')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
