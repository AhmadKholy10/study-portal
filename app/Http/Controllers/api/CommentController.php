<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $comment = $lesson->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Check for achievements
        $this->checkAchievements(auth()->user(), 'comment');

        return response()->json(['message' => 'Comment added', 'comment' => $comment]);
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
                $this->sendEmail($user, $achievement);
            }
        }
        $user->updateBadge();
    }

    public function sendEmail($user, $achievement){
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'message' => 'You unlocked \"'. $achievement->name . '\"' ,
        ];

        Mail::to($user->email)->send(new AchievementUnlocked($data));

        //return response()->json(['message' => 'Email sent successfully'], 200);
    }

}
