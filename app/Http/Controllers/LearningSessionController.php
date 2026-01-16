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

    //5分おきの更新通知（API的役割）
    public function update(Request $request, $id)
    {
        $session = LearningSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrfail();
        
        //5分(300秒)間隔のチェック
        $interval = 300;

        //総学習時間を加算
        $session->total_duration += $interval;

        //フロントから送られてきた学習判定がtrueの場合、有効学習時間を加算
        if ($request->input('is_learning')) {
            $session->effective_duration += $interval;
        }

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
