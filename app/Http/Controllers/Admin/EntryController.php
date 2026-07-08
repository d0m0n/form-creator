<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntryController extends Controller
{
    public function index(Event $event)
    {
        $entries = $event->entries()->with(['slot', 'members'])->latest()->paginate(30);
        return view('admin.entries.index', compact('event', 'entries'));
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
