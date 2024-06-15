<?php

namespace App\Http\Controllers;

use App\Mail\AchievementUnlocked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(){
        $data = [
            'name' => 'Test name',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        Mail::to('adhammazen900@gmail.com')->send(new AchievementUnlocked($data));

        return response()->json(['message' => 'Email sent successfully'], 200);
    }
}
