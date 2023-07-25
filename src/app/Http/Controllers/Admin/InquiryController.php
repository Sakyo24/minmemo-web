<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Inquiry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInquiryRequest;
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

    /**
     * inquiry取得
     *
     * @param Inquiry $inquiry
     * @return JsonResponse
     */
    public function show(Inquiry $inquiry): JsonResponse
    {
        $inquiry = Inquiry::with('user')->find($inquiry->id);

        return response()->json([
            'inquiry' => $inquiry
        ]);
    }

    /**
     * inquiry更新
     *
     * @param UpdateInquiryRequest $request
     * @param Inquiry $inquiry
     * @return JsonResponse
     */
    public function update(UpdateInquiryRequest $request, Inquiry $inquiry): JsonResponse
    {
        try {
            $input = $request->only(['status']);
            $inquiry->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'inquiry' => $inquiry
        ]);
    }
}
