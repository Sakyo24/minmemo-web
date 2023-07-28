<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
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
}
