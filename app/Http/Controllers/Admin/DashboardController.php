<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $events       = Event::withCount(['entries' => fn ($q) => $q->where('status', 'confirmed')])->latest()->take(5)->get();
        $recentEntries = Entry::with(['event', 'slot'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('events', 'recentEntries'));
    }
}
