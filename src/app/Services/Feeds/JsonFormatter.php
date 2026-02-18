<?php

namespace App\Services\Feeds;

use App\Contracts\FeedFormatterInterface;
use Illuminate\Support\Collection;

class JsonFormatter implements FeedFormatterInterface
{
    public function format(Collection $products): string
    {
        $data = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'in_stock' => $p->stock > 0
        ]);
        
        return json_encode(['products' => $data], JSON_PRETTY_PRINT);
    }
    
    public function getContentType(): string
    {
        return 'application/json';
    }
}
