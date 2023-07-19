<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InquiryStatus;
use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;
    use SerializeDate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'user_id',
        'name',
        'email',
        'title',
        'detail',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute(): string
    {
        return $this->status->statusName();
    }

    public function setStatusNameAttribute($value): void
    {
        $this->attributes['status_name'] = $value;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
