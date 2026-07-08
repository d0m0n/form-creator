<?php

namespace App\Http\Controllers;

use App\Models\GuestEmailVerification;
use App\Models\GuestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestEmailVerificationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $guest = $request->attributes->get('guest_user');
        abort_unless($guest, 422);

        $guest->emailVerifications()->delete();

        $token = Str::random(64);
        $guest->emailVerifications()->create([
            'email'        => $request->email,
            'verify_token' => $token,
            'expires_at'   => now()->addMinutes(15),
        ]);

        \Illuminate\Support\Facades\Mail::to($request->email)
            ->send(new \App\Mail\GuestEmailVerification($guest, $token));

        return back()->with('success', '確認メールを送信しました。');
    }

    public function verify(Request $request, string $verifyToken)
    {
        $verification = GuestEmailVerification::where('verify_token', $verifyToken)->firstOrFail();
        abort_if($verification->isExpired(), 410, '認証リンクの有効期限が切れています。');

        $verification->guestUser->update([
            'email'             => $verification->email,
            'email_verified'    => true,
            'email_verified_at' => now(),
        ]);
        $verification->delete();

        return redirect()->route('guest.event.create')->with('success', 'メールアドレスを確認しました。');
    }
}
