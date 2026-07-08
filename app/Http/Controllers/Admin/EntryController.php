<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class EntryController extends Controller
{
    public function index(Event $event)
    {
        $entries = $event->entries()->with(['slot', 'members'])->latest()->paginate(30);
        return view('admin.entries.index', compact('event', 'entries'));
    }

    public function participants(Request $request, Event $event)
    {
        $slots = $event->slots()->orderBy('game_date')->orderBy('start_time')->get();

        $members = $event->members()
            ->with(['entry' => fn ($q) => $q->with('slot')])
            ->when($request->slot_id, fn ($q, $slotId) => $q->where('entries.slot_id', $slotId))
            ->when($request->status !== 'all', fn ($q) => $q->where('entries.status', 'confirmed'))
            ->orderBy('entries.slot_id')
            ->orderBy('entry_members.entry_id')
            ->orderBy('entry_members.sort_order')
            ->paginate(100)
            ->withQueryString();

        return view('admin.entries.participants', compact('event', 'slots', 'members'));
    }

    public function show(Event $event, Entry $entry)
    {
        $entry->load(['slot', 'members']);
        return view('admin.entries.show', compact('event', 'entry'));
    }

    public function updateStatus(Request $request, Event $event, Entry $entry)
    {
        $request->validate(['status' => ['required', Rule::in(['confirmed', 'cancelled'])]]);
        $entry->update(['status' => $request->status]);
        return back()->with('success', 'ステータスを更新しました。');
    }
}
