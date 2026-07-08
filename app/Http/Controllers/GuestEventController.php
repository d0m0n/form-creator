<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Models\GuestUser;
use Illuminate\Http\Request;

class GuestEventController extends Controller
{
    private function resolveGuestUser(Request $request): ?GuestUser
    {
        return $request->attributes->get('guest_user');
    }

    private function authorizeOwner(Request $request, Event $event): void
    {
        $guest = $this->resolveGuestUser($request);
        abort_unless($guest && $event->guestOwners()->where('guest_users.id', $guest->id)->exists(), 403);
    }

    public function create(Request $request)
    {
        $guest = $this->resolveGuestUser($request);
        if (! ($guest && $guest->email_verified)) {
            return view('guest.create');
        }
        return view('guest.event_form', ['event' => null]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $guest = $this->resolveGuestUser($request);
        abort_unless($guest, 422);

        $token = \Illuminate\Support\Str::random(64);
        $guest->emailVerifications()->create([
            'email'        => $request->email,
            'verify_token' => $token,
            'expires_at'   => now()->addMinutes(15),
        ]);

        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\GuestEmailVerification($guest, $token));

        return view('guest.verify_sent', ['email' => $request->email]);
    }

    public function confirm(Request $request, string $token)
    {
        $verification = \App\Models\GuestEmailVerification::where('verify_token', $token)->firstOrFail();
        abort_if($verification->isExpired(), 410);

        $verification->guestUser->update([
            'email'             => $verification->email,
            'email_verified'    => true,
            'email_verified_at' => now(),
        ]);
        $verification->delete();

        return redirect()->route('guest.event.create')->with('success', 'メールアドレスを確認しました。イベントを作成できます。');
    }

    public function store(EventRequest $request)
    {
        $guest = $this->resolveGuestUser($request);
        abort_unless($guest && $guest->email_verified, 403);

        $event = Event::create($request->validated());
        $event->guestOwners()->attach($guest->id);

        return redirect()->route('guest.event.show', $event)->with('success', 'イベントを作成しました。');
    }

    public function show(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);
        $slots = $event->slots()->withCount(['entries as confirmed_count' => fn ($q) => $q->where('status', 'confirmed')])->get();
        return view('guest.dashboard', compact('event', 'slots'));
    }

    public function edit(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);
        return view('guest.event_form', compact('event'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $this->authorizeOwner($request, $event);
        $event->update($request->validated());
        return redirect()->route('guest.event.show', $event)->with('success', 'イベントを更新しました。');
    }

    public function entries(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);
        $entries = $event->entries()->with(['slot', 'members'])->latest()->paginate(30);
        return view('guest.entries', compact('event', 'entries'));
    }

    public function export(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);
        return app(Admin\ExportController::class)->entries($event);
    }
}
