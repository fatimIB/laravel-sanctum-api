<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    // Method to initiate password reset
    public function requestReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // Generate a random 4-digit code


        // Save the code to the database
        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            ['token' => $code]
        );

        // Send the code to the user's email
        $this->sendResetCodeEmail($email, $code);

        return response()->json(['message' => 'Verification code sent to your email'], 200);
    }

    // Method to reset the password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:4',
            'password' => 'required|min:6',
        ]);

        $email = $request->email;
        $code = $request->code;
        $password = $request->password;

        // Check if the code is valid
        $token = PasswordResetToken::where('email', $email)
            ->where('token', $code)
            ->first();

        if (!$token) {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }

        // Update the user's password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($password);
        $user->save();

        // Delete the token from the database
        $token->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    // Method to send reset code email
    private function sendResetCodeEmail($email, $code)
    {
        // You can implement email sending logic here
        // For example, using Laravel Mail
        // Here's a simplified example
        Mail::raw("Your password reset code is: $code", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Verification Code');
        });
    }
}
