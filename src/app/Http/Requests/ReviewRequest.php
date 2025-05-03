<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReviewRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価は必須です。',
            'rating.integer' => '評価は整数で指定してください。',
            'rating.min' => '評価は1以上で指定してください。',
            'rating.max' => '評価は5以下で指定してください。',
            'comment.required' => '口コミ本文は必須です。',
            'comment.max' => '口コミ本文は400文字以内で入力してください。',
            'image.required' => '画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像はJPEGまたはPNG形式のみアップロードできます。',
            'image.max' => '画像サイズは2MB以下にしてください。',
        ];
    }
}
