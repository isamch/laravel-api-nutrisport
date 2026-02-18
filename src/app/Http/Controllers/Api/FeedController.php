<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeedService;

class FeedController extends Controller
{
    public function __construct(private FeedService $feedService) {}

    public function show($format)
    {
        try {
            $feed = $this->feedService->generate($format);
            
            return response($feed['content'])
                ->header('Content-Type', $feed['contentType']);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'supported_formats' => $this->feedService->getSupportedFormats()
            ], 400);
        }
    }
}
