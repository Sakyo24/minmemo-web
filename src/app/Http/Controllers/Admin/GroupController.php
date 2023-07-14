<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Http\Requests\UpdateGroupRequest;
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

    /**
     * group取得
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function show(Group $group): JsonResponse
    {
        $group = Group::with('owner')->find($group->id);

        return response()->json([
            'group' => $group
        ]);
    }

    /**
     * group更新
     *
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function update(UpdateGroupRequest $request, Group $group): JsonResponse
    {
        try {
            $input = $request->only(['name']);
            $group->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'group' => $group
        ]);
    }

    /**
     * group削除
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group): JsonResponse
    {
        try {
            $group->delete();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
