<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CsvImportRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
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
