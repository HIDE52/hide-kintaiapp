<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceListRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'nullable|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'date.date_format' => '日付の形式が正しくありません。',
        ];
    }
}
