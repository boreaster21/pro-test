<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CsvImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // 例: 5MBまで
        ];
    }
    public function messages(): array
    {
        return [
            'csv_file.required' => 'CSVファイルを選択してください。',
            'csv_file.file' => '有効なファイルをアップロードしてください。',
            'csv_file.mimes' => 'ファイル形式はCSVのみです。',
            'csv_file.max' => 'ファイルサイズは5MB以下にしてください。',
        ];
    }
}
