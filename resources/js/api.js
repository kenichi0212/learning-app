//HTMLのmetaタグからCSRFトークンを取得
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        console.error('CSRF token not found in meta tag');
    }
    return token;
};

//共通のheadersを生成する関数
const getHeaders = () => ({
    'X-CSRF-TOKEN': getCsrfToken(),
    'Content-Type': 'application/json',
    'Accept': 'application/json'
});

//学習セッションの開始
export async function apiStartSession() {
    const res = await fetch("/learning/start", {
        method: 'POST',
        headers: getHeaders()
    });

    const data = await res.json();

    //その変数を使って成功・失敗を判定
    if (!res.ok) {
        //dataの中身をそのまま使う。
        throw new Error(data.message || "セッションの開始に失敗しました。");
    }
    //成功した場合はdataを返す
    return data;
}

//学習セッションの更新
export async function apiUpdateSession(sessionId, isLearning, hasFace = true, screenChanged = true, interval = 10) {
    if (!sessionId) return;
    
    const res = await fetch(`/learning/update/${sessionId}`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
            is_learning: isLearning,
            has_face: hasFace,
            screen_changed: screenChanged,
            interval: interval
        })
    });
    
    if (!res.ok) {
        const data = await res.json();
        throw new Error(data.message || "セッションの更新に失敗しました。");
    }
    
    return await res.json();
}

//学習セッションの停止
export async function apiStopSession(sessionId) {
    if (!sessionId) {
        throw new Error("セッションIDが取得できていません。");
    }
    
    const res = await fetch(`/learning/stop/${sessionId}`, {
        method: 'POST',
        headers: getHeaders()
    });
    
    if (!res.ok) {
        const text = await res.text();
        throw new Error(text || "停止APIエラー");
    }
    
    const result = await res.json();
    
    if (!result.success) {
        throw new Error("処理に失敗しました");
    }
    
    return result;
}