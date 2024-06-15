<?php

use App\Http\Controllers\api\AchievementsController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\LessonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Middleware\EnsureAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->post('logout', [AuthController::class, 'logout']);

Route::middleware(['check_token'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/courses/{course}', [CourseController::class, 'enroll']);
    Route::post('/lesson/{lesson}', [LessonController::class, 'watch']);

    Route::post('comment/{lesson}', [CommentController::class, 'store']); //write a commnet
});

Route::get('users/{user}/achievements', [AchievementsController::class, 'userAchievements']);
Route::post('/send', [ContactController::class, 'send']);
