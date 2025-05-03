<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\Statement;
use Exception;

class CsvImportService
{
    /**
     * CSVファイルから店舗情報をインポートする
     *
     * @param UploadedFile $file
     * @return int インポートされたレコード数
     * @throws ValidationException バリデーションエラーが発生した場合
     * @throws Exception その他のエラーが発生した場合
     */
    public function importStoresFromCsv(UploadedFile $file): int
    {
        $path = $file->getRealPath();
        $storesToInsert = [];
        $validationErrors = [];

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);

            $records = Statement::create()->process($csv);

            foreach ($records as $index => $record) {
                $rowNumber = $index + 2; // CSVの行番号 (ヘッダーを除き、1ベース)
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
                // バリデーションエラーがある場合は ValidationException をスロー
                throw ValidationException::withMessages($validationErrors);
            }

            if (empty($storesToInsert)) {
                // 有効なデータがない場合 (例: 空のCSVや全行エラー)
                return 0;
            }

            // トランザクション内で挿入
            DB::transaction(function () use ($storesToInsert) {
                Store::insert($storesToInsert);
            });

            return count($storesToInsert);

        } catch (ValidationException $e) {
            // バリデーション例外はそのままスローしてコントローラーで処理
            throw $e;
        } catch (Exception $e) {
            // その他の予期せぬエラー
            report($e); // エラーをログに記録
            throw new Exception('CSVファイルの処理中に予期せぬエラーが発生しました。');
        }
    }
} 