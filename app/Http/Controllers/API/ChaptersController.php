<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Chapter;
use Illuminate\Http\JsonResponse;

class ChaptersController extends BaseController
{

    public function get(Request $request): JsonResponse
    {
        try {
            return $this->sendResponse(Chapter::all(), "");
        } catch(\Exception $e) {
            return $this->sendError($e, ["internal server error"], 500);
        }

    }

}
