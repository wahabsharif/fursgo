<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Support\DevHotReload;
use Illuminate\Http\JsonResponse;

class HotReloadController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(DevHotReload::fingerprints());
    }
}
