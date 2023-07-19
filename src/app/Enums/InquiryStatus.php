<?php

namespace App\Enums;

enum InquiryStatus: int
{
    case NO_ANSWER = 1;
    case ANSWERING = 2;
    case ANSWERED = 3;

    public function statusName(): string
    {
        return match($this) {
            self::NO_ANSWER => "未回答",
            self::ANSWERING => "回答中",
            self::ANSWERED => "回答済",
        };
    }
}
