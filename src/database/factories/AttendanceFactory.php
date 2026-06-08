<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id'   => User::factory(),
            'date'      => now()->format('Y-m-d'),
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ];
    }
}
