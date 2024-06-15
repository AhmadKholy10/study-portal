<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\Comment;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed achievements
        $this->seedAchievements();
    }

    private function seedAchievements()
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
            Achievement::create($achievement);
        }
    }

    private function unlockAchievements(User $user, $achievementNames)
    {
        foreach ($achievementNames as $achievementName) {
            $achievement = Achievement::where('name', $achievementName)->first();
            $user->achievements()->attach($achievement);
        }

        $user->updateBadge();
    }

    public function test_user_achievements_endpoint_returns_correct_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [],
                'next_available_achievements' => ['First Lesson Watched', 'First Comment Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 4,
            ]);
    }

    public function test_user_unlocks_first_lesson_achievement()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        // Simulate watching a lesson
        $user->lessons()->attach($lesson);

        $this->unlockAchievements($user, ['First Lesson Watched']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched'],
                'next_available_achievements' => ['5 Lessons Watched', 'First Comment Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 3,
            ]);
    }

    public function test_user_unlocks_multiple_achievements()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        // Simulate watching multiple lessons
        for ($i = 0; $i < 5; $i++) {
            $user->lessons()->attach($lesson);
        }

        $this->unlockAchievements($user, ['First Lesson Watched', '5 Lessons Watched']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched'],
                'next_available_achievements' => ['10 Lessons Watched', 'First Comment Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 2,
            ]);
    }

    public function test_user_unlocks_comment_achievement()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        // Simulate writing a comment
        Comment::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'comment' => 'Great lesson!'
        ]);

        $this->unlockAchievements($user, ['First Comment Written']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written'],
                'next_available_achievements' => ['First Lesson Watched', '3 Comments Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 3,
            ]);
    }

    public function test_user_badge_transitions()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        // Simulate watching lessons to unlock achievements
        for ($i = 0; $i < 10; $i++) {
            $user->lessons()->attach($lesson);
        }

        // Unlock achievements to transition to Intermediate badge
        $this->unlockAchievements($user, [
            'First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', 'First Comment Written'
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'current_badge' => 'Intermediate',
                'next_badge' => 'Advanced',
                'remaining_to_unlock_next_badge' => 4,
            ]);

        // Unlock more achievements to transition to Advanced badge
        $this->unlockAchievements($user, [
            '25 Lessons Watched', '3 Comments Written', '5 Comments Written', '10 Comments Written'
        ]);

        $user->updateBadge();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'current_badge' => 'Advanced',
                'next_badge' => 'Master',
                'remaining_to_unlock_next_badge' => 2,
            ]);

        // Unlock more achievements to transition to Master badge
        $this->unlockAchievements($user, [
            '50 Lessons Watched', '20 Comments Written'
        ]);

        $user->updateBadge();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/users/{$user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'current_badge' => 'Master',
                'next_badge' => 'Master',
                'remaining_to_unlock_next_badge' => 0,
            ]);
    }
}
