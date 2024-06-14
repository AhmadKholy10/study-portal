<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function watch(Lesson $lesson){
        $user = auth()->user();

        //check if the user wathced this lesson before
        if ($user->lessons()->where('lesson_id', $lesson->id)->exists()) {
            return response()->json(['message' => 'Already watched this lesson'], 400);
        }

        $user->lessons()->attach($lesson->id); // add the lesson to user's watched list

    // Check for lesson-watched achievements
    $lessonWatchedCount = $user->lessons()->count();
    $this->checkAchievements($user, 'lesson');

    return response()->json(['message' => 'Lesson marked as watched']);
    }

    protected function checkAchievements($user, $type)
    {
        $counts = [
            'lesson' => $user->lessons()->count(),
            'comment' => $user->comments()->count(),
        ];

        $achievements = Achievement::where('type', $type)
            ->where('count', '<=', $counts[$type])
            ->get();

        foreach ($achievements as $achievement) {
            if (!$user->achievements->contains($achievement)) {
                $user->achievements()->attach($achievement);
                //$user->notify(new AchievementUnlocked($achievement));
            }
        }
    }
}
