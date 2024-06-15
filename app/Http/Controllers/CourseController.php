<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function enroll(Course $course)
    {
        $user = auth()->user();

        if ($user->courses()->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'Already enrolled in this course'], 400);
        }

        $user->courses()->attach($course->id);

        return response()->json(['message' => 'Enrolled successfully']);
    }
}
