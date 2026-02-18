<?php

namespace App\Services;

use App\Services\Feeds\JsonFormatter;
use App\Services\Feeds\XmlFormatter;

class FeedService
{
    private array $formatters = [];

    public function __construct(private ProductService $productService)
    {
        $this->formatters['json'] = new JsonFormatter();
        $this->formatters['xml'] = new XmlFormatter();
    }

    public function generate(string $format): array
    {
        if (!isset($this->formatters[$format])) {
            throw new \Exception("Format '{$format}' not supported");
        }

        $products = $this->productService->getAllForFeed();
        $formatter = $this->formatters[$format];

        return [
            'content' => $formatter->format($products),
            'contentType' => $formatter->getContentType()
        ];
    }

    public function getSupportedFormats(): array
    {
        return array_keys($this->formatters);
    }
}
