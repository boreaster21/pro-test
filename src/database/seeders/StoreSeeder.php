<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::factory()->createMany([
            [
                'name' => '仙人',
                'region' => '東京都',
                'genre' => '寿司',
                'description' => '料理長厳選の食材から作る寿司を用いたコースをぜひお楽しみください。食材・味・価格、お客様の満足度を徹底的に追求したお店です。特別な日のお食事、ビジネス接待まで気軽に使用することができます。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg'
            ],
            [
                'name' => '牛助',
                'region' => '大阪府',
                'genre' => '焼肉',
                'description' => '焼肉業界で20年間経験を積み、肉を熟知したマスターによる実力派焼肉店。長年の実績とお付き合いをもとに、なかなか食べられない希少部位も仕入れております。また、ゆったりとくつろげる空間はお仕事終わりの一杯や女子会にぴったりです。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg'
            ],
            [
                'name' => '戦慄',
                'region' => '福岡県',
                'genre' => '居酒屋',
                'description' => '気軽に立ち寄れる昔懐かしの大衆居酒屋です。キンキンに冷えたビールを、なんと199円で楽しむことができます。定番メニューから季節限定メニューまで、とにかく豊富にご用意。週替わりメニューは店内のボードをご確認ください。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg'
            ],
            [
                'name' => 'ルーク',
                'region' => '東京都',
                'genre' => 'イタリアン',
                'description' => '都心にひっそりとたたずむ、隠れ家イタリアン。季節の食材を使ったコース料理は、ワインとの相性も抜群です。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg'
            ],
            [
                'name' => '志摩屋',
                'region' => '福岡県',
                'genre' => 'ラーメン',
                'description' => '博多豚骨ラーメンの本場福岡で、常に行列を作る人気店。秘伝のタレと熟成スープは一度食べたらやみつきに。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg'
            ],
            [
                'name' => '評価なしテスト店',
                'region' => '東京都',
                'genre' => '居酒屋',
                'description' => 'この店舗は評価ソートのテスト用です。評価はまだありません。',
                'image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg'
            ],
        ]);
    }
}
