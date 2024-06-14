<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function userAchievements(User $user)
    {
        // Retrieve the user's unlocked achievements
        $unlockedAchievements = $user->achievements()->pluck('name')->toArray();

        // Get the next available achievements
        $nextAvailableAchievements = $this->getNextAvailableAchievements($unlockedAchievements);

        // Update the user's badge
        $user->updateBadge();

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $user->badge,
            'next_badge' => $this->getNextBadge($user->badge),
            'remaining_to_unlock_next_badge' => $this->getRemainingAchievements($user->badge, count($unlockedAchievements))
        ]);
    }

    private function getNextAvailableAchievements($unlockedAchievements)
    {
        $nextAvailableAchievements = [];

        // Retrieve the next lesson achievement
        $nextLessonAchievement = Achievement::where('type', 'lesson')
            ->whereNotIn('name', $unlockedAchievements)
            ->orderBy('id')
            ->first();

        // Retrieve the next comment achievement
        $nextCommentAchievement = Achievement::where('type', 'comment')
            ->whereNotIn('name', $unlockedAchievements)
            ->orderBy('id')
            ->first();

        if ($nextLessonAchievement) {
            $nextAvailableAchievements[] = $nextLessonAchievement->name;
        }

        if ($nextCommentAchievement) {
            $nextAvailableAchievements[] = $nextCommentAchievement->name;
        }

        return $nextAvailableAchievements;
    }

    private function getNextBadge($currentBadge)
    {
        switch ($currentBadge) {
            case 'Beginner':
                return 'Intermediate';
            case 'Intermediate':
                return 'Advanced';
            case 'Advanced':
                return 'Master';
            default:
                return 'Master';
        }
    }

    private function getRemainingAchievements($currentBadge, $currentAchievementCount)
    {
        switch ($currentBadge) {
            case 'Beginner':
                return 4 - $currentAchievementCount;
            case 'Intermediate':
                return 8 - $currentAchievementCount;
            case 'Advanced':
                return 10 - $currentAchievementCount;
            default:
                return 0;
        }
    }





















    //     public function userAchievements(User $user)
    //     {
    //         // Retrieve the user's unlocked achievements
    //         $unlockedAchievements = $user->achievements()->pluck('name');

    //         return response()->json([
    //             'unlocked_achievements' => $unlockedAchievements
    //         ]);
    //     }

    // public function userAchievements(User $user)
    // {
    //     // Retrieve all possible achievements from the database
    //     $lessonAchievements = Achievement::where('type', 'lesson')->pluck('name')->toArray();
    //     $commentAchievements = Achievement::where('type', 'comment')->pluck('name')->toArray();

    //     // Retrieve the user's unlocked achievements
    //     $unlockedAchievements = $user->achievements()->pluck('name')->toArray();

    //     // Determine the next available achievements
    //     $nextAvailableAchievements = $this->getNextAvailableAchievements($lessonAchievements, $commentAchievements, $unlockedAchievements);

    //     return response()->json([
    //         'unlocked_achievements' => $unlockedAchievements,
    //         'next_available_achievements' => $nextAvailableAchievements
    //     ]);
    // }

    // private function getNextAvailableAchievements($lessonAchievements, $commentAchievements, $unlockedAchievements)
    // {
    //     $nextAvailableAchievements = [];

    //     // Find next lesson achievement
    //     foreach ($lessonAchievements as $achievement) {
    //         if (!in_array($achievement, $unlockedAchievements)) {
    //             $nextAvailableAchievements[] = $achievement;
    //             break;
    //         }
    //     }

    //     // Find next comment achievement
    //     foreach ($commentAchievements as $achievement) {
    //         if (!in_array($achievement, $unlockedAchievements)) {
    //             $nextAvailableAchievements[] = $achievement;
    //             break;
    //         }
    //     }

    //     return $nextAvailableAchievements;
    // }
}
