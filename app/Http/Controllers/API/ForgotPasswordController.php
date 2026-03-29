<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // STEP 1: Send Reset Link
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Create/reset token
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send email
        Mail::send('emails.reset-password', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset Your Password');
        });

        return response()->json(['message' => 'Password reset link sent to email']);
    }

    // STEP 2: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $check = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $check) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => bcrypt($request->password)
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successful']);
    }
}
