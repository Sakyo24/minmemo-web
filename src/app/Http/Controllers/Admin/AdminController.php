<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminController extends Controller
{
    /**
     * admin一覧全取得
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $admins = Admin::paginate(20);

        return response()->json([
            'admins' => $admins
        ]);
    }

    /**
     * admin取得
     *
     * @param Admin $admin
     * @return JsonResponse
     */
    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'admin' => $admin
        ]);
    }

    /**
     * admin更新
     *
     * @param UpdateAdminRequest $request
     * @param Admin $admin
     * @return JsonResponse
     */
    public function update(UpdateAdminRequest $request, Admin $admin): JsonResponse
    {
        try {
            $input = $request->only(['name', 'email']);
            $admin->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'admin' => $admin
        ]);
    }
}
