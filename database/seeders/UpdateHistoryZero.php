<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\History;

class UpdateHistoryZero extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $histories = History::where('type_history', 2)->where('user_id', $i)->get();
            foreach ($histories as $history) {
                if (!$history->scan->is_valid) {
                    $history->delete();
                    continue;
                }
            }
        }
    }
}
