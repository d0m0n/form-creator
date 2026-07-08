<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class ExportController extends Controller
{
    public function participants(Event $event)
    {
        $members = $event->members()
            ->with(['entry' => fn ($q) => $q->with('slot')])
            ->where('entries.status', 'confirmed')
            ->orderBy('entries.slot_id')
            ->orderBy('entry_members.entry_id')
            ->orderBy('entry_members.sort_order')
            ->get();

        $filename = $event->slug . '_participants_' . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($members) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            fputcsv($out, ['受付番号', '開催日', '枠名', '開始', '終了', '代表者氏名', '電話番号', '氏名', '年齢', '性別']);

            $genderMap = ['male' => '男性', 'female' => '女性', 'other' => 'その他'];

            foreach ($members as $member) {
                $entry = $member->entry;
                fputcsv($out, [
                    $entry->entry_no,
                    $entry->slot->game_date->format('Y/m/d'),
                    $entry->slot->name,
                    $entry->slot->start_time,
                    $entry->slot->end_time,
                    $entry->rep_name,
                    $entry->rep_phone,
                    $member->name,
                    $member->age,
                    $genderMap[$member->gender] ?? $member->gender,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function entries(Event $event)
    {
        $entries = $event->entries()->with(['slot', 'members'])->where('status', 'confirmed')->get();

        $filename = $event->slug . '_entries_' . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($entries, $event) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            $header = ['受付番号', '開催日', '枠名', '開始', '終了', '代表者氏名', '電話番号', 'メール', 'ステータス'];
            for ($i = 1; $i <= $event->member_count; $i++) {
                $header[] = "メンバー{$i}氏名";
                $header[] = "メンバー{$i}年齢";
                $header[] = "メンバー{$i}性別";
            }
            fputcsv($out, $header);

            $genderMap = ['male' => '男性', 'female' => '女性', 'other' => 'その他'];

            foreach ($entries as $entry) {
                $row = [
                    $entry->entry_no,
                    $entry->slot->game_date->format('Y/m/d'),
                    $entry->slot->name,
                    $entry->slot->start_time,
                    $entry->slot->end_time,
                    $entry->rep_name,
                    $entry->rep_phone,
                    $entry->email,
                    $entry->status === 'confirmed' ? '確認済み' : 'キャンセル',
                ];
                foreach ($entry->members as $member) {
                    $row[] = $member->name;
                    $row[] = $member->age;
                    $row[] = $genderMap[$member->gender] ?? $member->gender;
                }
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
