<?php

namespace App\Http\Middleware;

use App\Models\GuestUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentifyGuestUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('guest_token');

        if ($token) {
            $guest = GuestUser::where('token', $token)->first();
            if ($guest) {
                $guest->update(['last_seen_at' => now()]);
                $request->attributes->set('guest_user', $guest);
            }
        }

        $response = $next($request);

        if (! $token) {
            $newToken = (string) Str::uuid();
            GuestUser::create(['token' => $newToken]);
            $response->withCookie(cookie('guest_token', $newToken, 60 * 24 * 30, '/', null, true, true, false, 'Strict'));
        }

        return $response;
    }
}
