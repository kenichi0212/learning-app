//HTMLのmetaタグからCSRFトークンを取得
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.textContent;
};

//共通のfetchオプションを生成
const headers = {
    'X-CSRF-TOKEN': getCsrfToken(),
    'Content-Type': 'application/json',
    'Accept': 'application/json'
};

//学習セッションの開始
export async function apiStartSession() {
    const res = await fetch("/learning/start", {
        method: 'POST',
        headers: headers
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