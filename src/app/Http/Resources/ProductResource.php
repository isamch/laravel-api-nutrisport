<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $site = $request->query('site');
        $siteId = null;
        
        if ($site) {
            $siteId = \Cache::remember("site_{$site}", 86400, function () use ($site) {
                return \DB::table('sites')->where('country_code', strtoupper($site))->value('id');
            });
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $siteId ? $this->prices->where('site_id', $siteId)->first()?->price : null,
            'stock' => $this->stock,
            'in_stock' => $this->stock > 0,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => $this->whenLoaded('images', fn() => $this->images->map(fn($img) => [
                'id' => $img->id,
                'url' => url(\Storage::url($img->url)),
                'alt_text' => $img->alt_text
            ])),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
