<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsSessionAttendance extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'check_in_at',
        'check_out_at',
        'feedback',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'rating' => 'integer',
        ];
    }

    // ── Relations ──

    public function session(): BelongsTo
    {
        return $this->belongsTo(LmsSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Methods ──

    public function checkIn(): void
    {
        $this->update([
            'status' => 'attended',
            'check_in_at' => Carbon::now(),
        ]);
    }

    public function checkOut(): void
    {
        $this->update([
            'check_out_at' => Carbon::now(),
        ]);
    }
}
