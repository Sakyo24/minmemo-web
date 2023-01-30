<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rorecek\Ulid\HasUlid;

class Group extends Model
{
    use HasFactory;
    use HasUlid;

    /**
     * IDが自動増分されるか
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * 自動増分IDの「タイプ」
     *
     * @var string
     */
    protected $keyType = 'string';

}
