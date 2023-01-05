<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    use SerializeDate;

    protected $fillable = [
        'title',
        'detail'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
