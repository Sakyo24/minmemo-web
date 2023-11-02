<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Http\Requests\AddGroupUserRequest;
use App\Http\Requests\DeleteGroupUserRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
        $groups = $user->groups()->orderBy('updated_at', 'desc')->get();

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
            $input = $request->only(['name']);
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

    /**
     * グループ更新
     *
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function update(UpdateGroupRequest $request, Group $group): JsonResponse
    {
        try {
            $input = $request->only(['name']);
            DB::transaction(function () use ($input, $group) {
                $group->fill($input)->update();
            });
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'group' => $group
        ]);
    }

    /**
     * グループ削除
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group): JsonResponse
    {
        try {
            DB::transaction(function () use ($group) {
                $group->groupUsers->each->delete();
                $group->todos->each->delete();
                $group->delete();
            });
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * グループのtodo一覧を取得
     * 
     * @param Group $group
     * @return JsonResponse
     */
    public function todos(Group $group): JsonResponse
    {
        $todos = $group->todos()->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'todos' => $todos
        ]);
    }

    /**
     * グループのユーザー一覧を取得
     * 
     * @param Group $group
     * @return JsonResponse
     */
    public function users(Group $group): JsonResponse
    {
        $users = $group->users()->orderBy('name', 'asc')->get();

        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * グループにユーザーを追加
     *
     * @param AddGroupUserRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function addUser(AddGroupUserRequest $request, Group $group): JsonResponse
    {
        try {
            $input = $request->only(['email']);
            $user = User::where('email', $input['email'])->first();

            if (is_null($user)) {
                return response()->json([
                    'message' => 'ユーザーが存在しません'
                ], Response::HTTP_BAD_REQUEST);
            }

            $group_user = GroupUser::where('group_id', $group->id)->where('user_id', $user->id)->first();
            if (!is_null($group_user)) {
                return response()->json([
                    'message' => '当該ユーザーは、すでにこのグループに追加されています'
                ], Response::HTTP_BAD_REQUEST);
            }

            $group_user = new GroupUser();
            $group_user->fill([
                'user_id' => $user->id,
                'group_id' => $group->id,
            ])->save();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'group_user' => $group_user
        ]);
    }

    /**
     * グループのユーザーを削除
     *
     * @param DeleteGroupUserRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function deleteUser(DeleteGroupUserRequest $request, Group $group): JsonResponse
    {
        try {
            $input = $request->only(['user_id']);
            $group_user = GroupUser::where('group_id', $group->id)->where('user_id', $input['user_id'])->first();
            $group_user->delete();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
