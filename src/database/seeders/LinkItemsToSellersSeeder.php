<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;   // ← これを使う
use App\Models\User;
use App\Models\Item;

class LinkItemsToSellersSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) 出品者ユーザー（UsersSeeder で作成済み）
            $sellerA = User::where('email', 'user_a@example.com')->firstOrFail();
            $sellerB = User::where('email', 'user_b@example.com')->firstOrFail();
            // user_c は紐付けしない

            // 2) items テーブルの列を確認して分岐
            if (Schema::hasColumn('items', 'code')) {
                // C001〜C005 → User A,  C006〜C010 → User B
                Item::whereIn('code', ['C001', 'C002', 'C003', 'C004', 'C005'])
                    ->update(['user_id' => $sellerA->id]);

                Item::whereIn('code', ['C006', 'C007', 'C008', 'C009', 'C010'])
                    ->update(['user_id' => $sellerB->id]);

                // PHP7.4 なので null-safe は使わず通常の -> を使用
                $this->command->info('Linked items by code column (C001–C010).');
                return;
            }

            // 3) code 列が無い場合：商品名で対応（あなたのDBの商品名に合わせて調整）
            $groupA = ['腕時計', 'HDD', '玉ねぎ3束', '革靴', 'ノートPC'];
            $groupB = ['マイク', 'ショルダーバッグ', 'タンブラー', 'コーヒーミル', 'メイクセット'];

            Item::whereIn('name', $groupA)->update(['user_id' => $sellerA->id]);
            Item::whereIn('name', $groupB)->update(['user_id' => $sellerB->id]);

            $this->command->warn('Linked items by name (code column not found). 商品名が一致しない場合は配列を調整してください。');
        });
    }
}
