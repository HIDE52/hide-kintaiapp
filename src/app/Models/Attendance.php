<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'punch_in',
        'punch_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function correctionAttendances()
    {
        return $this->hasMany(CorrectionAttendance::class);
    }

    public function getTotalRestTimeAttribute()
    {
        if ($this->rests->isEmpty()) {
            return '00:00';
        }

        $totalSeconds = 0;

        foreach ($this->rests as $rest) {
            if ($rest->break_in && $rest->break_out) {
                $in = Carbon::parse($rest->break_in);
                $out = Carbon::parse($rest->break_out);
                $totalSeconds += $out->diffInSeconds($in);
            }
        }

        $totalMinutes = floor($totalSeconds / 60);

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getTotalWorkTimeAttribute()
    {
        if (!$this->punch_in || !$this->punch_out) {
            return '-';
        }

        $punchIn = Carbon::parse($this->punch_in);
        $punchOut = Carbon::parse($this->punch_out);

        $totalSeconds = $punchOut->diffInSeconds($punchIn);

        $restSeconds = 0;
        foreach ($this->rests as $rest) {
            if ($rest->break_in && $rest->break_out) {
                $restSeconds += Carbon::parse($rest->break_out)->diffInSeconds(Carbon::parse($rest->break_in));
            }
        }

        $workSeconds = $totalSeconds - $restSeconds;
        if ($workSeconds < 0) {
            $workSeconds = 0;
        }

        $totalMinutes = ceil($workSeconds / 60);

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
