<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatChannel;
use App\Models\LiveChatOperator;
use App\Models\LiveChatOperatorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveChatOperatorController extends Controller
{
    public function index()
    {
        $operators = LiveChatOperator::with(['user', 'channel', 'schedules'])->get();

        $channels = LiveChatChannel::active()->ordered()->get();

        $availableUsers = User::where('is_admin', false)
            ->where('is_live_chat_admin', false)
            ->whereDoesntHave('assignedOperators')
            ->get();

        return view('admin.live-chat.operators.index', compact('operators', 'channels', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'channel_id' => 'required|exists:live_chat_channels,id',
        ]);

        $existing = LiveChatOperator::where('user_id', $request->user_id)
            ->where('channel_id', $request->channel_id)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'User sudah menjadi operator di channel ini'], 422);
        }

        $channelOperatorCount = LiveChatOperator::where('channel_id', $request->channel_id)
            ->count();

        if ($channelOperatorCount >= 10) {
            return response()->json(['error' => 'Maksimal 10 operator per channel'], 422);
        }

        LiveChatOperator::create([
            'user_id' => $request->user_id,
            'channel_id' => $request->channel_id,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Operator berhasil ditambahkan']);
    }

    public function toggle(LiveChatOperator $operator)
    {
        $operator->update(['is_active' => !$operator->is_active]);

        return response()->json(['success' => true, 'is_active' => $operator->is_active]);
    }

    public function destroy(LiveChatOperator $operator)
    {
        if ($operator->user_id) {
            return response()->json(['error' => 'Operator akun tidak dapat dihapus. Ubah role akun di menu Users jika ingin melepas channel.'], 422);
        }

        $operator->schedules()->delete();
        $operator->delete();

        return response()->json(['success' => true, 'message' => 'Operator berhasil dihapus']);
    }

    public function schedule(Request $request, LiveChatOperator $operator)
    {
        $request->validate([
            'schedules' => 'required|array|min:1|max:7',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
            'schedules.*.is_active' => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($request, $operator) {
            $operator->schedules()->delete();

            foreach ($request->schedules as $schedule) {
                LiveChatOperatorSchedule::create([
                    'operator_id' => $operator->id,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'is_active' => $schedule['is_active'] ?? true,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan']);
    }
}
