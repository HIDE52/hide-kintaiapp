<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\CorrectionAttendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => '山田　太朗',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
        ]);

        $userA = User::create([
            'name' => '佐藤　隆',
            'email' => 'userA@example.com',
            'password' => Hash::make('password'),
            'role' => 2,
        ]);

        $startOfMonth = Carbon::now()->startOfMonth();

        for ($date = $startOfMonth->copy(); $date->isPast() || $date->isToday(); $date->addDay()) {
            $attendanceA = $userA->attendances()->create([
                'date' => $date->format('Y-m-d'),
                'punch_in' => '09:00:00',
                'punch_out' => '18:00:00',
            ]);

            if ($date->day == 15) {
                $attendanceA->rests()->create(['break_in' => '12:00:00', 'break_out' => '12:45:00']);
                $attendanceA->rests()->create(['break_in' => '15:00:00', 'break_out' => '15:15:00']);
            } else {
                $attendanceA->rests()->create(['break_in' => '12:00:00', 'break_out' => '13:00:00']);
            }
        }

        $userB = User::create([
            'name' => '鈴木 涼子',
            'email' => 'userB@example.com',
            'password' => Hash::make('password'),
            'role' => 2,
        ]);

        for ($date = $startOfMonth->copy(); $date->isPast() || $date->isToday(); $date->addDay()) {
            if ($date->isYesterday()) {
                $userB->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '20:00:00',
                    'punch_out' => null,
                ]);
            } elseif ($date->isToday()) {
                // 出勤前状態
            } else {
                $attendanceB = $userB->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '10:00:00',
                    'punch_out' => '19:00:00',
                ]);
                $attendanceB->rests()->create(['break_in' => '13:00:00', 'break_out' => '14:00:00']);
            }
        }

        $userC = User::create([
            'name' => '高橋 健太',
            'email' => 'userC@example.com',
            'password' => Hash::make('password'),
            'role' => 2,
        ]);

        for ($date = $startOfMonth->copy(); $date->isPast() || $date->isToday(); $date->addDay()) {
            if ($date->isYesterday()) {
                $attendanceC = $userC->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '09:00:00',
                    'punch_out' => '18:00:00',
                ]);
                $attendanceC->rests()->create(['break_in' => '12:00:00', 'break_out' => '13:00:00']);

                $attendanceC->correctionAttendances()->create([
                    'user_id' => $userC->id,
                    'requested_punch_in' => '08:30:00',
                    'requested_punch_out' => '18:00:00',
                    'status' => 0,
                    'remark' => '電車の遅延により打刻が遅れました。手続きをお願いします。',
                ]);
            } elseif ($date->isToday()) {
                $userC->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '09:00:00',
                    'punch_out' => null,
                ]);
            } else {
                $attendanceC = $userC->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '09:00:00',
                    'punch_out' => '18:00:00',
                ]);
                $attendanceC->rests()->create(['break_in' => '12:00:00', 'break_out' => '13:00:00']);
            }
        }
    }
}
