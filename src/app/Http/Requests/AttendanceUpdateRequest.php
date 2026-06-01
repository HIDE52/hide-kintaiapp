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
            'rest_id.*.break_in' => 'bail|nullable|required_with:rest_id.*.break_out|date_format:H:i|after:punch_in|before:punch_out',
            'rest_id.*.break_out' => 'bail|nullable|required_with:rest_id.*.break_in|date_format:H:i|after:rest_id.*.break_in|before:punch_out',
            'remark' => 'bail|required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'punch_in.date_format' => '出勤時間の形式が正しくありません',
            'punch_out.date_format' => '退勤時間の形式が正しくありません',
            'punch_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'rest_id.*.break_in.date_format' => '休憩時間の形式が正しくありません',
            'rest_id.*.break_in.after' => '休憩時間が不適切な値です',
            'rest_id.*.break_in.before' => '休憩時間が不適切な値です',

            'rest_id.*.break_out.date_format' => '休憩時間の形式が正しくありません',
            'rest_id.*.break_out.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'rest_id.*.break_out.before' => '休憩時間もしくは退勤時間が不適切な値です',

            'rest_id.*.break_in.required_with' => '休憩の開始時間と終了時間は両方入力してください',
            'rest_id.*.break_out.required_with' => '休憩の開始時間と終了時間は両方入力してください',

            'remark.required' => '備考を記入してください',
            'remark.max' => '備考は255文字以内で入力してください',
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
