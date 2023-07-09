<?php

namespace App\Http\Controllers\API;

use App\Models\ChapterState;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class ChapterStatesController extends BaseController
{

    public function get(Request $request): JsonResponse
    {
        try {
            return $this->sendResponse(ChapterState::with("metroAreas")->get(), "");
        } catch(\Exception $e) {
            return $this->sendError($e, ["internal server error"], 500);
        }

    }

}
