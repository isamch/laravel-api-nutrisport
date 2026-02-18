<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface FeedFormatterInterface
{
    public function format(Collection $products): string;
    public function getContentType(): string;
}
