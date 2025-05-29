<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUpStatusLog extends Model
{
    protected $table = 'top_up_status_logs';

    protected $fillable = [
        'topup_id',
        'changed_by',
        'status',
    ];

    public function topUp()
    {
        return $this->belongsTo(TopUp::class, 'topup_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
