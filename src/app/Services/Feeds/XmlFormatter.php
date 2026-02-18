<?php

namespace App\Services\Feeds;

use App\Contracts\FeedFormatterInterface;
use Illuminate\Support\Collection;

class XmlFormatter implements FeedFormatterInterface
{
    public function format(Collection $products): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products/>');
        
        foreach ($products as $product) {
            $item = $xml->addChild('product');
            $item->addChild('id', $product->id);
            $item->addChild('name', htmlspecialchars($product->name));
            $item->addChild('in_stock', $product->stock > 0 ? 'true' : 'false');
        }
        
        return $xml->asXML();
    }
    
    public function getContentType(): string
    {
        return 'application/xml';
    }
}
