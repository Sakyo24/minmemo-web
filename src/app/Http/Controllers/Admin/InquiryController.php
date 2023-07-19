<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Inquiry;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class InquiryController extends Controller
{
    /**
     * inquiry一覧全取得
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $inquiries = Inquiry::with('user')->paginate(20);

        return response()->json([
            'inquiries' => $inquiries,
        ]);
    }
}
