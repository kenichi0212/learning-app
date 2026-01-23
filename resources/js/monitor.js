// 監視機能モジュール
// AI判定と画面差分チェックを管理

// グローバル変数（show.blade.phpから参照される）
let lastScreenCanvas = null;
const DIFF_THRESHOLD = 0.1; // 画面変化の閾値（調整可能）

// TensorFlow.js モデルのロード
export async function loadModel() {
    if (typeof blazeface === 'undefined') {
        throw new Error('BlazeFace model is not loaded. Please include the script.');
    }
    const model = await blazeface.load();
    console.log("Model Loaded");
    return model;
}

// 画面変化検出
export function checkScreenDifference(screenStream) {
    if (!screenStream) return Promise.resolve(true); // 画面共有がない場合は常に変化ありとする

    const screenVideoTrack = screenStream.getVideoTracks()[0];
    const imageCapture = new ImageCapture(screenVideoTrack);

    return imageCapture.grabFrame().then(imageBitmap => {
        // Canvasに描画して比較
        const canvas = document.createElement('canvas');
        canvas.width = 160;
        canvas.height = 90;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(imageBitmap, 0, 0, canvas.width, canvas.height);

        const currentData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

        if (!lastScreenCanvas) {
            // 前回のフレームと比較
            lastScreenCanvas = currentData;
            return true; // 最初は変化ありとする
        }

        let diffPixels = 0;
        for (let i = 0; i < currentData.length; i += 4) {
            // RGBの差分を計算
            if (Math.abs(currentData[i] - lastScreenCanvas[i]) > 25) {
                diffPixels++;
            }
        }

        const diffRatio = diffPixels / (canvas.width * canvas.height);
        lastScreenCanvas = currentData;

        console.log(`画面変化率: ${(diffRatio * 100).toFixed(2)}%`);
        return diffRatio > DIFF_THRESHOLD; // しきい値を超えていれば true（学習中）
    }).catch(err => {
        console.error("Screen capture error:", err);
        return true;
    });
}

// 顔認証チェック
export async function checkFaceDetection(model, videoElement) {
    if (!model || !videoElement) {
        throw new Error('Model or video element is not available');
    }
    
    const predictions = await model.estimateFaces(videoElement, false);
    const hasFace = predictions.length > 0;
    
    return hasFace;
}

// 監視機能の初期化
export function resetScreenCanvas() {
    lastScreenCanvas = null;
}

/**
 * 監視インターバルを開始
 * @param {Object} params - パラメータオブジェクト
 * @param {Function} params.getIsMonitoring - isMonitoringの状態を取得する関数
 * @param {Function} params.setIsMonitoring - isMonitoringの状態を設定する関数
 * @param {Function} params.getMonitoringTimeout - monitoringTimeoutを取得する関数
 * @param {Function} params.setMonitoringTimeout - monitoringTimeoutを設定する関数
 * @param {Object} params.model - TensorFlow.jsモデル
 * @param {HTMLElement} params.video - ビデオ要素
 * @param {Function} params.getScreenStream - screenStreamを取得する関数
 * @param {Function} params.getSessionId - sessionIdを取得する関数
 */
export async function startMonitoringInterval(params) {
    const {
        getIsMonitoring,
        setIsMonitoring,
        getMonitoringTimeout,
        setMonitoringTimeout,
        model,
        video,
        getScreenStream,
        getSessionId
    } = params;

    const currentTimeout = getMonitoringTimeout();
    if (currentTimeout) clearTimeout(currentTimeout);

    async function check() {
        const currentSeconds = window.getSeconds();
        if (getIsMonitoring() && currentSeconds > 0 && currentSeconds % 10 === 0) {

            // AI処理を非同期で実行し、タイマーの進行を邪魔しないようにする
            (async () => {
                const checkSeconds = window.getSeconds();
                console.log(`${checkSeconds}秒地点：AI検知開始`);

                // 判定中はタイマー加算を止める
                setIsMonitoring(false);

                //顔認証(TensorFlow.js)
                const hasFace = await checkFaceDetection(model, video);

                //画面変化チェック
                const screenChanged = await checkScreenDifference(getScreenStream());

                //最終的な学習中判定
                const isEffective = hasFace && screenChanged;

                // 前回の更新から経過した時間（インターバル）を計算
                const interval = checkSeconds - window.getLastUpdateSeconds();
                window.setLastUpdateSeconds(checkSeconds);

                // セッション更新API呼び出し
                await window.apiUpdateSession(getSessionId(), isEffective, hasFace, screenChanged, interval);

                if (!hasFace) {
                    const resume = confirm("離席を検知しました。学習を再開する場合はOKを押してください。");
                    if (resume) {
                        setIsMonitoring(true);
                    }
                } else if (!screenChanged) {
                    console.warn("画面の変化がありません。静止画または放置の可能性があります。");
                    setIsMonitoring(true);
                } else {
                    // 通常継続
                    setIsMonitoring(true);
                }
            })();
        }
        const timeout = setTimeout(check, 1000);
        setMonitoringTimeout(timeout);
    }
    check();
}

/**
 * 監視インターバルを停止
 * @param {Function} getMonitoringTimeout - monitoringTimeoutを取得する関数
 * @param {Function} setMonitoringTimeout - monitoringTimeoutを設定する関数
 */
export function stopMonitoringInterval(getMonitoringTimeout, setMonitoringTimeout) {
    const timeout = getMonitoringTimeout();
    if (timeout) {
        clearTimeout(timeout);
        setMonitoringTimeout(null);
    }
}
