<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    //     public function userAchievements(User $user)
    //     {
    //         // Retrieve the user's unlocked achievements
    //         $unlockedAchievements = $user->achievements()->pluck('name');

    //         return response()->json([
    //             'unlocked_achievements' => $unlockedAchievements
    //         ]);
    //     }

    public function userAchievements(User $user)
    {
        // Retrieve all possible achievements from the database
        $lessonAchievements = Achievement::where('type', 'lesson')->pluck('name')->toArray();
        $commentAchievements = Achievement::where('type', 'comment')->pluck('name')->toArray();

        // Retrieve the user's unlocked achievements
        $unlockedAchievements = $user->achievements()->pluck('name')->toArray();

        // Determine the next available achievements
        $nextAvailableAchievements = $this->getNextAvailableAchievements($lessonAchievements, $commentAchievements, $unlockedAchievements);

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements
        ]);
    }

    private function getNextAvailableAchievements($lessonAchievements, $commentAchievements, $unlockedAchievements)
    {
        $nextAvailableAchievements = [];

        // Find next lesson achievement
        foreach ($lessonAchievements as $achievement) {
            if (!in_array($achievement, $unlockedAchievements)) {
                $nextAvailableAchievements[] = $achievement;
                break;
            }
        }

        // Find next comment achievement
        foreach ($commentAchievements as $achievement) {
            if (!in_array($achievement, $unlockedAchievements)) {
                $nextAvailableAchievements[] = $achievement;
                break;
            }
        }

        return $nextAvailableAchievements;
    }
}
