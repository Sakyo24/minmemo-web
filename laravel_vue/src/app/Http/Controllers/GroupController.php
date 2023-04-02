<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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

    /**
     * グループ新規作成
     *
     * @param StoreGroupRequest $request
     * @return JsonResponse
     */
    public function store(StoreGroupRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $input = $request->all();
            $input['owner_user_id'] = $user->id;

            DB::transaction(function () use ($input, $user) {
                $group = new Group();
                $group->fill($input)->save();
                $group_user = new GroupUser();
                $group_user->fill([
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                ])->save();
            });
        } catch (Throwable $e) {
            Log::error((string)$e);

            throw $e;
        }

        return response()->json([], Response::HTTP_CREATED);
    }
}
