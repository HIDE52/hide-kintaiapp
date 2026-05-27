<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'punch_in' => 'bail|required|date_format:H:i',
            'punch_out' => 'bail|required|date_format:H:i|after:punch_in',
            'break_in.*' => 'bail|nullable|date_format:H:i|after:punch_in|before:punch_out',
            'break_out.*' => 'bail|nullable|date_format:H:i|after:break_in.*|before:punch_out',
            'remark' => 'bail|required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            // ↓↓↓ 【追記】形式エラーのときの受け皿を作ってあげる ↓↓↓
            'punch_in.date_format' => '出勤時間の形式が正しくありません',
            'punch_out.date_format' => '退勤時間の形式が正しくありません',

            'punch_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_in.*.after' => '休憩時間が不適切な値です',
            'break_in.*.before' => '休憩時間が不適切な値です',
            'break_out.*.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'break_out.*.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'remark.required' => '備考を記入してください',
        ];
    }

    public function attributes()
    {
        return [
            'punch_in' => '出勤時間',
            'punch_out' => '退勤時間',
            'remark' => '備考',
        ];
    }
}
