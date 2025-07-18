<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Send password reset email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        print_r($request->all());
        exit;

        try {
            $user = \App\Models\User::where('email', $request->email)->first();

            print_r($user);
            exit;

            Mail::to($user->email)->send(new PasswordResetMail($user->name, $user->remember_token));

                // return response()->json(true, 200);
            // return true;
            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
