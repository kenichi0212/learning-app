// UI制御モジュール
// ボタンの表示/非表示、状態管理を行う

/**
 * 学習開始時のボタン状態に切り替え
 * @param {HTMLElement} startBtn - 開始ボタン
 * @param {HTMLElement} pauseBtn - 一時停止ボタン
 * @param {HTMLElement} stopBtn - 停止ボタン
 */
export function showRunningButtons(startBtn, pauseBtn, stopBtn) {
    startBtn.classList.add('hidden');
    pauseBtn.classList.remove('hidden');
    stopBtn.classList.remove('hidden');
}

/**
 * 学習一時停止時のボタン状態に切り替え
 * @param {HTMLElement} startBtn - 開始ボタン
 * @param {HTMLElement} pauseBtn - 一時停止ボタン
 */
export function showPausedButtons(startBtn, pauseBtn) {
    pauseBtn.classList.add('hidden');
    startBtn.classList.remove('hidden');
    startBtn.disabled = false;
    startBtn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
    </svg>
    学習再開
`};

/**
 * 学習開始ボタンをローディング状態にする
 * @param {HTMLElement} startBtn - 開始ボタン
 */
export function setStartButtonLoading(startBtn) {
    startBtn.disabled = true;
    startBtn.innerText = "起動中...";
}

/**
 * 学習開始ボタンをエラー時の初期状態に戻す
 * @param {HTMLElement} startBtn - 開始ボタン
 */
export function resetStartButton(startBtn) {
    startBtn.disabled = false;
    startBtn.innerText = "▷ 学習開始";
}

/**
 * カメラUIの表示を更新
 * @param {HTMLElement} video - ビデオ要素
 * @param {HTMLElement} cameraOffUI - カメラOFF UI要素
 * @param {HTMLElement} cameraStatusText - カメラステータステキスト要素
 */
export function showCameraOn(video, cameraOffUI, cameraStatusText) {
    video.classList.remove('hidden');
    cameraOffUI.classList.add('hidden');
    cameraStatusText.innerText = 'ON';
}

/**
 * PiP再接続エリアを表示
 * @param {HTMLElement} pipReconnectArea - PiP再接続エリア要素
 */
export function showPipReconnect(pipReconnectArea) {
    pipReconnectArea.classList.remove('hidden');
}

/**
 * PiP再接続エリアを非表示
 * @param {HTMLElement} pipReconnectArea - PiP再接続エリア要素
 */
export function hidePipReconnect(pipReconnectArea) {
    pipReconnectArea.classList.add('hidden');
}
