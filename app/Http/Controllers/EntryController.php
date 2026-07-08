<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryRequest;
use App\Mail\EntryConfirmation;
use App\Mail\EntryNotification;
use App\Models\Entry;
use App\Models\Event;
use App\Models\GuestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EntryController extends Controller
{
    public function index(Event $event)
    {
        abort_unless($event->isOpen(), 404);

        if ($event->isDeadlinePassed()) {
            return view('entry.index', [
                'event'          => $event,
                'slots'          => collect(),
                'deadlinePassed' => true,
                'allFull'        => false,
            ]);
        }

        $slots = $event->activeSlots()
            ->withCount(['members as confirmed_count' => fn ($q) => $q->where('entries.status', 'confirmed')])
            ->get();

        $allFull = $slots->isNotEmpty()
            && $slots->every(fn ($slot) => $slot->confirmed_count >= $slot->capacity);

        return view('entry.index', compact('event', 'slots'))
            ->with(['deadlinePassed' => false, 'allFull' => $allFull]);
    }

    public function confirm(EntryRequest $request, Event $event)
    {
        abort_unless($event->isOpen(), 404);
        abort_if($event->isDeadlinePassed(), 422);
        $data = $request->validated();
        session(['entry_data' => $data]);

        $formToken = Str::random(32);
        session(['entry_form_token' => $formToken]);

        $slot = $event->slots()->findOrFail($data['slot_id']);
        return view('entry.confirm', compact('event', 'slot', 'data', 'formToken'));
    }

    public function submit(Request $request, Event $event)
    {
        abort_unless($event->isOpen(), 404);
        abort_if($event->isDeadlinePassed(), 422);

        // 使い捨てトークン検証（二重送信防止）
        $submittedToken = $request->input('entry_form_token');
        if (! $submittedToken || $submittedToken !== session('entry_form_token')) {
            return redirect()->route('entry.index', $event)
                ->withErrors(['slot_id' => '申込が既に送信されています。再度お申込みの場合は最初からやり直してください。']);
        }
        session()->forget('entry_form_token');

        $data = session('entry_data');
        abort_unless($data, 422);

        $entry = DB::transaction(function () use ($data, $event, $request) {
            $slot = $event->slots()
                ->where('id', $data['slot_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            if ($slot->isFull()) {
                return null;
            }

            $guestUser = $request->attributes->get('guest_user');

            $entry = Entry::create([
                'event_id'      => $event->id,
                'slot_id'       => $slot->id,
                'guest_user_id' => $guestUser?->id,
                'entry_no'      => $this->generateEntryNo($event),
                'edit_token'    => Str::random(64),
                'rep_name'      => $data['rep_name'],
                'rep_phone'     => $data['rep_phone'],
                'email'         => $data['email'],
                'status'        => 'confirmed',
            ]);

            foreach ($data['members'] as $i => $member) {
                $entry->members()->create([
                    'sort_order' => $i + 1,
                    'name'       => $member['name'],
                    'age'        => $member['age'],
                    'gender'     => $member['gender'],
                ]);
            }

            return $entry;
        });

        if (! $entry) {
            return redirect()->route('entry.index', $event)->withErrors(['slot_id' => 'この時間枠は満員になりました。別の枠を選択してください。']);
        }

        session()->forget('entry_data');

        $entry->load(['event', 'slot', 'members']);
        Mail::to($entry->email)->send(new EntryConfirmation($entry));

        if ($event->contact_email) {
            Mail::to($event->contact_email)->send(new EntryNotification($entry));
        }

        return redirect()->route('entry.complete', $event)->with('entry_no', $entry->entry_no);
    }

    public function complete(Event $event)
    {
        $entryNo = session('entry_no');
        abort_unless($entryNo, 404);
        return view('entry.complete', compact('event', 'entryNo'));
    }

    public function edit(Event $event, string $token)
    {
        $entry = Entry::where('edit_token', $token)->where('event_id', $event->id)->firstOrFail();
        abort_unless($entry->isCancellable(), 403);
        $slots = $event->activeSlots()
            ->withCount(['members as confirmed_count' => fn ($q) => $q->where('entries.status', 'confirmed')])
            ->get();
        return view('entry.edit', compact('event', 'entry', 'slots', 'token'));
    }

    public function update(EntryRequest $request, Event $event, string $token)
    {
        $entry = Entry::where('edit_token', $token)->where('event_id', $event->id)->firstOrFail();
        abort_unless($entry->isCancellable(), 403);

        DB::transaction(function () use ($request, $event, $entry) {
            $data = $request->validated();
            $slot = $event->slots()->where('id', $data['slot_id'])->where('is_active', true)->lockForUpdate()->firstOrFail();

            if ($slot->id !== $entry->slot_id && $slot->isFull()) {
                abort(422, 'この時間枠は満員です。');
            }

            $entry->update(['slot_id' => $slot->id, 'rep_name' => $data['rep_name'], 'rep_phone' => $data['rep_phone'], 'email' => $data['email']]);
            $entry->members()->delete();
            foreach ($data['members'] as $i => $member) {
                $entry->members()->create(['sort_order' => $i + 1, 'name' => $member['name'], 'age' => $member['age'], 'gender' => $member['gender']]);
            }
        });

        return redirect()->route('entry.edit', [$event, $token])->with('success', '申込内容を更新しました。');
    }

    public function cancel(Event $event, string $token)
    {
        $entry = Entry::where('edit_token', $token)->where('event_id', $event->id)->firstOrFail();
        abort_unless($entry->isCancellable(), 403);
        $entry->update(['status' => 'cancelled']);
        return redirect()->route('entry.index', $event)->with('success', '申込をキャンセルしました。');
    }

    private function generateEntryNo(Event $event): string
    {
        $prefix = strtoupper(substr($event->slug, 0, 3));
        $date   = now()->format('Ymd');
        $seq    = str_pad((string) ($event->entries()->max('id') + 1), 6, '0', STR_PAD_LEFT);
        return "{$prefix}-{$date}-{$seq}";
    }
}
