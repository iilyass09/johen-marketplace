<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatAdmin;
use App\Models\LiveChatChannel;
use App\Models\LiveChatOperator;
use App\Models\LiveChatOperatorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LiveChatAdminController extends Controller
{
    public function index()
    {
        $channels = LiveChatChannel::with(['admins.user', 'operators.schedules'])
            ->ordered()
            ->get();

        return view('admin.live-chat.admins.index', compact('channels'));
    }

    public function edit(LiveChatChannel $channel)
    {
        $channel->load(['admins.user', 'operators.schedules']);

        return view('admin.live-chat.admins.edit', compact('channel'));
    }

    public function update(Request $request, LiveChatChannel $channel)
    {
        $request->validate([
            'admin_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'operators' => 'nullable|array|max:10',
            'operators.*.name' => 'required_with:operators|string|max:255',
            'operators.*.start_time' => 'required_with:operators|date_format:H:i',
            'operators.*.end_time' => 'required_with:operators|date_format:H:i',
        ]);

        DB::beginTransaction();

        try {
            $admin = LiveChatAdmin::firstOrNew(['channel_id' => $channel->id]);
            $admin->channel_id = $channel->id;
            $admin->is_active = $request->boolean('admin_is_active');

            if ($request->hasFile('admin_photo')) {
                if ($admin->photo_path && Storage::disk('public')->exists($admin->photo_path)) {
                    Storage::disk('public')->delete($admin->photo_path);
                }
                $admin->photo_path = $request->file('admin_photo')->store('live-chat/admins', 'public');
            }

            $admin->save();

            LiveChatOperator::where('channel_id', $channel->id)
                ->whereNull('user_id')
                ->each(function ($op) {
                    $op->schedules()->delete();
                    $op->delete();
                });

            $operators = $request->input('operators', []);
            foreach ($operators as $opData) {
                if (empty($opData['name'])) continue;

                $operator = LiveChatOperator::create([
                    'name' => $opData['name'],
                    'channel_id' => $channel->id,
                    'is_active' => true,
                ]);

                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                foreach ($days as $day) {
                    LiveChatOperatorSchedule::create([
                        'operator_id' => $operator->id,
                        'day_of_week' => $day,
                        'start_time' => $opData['start_time'],
                        'end_time' => $opData['end_time'],
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.live-chat.admins')
                ->with('success', 'Admin Live Chat berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
