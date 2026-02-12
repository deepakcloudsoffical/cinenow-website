<?php
namespace Modules\Fronntend\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainmentWatchLimit extends Model
{
    protected $fillable = [
        'entertainment_id',
        'user_id',
        'watch_count_remaining',
    ];
}
