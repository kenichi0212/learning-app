<?php

namespace App\Http\Controllers;

use App\Models\LearningSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LearningSessionController extends Controller
{
    /**
     * 学習画面の表示
     */
    public function show()
    {
        return view('learning-sessions.show');
    }

    //sessionの開始
    public function start(Request $request)
    {
        $session = LearningSession::create([
            'user_id' => Auth::id(),
            'start_at' => now(),
            'total_duration' => 0,
            'effective_duration' => 0,
            'session_status' => 'active',
        ]);
        return response()->json([
            'success' => true,
            'session_id' => $session->id
        ]);
    }

    //定期的な更新通知
    public function update(Request $request, $id)
    {
        $session = LearningSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrfail();

        // フロントから送られてきた実際の経過時間（秒）を取得
        // 一時停止や離席中は送られないので、その時間は除外される
        $interval = $request->input('interval', 10); // デフォルト10秒

        //総学習時間を加算
        $session->total_duration += $interval;

        //フロントから送られてきた学習判定がtrueの場合、有効学習時間を加算
        if ($request->input('is_learning')) {
            $session->effective_duration += $interval;
        }

        \App\Models\LearningLog::create([
            'learning_session_id' => $id,
            'captured_at' => now(),
            'is_away' => !$request->input('has_face'), // 顔認証の結果
            'is_changed' => $request->input('screen_changed'), // 画面変化の結果
        ]);
        $session->save();

        return response()->json([
            'success' => true,
            'total' => $session->total_duration,
            'current_effective' => $session->effective_duration
        ]);
    }

    //sessionの終了
    public function stop($id)
    {
        $session = LearningSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($session) {
            $session->update([
                'end_at' => now(),
                'session_status' => 'finished',
            ]);
        }

        return response()->json(['success' => true]);
    }
}
