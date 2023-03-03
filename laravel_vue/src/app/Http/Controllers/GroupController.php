<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * グループ一覧全取得
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $groups = $user->groups;

        return response()->json([
            'groups' => $groups
        ]);
    }
}
