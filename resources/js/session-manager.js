// セッション管理モジュール
// 学習セッション全体の初期化、開始、終了を管理

/**
 * システムを起動（カメラ、画面共有、PiP、セッション開始）
 * @param {Object} params - パラメータオブジェクト
 * @param {HTMLElement} params.video - ビデオ要素
 * @param {HTMLElement} params.pauseBtn - 一時停止ボタン
 * @param {HTMLElement} params.timerDisplay - タイマー表示要素
 * @param {Function} params.getIsMonitoring - isMonitoringの状態を取得する関数
 * @param {Function} params.setIsMonitoring - isMonitoringの状態を設定する関数
 * @param {Function} params.setSessionId - sessionIdを設定する関数
 * @param {Function} params.setScreenStream - screenStreamを設定する関数
 * @param {Function} params.startMonitoringInterval - 監視インターバルを開始する関数
 * @returns {Promise<void>}
 */
export async function startSystem(params) {
    const {
        video,
        pauseBtn,
        timerDisplay,
        getIsMonitoring,
        setIsMonitoring,
        setSessionId,
        setScreenStream,
        startMonitoringInterval
    } = params;

    try {
        //1. カメラ取得
        console.log("Attempting to access camera...");
        const camstream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        });
        video.srcObject = camstream;

        //カメラ映像のメタデータが読み込まれるまで待機
        await new Promise((resolve) => {
            video.onloadedmetadata = () => {
                resolve();
            };
        });

        //2. 画面共有を取得
        console.log("Attempting to access screen share...");
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: true
            });

            //画面共有が停止されたときの処理（ブラウザの共有を停止ボタン）
            screenStream.getVideoTracks()[0].onended = () => {
                alert("画面共有が停止されました。学習判定には画面共有が必要です。");
                //処理を中断
                pauseBtn.click();
            };
            
            setScreenStream(screenStream);
        } catch (screenErr) {
            console.warn("Screen share permission denied or not available.", screenErr);
            alert("画面共有の権限が拒否されました。学習判定には画面共有が必要です。");
            //処理を中断
            return;
        }

        //3. UI更新
        window.showCameraOn(
            video,
            document.getElementById('offMessage'),
            document.getElementById('cameraStatusText')
        );

        //4. PiP起動
        if (document.pictureInPictureEnabled) {
            try {
                await video.requestPictureInPicture().catch(console.error);
            } catch (pipErr) {
                console.warn("PiP failed:", pipErr);
            }
        }

        //5. PiP関連のイベントリスナーを設定
        setupPipListeners(video, getIsMonitoring);

        //6. セッション開始（API送信）
        const data = await window.apiStartSession();
        setSessionId(data.session_id);

        //7. タイマー開始
        setIsMonitoring(true);
        window.startTimerUI(timerDisplay, getIsMonitoring);
        
        //8. 監視機能開始
        startMonitoringInterval();

    } catch (err) {
        console.error("Error in startSystem:", err);
        alert("カメラの起動に失敗しました。権限を許可してください。");
        throw err;
    }
}

/**
 * PiP関連のイベントリスナーを設定
 * @param {HTMLElement} video - ビデオ要素
 * @param {Function} getIsMonitoring - isMonitoringの状態を取得する関数
 */
function setupPipListeners(video, getIsMonitoring) {
    const pipReconnectArea = document.getElementById('pipReconnectArea');
    const rePipBtn = document.getElementById('rePipBtn');

    // タブの切り替え（戻ってきた時）を検知
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && getIsMonitoring()) {
            checkPiPStatus(pipReconnectArea);
        }
    });

    // 再開ボタンをクリックした時の処理
    rePipBtn.addEventListener('click', async () => {
        try {
            if (document.pictureInPictureEnabled) {
                await video.requestPictureInPicture();
                window.hidePipReconnect(pipReconnectArea);
            }
        } catch (err) {
            console.error("PiP再開に失敗しました:", err);
        }
    });

    // PiPが閉じられた瞬間も検知（手動で閉じられた場合）
    video.addEventListener('leavepictureinpicture', () => {
        if (getIsMonitoring()) {
            window.showPipReconnect(pipReconnectArea);
        }
    });
}

/**
 * PiPの状態をチェックしてUIを更新
 * @param {HTMLElement} pipReconnectArea - PiP再接続エリア要素
 */
function checkPiPStatus(pipReconnectArea) {
    if (!document.pictureInPictureElement) {
        window.showPipReconnect(pipReconnectArea);
    } else {
        window.hidePipReconnect(pipReconnectArea);
    }
}
