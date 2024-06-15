<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            ['name' => 'First Lesson Watched', 'type' => 'lesson', 'count' => 1],
            ['name' => '5 Lessons Watched', 'type' => 'lesson', 'count' => 5],
            ['name' => '10 Lessons Watched', 'type' => 'lesson', 'count' => 10],
            ['name' => '25 Lessons Watched', 'type' => 'lesson', 'count' => 25],
            ['name' => '50 Lessons Watched', 'type' => 'lesson', 'count' => 50],
            ['name' => 'First Comment Written', 'type' => 'comment', 'count' => 1],
            ['name' => '3 Comments Written', 'type' => 'comment', 'count' => 3],
            ['name' => '5 Comments Written', 'type' => 'comment', 'count' => 5],
            ['name' => '10 Comments Written', 'type' => 'comment', 'count' => 10],
            ['name' => '20 Comments Written', 'type' => 'comment', 'count' => 20],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate($achievement);
        }

    }
}
