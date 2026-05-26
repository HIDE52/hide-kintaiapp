<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceListRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $currentMonth = Carbon::now()->format('Y-m');

        return [
            'code' => [
                'nullable',
                'string',
                'regex:/^[0-9]{4}-[0-9]{2}$/',
                'before_or_equal:' . $currentMonth . '-31',
            ],
        ];
    }

    public function messages()
    {
        return [
            'code.regex' => '指定された年月の形式が不適切です。',
            'code.before_or_equal' => '未来の月は表示できません。',
        ];
    }
}
