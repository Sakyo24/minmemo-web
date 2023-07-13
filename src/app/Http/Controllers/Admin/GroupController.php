<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;


class GroupController extends Controller
{
    /**
     * group一覧全取得
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $groups = Group::with('owner')->paginate(20);

        return response()->json([
            'groups' => $groups
        ]);
    }
}
