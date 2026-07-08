<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $events = Event::withCount([
                'entries as entries_count' => fn ($q) => $q->where('status', 'confirmed'),
                'members as members_count' => fn ($q) => $q->where('entries.status', 'confirmed'),
            ])
            ->withSum(['slots as total_capacity' => fn ($q) => $q->where('is_active', true)], 'capacity')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('events'));
    }
}
