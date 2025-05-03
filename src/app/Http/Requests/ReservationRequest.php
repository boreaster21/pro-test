<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i'],
            'number_of_people' => ['required', 'integer', 'min:1', 'max:20'], // 例: 最大20人
        ];
    }


    public function messages(): array
    {
        return [
            'store_id.required' => '店舗を選択してください。',
            'store_id.exists' => '存在しない店舗です。',
            'reservation_date.required' => '予約日は必須です。',
            'reservation_date.date_format' => '予約日は「YYYY-MM-DD」形式で入力してください。',
            'reservation_date.after_or_equal' => '予約日は本日以降の日付を指定してください。',
            'reservation_time.required' => '予約時間は必須です。',
            'reservation_time.date_format' => '予約時間は「HH:MM」形式で入力してください。',
            'number_of_people.required' => '予約人数は必須です。',
            'number_of_people.integer' => '予約人数は整数で入力してください。',
            'number_of_people.min' => '予約人数は1人以上で入力してください。',
            'number_of_people.max' => '予約人数は20人以下で入力してください。',
        ];
    }
}
