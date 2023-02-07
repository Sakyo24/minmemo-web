<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Group extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * 自動増分IDの「タイプ」
     *
     * @var string
     */
    protected $keyType = 'string';
}
