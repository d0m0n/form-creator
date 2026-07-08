<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount(['entries' => fn ($q) => $q->where('status', 'confirmed')])->latest()->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(EventRequest $request)
    {
        $data = collect($request->validated())
            ->except(['header_image', 'remove_header_image'])
            ->toArray();

        if ($request->hasFile('header_image')) {
            $data['header_image'] = $request->file('header_image')->store('events', 'public');
        }

        $event = Event::create([...$data, 'created_by' => Auth::guard('admin')->id()]);
        return redirect()->route('admin.events.show', $event)->with('success', 'イベントを作成しました。');
    }

    public function show(Event $event)
    {
        $slots = $event->slots()->withCount(['entries' => fn ($q) => $q->where('status', 'confirmed')])->get();
        return view('admin.events.show', compact('event', 'slots'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $data = collect($request->validated())
            ->except(['header_image', 'remove_header_image'])
            ->toArray();

        if ($request->boolean('remove_header_image')) {
            $this->deleteHeaderImage($event);
            $data['header_image'] = null;
        } elseif ($request->hasFile('header_image')) {
            $this->deleteHeaderImage($event);
            $data['header_image'] = $request->file('header_image')->store('events', 'public');
        }

        $event->update($data);
        return redirect()->route('admin.events.show', $event)->with('success', 'イベントを更新しました。');
    }

    public function destroy(Event $event)
    {
        if ($event->entries()->where('status', 'confirmed')->exists()) {
            return back()->with('error', '申込済みの枠があるため削除できません。ステータスを「受付終了」に変更してください。');
        }
        $this->deleteHeaderImage($event);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'イベントを削除しました。');
    }

    private function deleteHeaderImage(Event $event): void
    {
        if ($event->header_image) {
            Storage::disk('public')->delete($event->header_image);
        }
    }
}
