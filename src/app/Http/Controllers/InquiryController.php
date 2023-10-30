<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Mail\InquiryToAdminsMail;
use App\Mail\InquiryToUserMail;
use App\Models\Admin;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class InquiryController extends Controller
{
    /**
     * お問い合わせ　新規作成
     *
     * @param StoreInquiryRequest $request
     * @return JsonResponse
     */
    public function store(StoreInquiryRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $input = $request->all();
            $input['user_id'] = $user->id;

            DB::transaction(function () use ($input, $user) {
                $inquiry = new Inquiry();
                $inquiry->fill($input)->save();
                $admin_emails = Admin::all()->pluck('email');
                Mail::to($user)->send(new InquiryToUserMail($inquiry));
                Mail::to($admin_emails)->send(new InquiryToAdminsMail($inquiry));
            });
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_CREATED);
    }
}
