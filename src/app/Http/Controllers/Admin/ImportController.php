<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CsvImportRequest;
use App\Models\Store;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class ImportController extends Controller
{

    public function showCsvForm()
    {
        return view('admin.import.csv');
    }

    public function importCsv(CsvImportRequest $request)
    {
        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);

            $records = Statement::create()->process($csv);
            $storesToInsert = [];
            $validationErrors = [];

            foreach ($records as $index => $record) {
                $rowNumber = $index + 2;

                $trimmedRecord = array_map('trim', $record);

                $validator = Validator::make($trimmedRecord, [
                    '店舗名' => ['required', 'string', 'max:50'],
                    '地域' => ['required', 'string', 'in:東京都,大阪府,福岡県'],
                    'ジャンル' => ['required', 'string', 'in:寿司,焼肉,イタリアン,居酒屋,ラーメン'],
                    '店舗概要' => ['required', 'string', 'max:400'],
                    '画像URL' => ['required', 'string', 'url', 'regex:/\.(jpg|jpeg|png)$/i'],
                ], [
                    'required' => ':attribute は必須です。',
                    'string' => ':attribute は文字列である必要があります。',
                    'max' => ':attribute は :max 文字以内で入力してください。',
                    'in' => ':attribute が無効な値です。',
                    'url' => ':attribute は有効なURL形式である必要があります。',
                    'regex' => ':attribute の形式が無効です (jpg, jpeg, png のみ)。',
                ], [
                    '店舗名' => '店舗名',
                    '地域' => '地域',
                    'ジャンル' => 'ジャンル',
                    '店舗概要' => '店舗概要',
                    '画像URL' => '画像URL',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $message) {
                         $validationErrors[] = "{$rowNumber}行目: {$message}";
                    }
                } else {
                    $storesToInsert[] = [
                        'name' => $trimmedRecord['店舗名'],
                        'region' => $trimmedRecord['地域'],
                        'genre' => $trimmedRecord['ジャンル'],
                        'description' => $trimmedRecord['店舗概要'],
                        'image_url' => $trimmedRecord['画像URL'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($validationErrors)) {
                throw ValidationException::withMessages($validationErrors);
            }

            DB::transaction(function () use ($storesToInsert) {
                Store::insert($storesToInsert);
            });

            return redirect()->route('admin.import.csv.form')
                         ->with('success', count($storesToInsert) . '件の店舗情報をインポートしました。');

        } catch (ValidationException $e) {
            return redirect()->route('admin.import.csv.form')
                         ->withErrors($e->validator);
        } catch (Exception $e) {
            report($e);
            return redirect()->route('admin.import.csv.form')
                         ->withErrors(['csv_file' => 'CSVファイルの処理中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}