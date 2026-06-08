<?php

namespace Database\Factories;

use App\Models\Rest;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestFactory extends Factory
{
    protected $model = Rest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ];
    }
}
