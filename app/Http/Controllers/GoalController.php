<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class GoalController extends Controller
{
    /**
     * 目標を保存する
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. バリデーション（入力チェック）
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date'],
            'stop_doing' => ['nullable', 'string', 'max:500'],
            'if_then_normal' => ['nullable', 'string', 'max:500'],
            'if_then_busy' => ['nullable', 'string', 'max:500'],
        ]);

        // 2. ログインユーザーに紐づけてGoalレコードを作成または更新
$request->user()->goal()->updateOrCreate(
    ['user_id' => $request->user()->id],
    $request->only(['title', 'deadline', 'stop_doing', 'if_then_normal', 'if_then_busy'])
);

        // 3. 【重要】プロフィール編集画面へリダイレクト
        // 'status' に 'goal-updated' を渡すと、Breezeの仕組みで「Saved.」と表示しやすくなります
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * 目標を削除する（将来的にプロフィール画面から消せるように用意）
     */
    public function destroy(Goal $goal): RedirectResponse
    {
        // 自分の目標かチェックして削除
        if ($goal->user_id === Auth::id()) {
            $goal->delete();
        }

        return redirect()->route('profile.edit')->with('status', 'ゴール設定を削除しました');
    }
}