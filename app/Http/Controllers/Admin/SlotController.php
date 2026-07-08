<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlotRequest;
use App\Models\Event;
use App\Models\Slot;

class SlotController extends Controller
{
    public function create(Event $event)
    {
        return view('admin.slots.create', compact('event'));
    }

    public function store(SlotRequest $request, Event $event)
    {
        $event->slots()->create($request->validated());
        return redirect()->route('admin.events.show', $event)->with('success', '時間枠を追加しました。');
    }

    public function bulk(\Illuminate\Http\Request $request, Event $event)
    {
        $rows = $request->validate([
            'slots'              => ['required', 'array', 'min:1'],
            'slots.*.game_date'  => ['required', 'date'],
            'slots.*.name'       => ['required', 'string', 'max:100'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time'   => ['required', 'date_format:H:i'],
            'slots.*.capacity'   => ['required', 'integer', 'min:1', 'max:999'],
        ])['slots'];

        foreach ($rows as $row) {
            $event->slots()->create([
                'game_date'  => $row['game_date'],
                'name'       => $row['name'],
                'start_time' => $row['start_time'],
                'end_time'   => $row['end_time'],
                'capacity'   => (int) $row['capacity'],
                'is_active'  => true,
            ]);
        }

        return redirect()->route('admin.events.show', $event)->with('success', '時間枠を追加しました。');
    }

    public function edit(Event $event, Slot $slot)
    {
        return view('admin.slots.edit', compact('event', 'slot'));
    }

    public function update(SlotRequest $request, Event $event, Slot $slot)
    {
        $slot->update($request->validated());
        return redirect()->route('admin.events.show', $event)->with('success', '時間枠を更新しました。');
    }

    public function destroy(Event $event, Slot $slot)
    {
        if ($slot->entries()->where('status', 'confirmed')->exists()) {
            return back()->with('error', '申込済みのため削除できません。');
        }
        $slot->delete();
        return redirect()->route('admin.events.show', $event)->with('success', '時間枠を削除しました。');
    }
}
