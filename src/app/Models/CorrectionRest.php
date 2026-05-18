<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_correction_id',
        'requested_break_in',
        'requested_break_out',
    ];

    public function correctionAttendance()
    {
        return $this->belongsTo(CorrectionAttendance::class, 'attendance_correction_id');
    }
}
