<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            ['name' => 'พล.ร.อ.', 'full_name' => 'พลเรือเอก', 'sort_order' => 1],
            ['name' => 'พล.ร.ท.', 'full_name' => 'พลเรือโท', 'sort_order' => 2],
            ['name' => 'พล.ร.ต.', 'full_name' => 'พลเรือตรี', 'sort_order' => 3],
            ['name' => 'น.อ.พิเศษ', 'full_name' => 'นาวาเอก (พิเศษ)', 'sort_order' => 4],
            ['name' => 'น.อ.', 'full_name' => 'นาวาเอก', 'sort_order' => 5],
            ['name' => 'น.อ.หญิง', 'full_name' => 'นาวาเอกหญิง', 'sort_order' => 6],
            ['name' => 'ว่าที่ น.อ.', 'full_name' => 'ว่าที่ นาวาเอก', 'sort_order' => 7],
            ['name' => 'ว่าที่ น.อ.หญิง', 'full_name' => 'ว่าที่ นาวาเอกหญิง', 'sort_order' => 8],
            ['name' => 'น.ท.', 'full_name' => 'นาวาโท', 'sort_order' => 9],
            ['name' => 'น.ท.หญิง', 'full_name' => 'นาวาโทหญิง', 'sort_order' => 10],
            ['name' => 'ว่าที่ น.ท.', 'full_name' => 'ว่าที่ นาวาโท', 'sort_order' => 11],
            ['name' => 'ว่าที่ น.ท.หญิง', 'full_name' => 'ว่าที่ นาวาโทหญิง', 'sort_order' => 12],
            ['name' => 'น.ต.', 'full_name' => 'นาวาตรี', 'sort_order' => 13],
            ['name' => 'น.ต.หญิง', 'full_name' => 'นาวาตรีหญิง', 'sort_order' => 14],
            ['name' => 'ว่าที่ น.ต.', 'full_name' => 'ว่าที่ นาวาตรี', 'sort_order' => 15],
            ['name' => 'ว่าที่ น.ต.หญิง', 'full_name' => 'ว่าที่ นาวาตรีหญิง', 'sort_order' => 16],
            ['name' => 'ร.อ.', 'full_name' => 'เรือเอก', 'sort_order' => 17],
            ['name' => 'ร.อ.หญิง', 'full_name' => 'เรือเอกหญิง', 'sort_order' => 18],
            ['name' => 'ว่าที่ ร.อ.', 'full_name' => 'ว่าที่ เรือเอก', 'sort_order' => 19],
            ['name' => 'ว่าที่ ร.อ.หญิง', 'full_name' => 'ว่าที่ เรือเอกหญิง', 'sort_order' => 20],
            ['name' => 'ร.ท.', 'full_name' => 'เรือโท', 'sort_order' => 21],
            ['name' => 'ร.ท.หญิง', 'full_name' => 'เรือโทหญิง', 'sort_order' => 22],
            ['name' => 'ว่าที่ ร.ท.', 'full_name' => 'ว่าที่ เรือโท', 'sort_order' => 23],
            ['name' => 'ว่าที่ ร.ท.หญิง', 'full_name' => 'ว่าที่ เรือโทหญิง', 'sort_order' => 24],
            ['name' => 'ร.ต.', 'full_name' => 'เรือตรี', 'sort_order' => 25],
            ['name' => 'ร.ต.หญิง', 'full_name' => 'เรือตรีหญิง', 'sort_order' => 26],
            ['name' => 'ว่าที่ ร.ต.', 'full_name' => 'ว่าที่ เรือตรี', 'sort_order' => 27],
            ['name' => 'ว่าที่ ร.ต.หญิง', 'full_name' => 'ว่าที่ เรือตรีหญิง', 'sort_order' => 28],
            ['name' => 'พ.จ.อ.', 'full_name' => 'พันจ่าเอก', 'sort_order' => 29],
            ['name' => 'พ.จ.อ.หญิง', 'full_name' => 'พันจ่าเอกหญิง', 'sort_order' => 30],
            ['name' => 'พ.จ.ท.', 'full_name' => 'พันจ่าโท', 'sort_order' => 31],
            ['name' => 'พ.จ.ท.หญิง', 'full_name' => 'พันจ่าโทหญิง', 'sort_order' => 32],
            ['name' => 'พ.จ.ต.', 'full_name' => 'พันจ่าตรี', 'sort_order' => 33],
            ['name' => 'พ.จ.ต.หญิง', 'full_name' => 'พันจ่าตรีหญิง', 'sort_order' => 34],
            ['name' => 'จ.อ.', 'full_name' => 'จ่าเอก', 'sort_order' => 35],
            ['name' => 'จ.อ.หญิง', 'full_name' => 'จ่าเอกหญิง', 'sort_order' => 36],
            ['name' => 'จ.ท.', 'full_name' => 'จ่าโท', 'sort_order' => 37],
            ['name' => 'จ.ท.หญิง', 'full_name' => 'จ่าโทหญิง', 'sort_order' => 38],
            ['name' => 'จ.ต.', 'full_name' => 'จ่าตรี', 'sort_order' => 39],
            ['name' => 'จ.ต.หญิง', 'full_name' => 'จ่าตรีหญิง', 'sort_order' => 40],
            ['name' => 'พลฯ', 'full_name' => 'พลทหาร', 'sort_order' => 41],
            ['name' => 'นาย', 'full_name' => 'นาย', 'sort_order' => 42],
            ['name' => 'นาง', 'full_name' => 'นาง', 'sort_order' => 43],
            ['name' => 'นางสาว', 'full_name' => 'นางสาว', 'sort_order' => 44],
        ];

        foreach ($ranks as $rank) {
            \App\Models\Rank::create($rank);
        }
    }
}
