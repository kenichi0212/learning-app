// タイマー管理モジュール

// タイマー管理用の状態
let timerState = {
    seconds: 0,
    accumulatedSeconds: 0,
    lastUpdateSeconds: 0,
    timerInterval: null,
    isMonitoring: false
};

/**
 * タイマーの表示を更新
 * @param {HTMLElement} timerDisplay - タイマー表示要素
 */
export function updateTimerText(timerDisplay) {
    const h = String(Math.floor(timerState.seconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((timerState.seconds % 3600) / 60)).padStart(2, '0');
    const s = String(timerState.seconds % 60).padStart(2, '0');
    timerDisplay.innerText = `${h}:${m}:${s}`;
    timerDisplay.classList.remove('text-gray-400');
    timerDisplay.classList.add('text-black');
}

/**
 * タイマーUIを開始
 * @param {HTMLElement} timerDisplay - タイマー表示要素
 * @param {function} isMonitoringGetter - isMonitoringの状態を取得する関数
 */
export function startTimerUI(timerDisplay, isMonitoringGetter) {
    if (timerState.timerInterval) clearInterval(timerState.timerInterval);
    
    timerState.timerInterval = setInterval(() => {
        if (isMonitoringGetter()) {
            timerState.seconds++;
            updateTimerText(timerDisplay);
        }
    }, 1000);
}

/**
 * タイマーを停止
 */
export function stopTimer() {
    if (timerState.timerInterval) {
        clearInterval(timerState.timerInterval);
        timerState.timerInterval = null;
    }
}

/**
 * 現在の秒数を取得
 */
export function getSeconds() {
    return timerState.seconds;
}

/**
 * 最後に更新した秒数を取得
 */
export function getLastUpdateSeconds() {
    return timerState.lastUpdateSeconds;
}

/**
 * 最後に更新した秒数を設定
 */
export function setLastUpdateSeconds(value) {
    timerState.lastUpdateSeconds = value;
}

/**
 * タイマーをリセット
 */
export function resetTimer() {
    stopTimer();
    timerState.seconds = 0;
    timerState.accumulatedSeconds = 0;
    timerState.lastUpdateSeconds = 0;
}

/**
 * タイマーの状態を取得（デバッグ用）
 */
export function getTimerState() {
    return { ...timerState };
}
