<?php

namespace App\Http\Controllers;

use App\Models\LearningSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;


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
            ->firstOrFail();

        // フロントから送られてきた実際の経過時間（秒）を取得
        // 一時停止や離席中は送られないので、その時間は除外される
        $interval = $request->input('interval', 10); // デフォルト10秒

        //総学習時間を加算
        $session->total_duration += $interval;

        //フロントから送られてきた学習判定がtrueの場合、有効学習時間を加算
        if ($request->input('is_learning')) {
            $session->effective_duration += $interval;
        }
        $session->save();

        //学習ログの記録
        \App\Models\LearningLog::create([
            'learning_session_id' => $id,
            'captured_at' => now(),
            'is_away' => !$request->input('has_face'), // 顔認証の結果
            'is_changed' => $request->input('screen_changed'), // 画面変化の結果
        ]);

        return response()->json([
            'success' => true,
            'total' => $session->total_duration,
            'current_effective' => $session->effective_duration
        ]);
    }

    //sessionの終了
    public function stop($id)
    {
        try {
            $session = LearningSession::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'セッションが見つかりません',
                ], 404);
            }

            $endTime = now();

            // start_at と end_at の差分（秒）。Carbon の diffInSeconds は絶対値を返す。
            $totalSeconds = $session->start_at ? $session->start_at->diffInSeconds($endTime) : 0;

            $awayLogsCount = \App\Models\LearningLog::where('learning_session_id', $id)
                ->where('is_away', true)
                ->count();

            //離席した秒数
            $awaySeconds = $awayLogsCount * 10;

            $effectiveSeconds = max(0, (int)$totalSeconds - $awaySeconds);

            $session->update([
                'end_at' => $endTime,
                'total_duration' => max(0, (int)$totalSeconds),
                'session_status' => 'finished',
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '終了処理でエラーが発生しました',
            ], 500);
        }
    }

    //学習セッション履歴の表示
    public function index(Request $request)
    {
        //クエリ作成
        $query = LearningSession::where('user_id', Auth::id());

        //日付検索が送られてきたら、条件を付けたす。
        if ($request->filled('date')) {
            $query->whereDate('start_at', $request->input('date'));
        }

        //ページネーションで取得
        $sessions = $query->orderBy('start_at', 'desc')->paginate(10);

        return view('learning-sessions.index', compact('sessions'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 期間ごとの集計（秒単位で取得し、分に変換）
        $stats = [
            'total' => LearningSession::where('user_id', $user->id)->sum('effective_duration'),
            'today' => LearningSession::where('user_id', $user->id)->whereDate('start_at', today())->sum('effective_duration'),
            'week'  => LearningSession::where('user_id', $user->id)->whereBetween('start_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->sum('effective_duration'),
            'month' => LearningSession::where('user_id', $user->id)->whereMonth('start_at', $now->month)->whereYear('start_at', $now->year)->sum('effective_duration'),
            'last_month' => LearningSession::where('user_id', $user->id)->whereMonth('start_at', $now->copy()->subMonth()->month)->whereYear('start_at', $now->copy()->subMonth()->year)->sum('effective_duration'),
        ];

        // 直近5件のセッション
        $recentSessions = LearningSession::where('user_id', $user->id)
            ->where('session_status', 'finished')
            ->orderBy('start_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentSessions'));
    }

    // 合計秒数を「○時間○分」の文字列で返すメソッド
    public static function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}時間{$minutes}分";
        }
        return "{$minutes}分";
    }
}
