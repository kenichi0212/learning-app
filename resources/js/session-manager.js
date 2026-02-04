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
        isCameraEnabled,
        isScreenshotEnabled,
        getIsMonitoring,
        setIsMonitoring,
        setSessionId,
        setScreenStream,
        startMonitoringInterval
    } = params;

    const buildMediaErrorMessage = (err, label) => {
        const name = err?.name || 'UnknownError';
        switch (name) {
            case 'NotAllowedError':
            case 'PermissionDeniedError':
                return `${label}の権限が拒否されました。ブラウザのサイト設定で許可してください。`;
            case 'NotFoundError':
                return `${label}のデバイスが見つかりません。接続を確認してください。`;
            case 'NotReadableError':
                return `${label}が他のアプリで使用中です。使用中のアプリを終了してください。`;
            case 'SecurityError':
                return `${label}はHTTPSまたはlocalhostでのみ利用できます。`;
            case 'OverconstrainedError':
                return `${label}の要求条件を満たすデバイスがありません。`;
            default:
                return `${label}の起動に失敗しました。`;
        }
    };

    if (isCameraEnabled && !navigator.mediaDevices?.getUserMedia) {
        alert('このブラウザはカメラに対応していません。');
        throw new Error('getUserMedia not supported');
    }
    if (isScreenshotEnabled && !navigator.mediaDevices?.getDisplayMedia) {
        alert('このブラウザは画面共有に対応していません。');
        throw new Error('getDisplayMedia not supported');
    }

    if ((isCameraEnabled || isScreenshotEnabled) && !window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        alert('カメラ/画面共有はHTTPSまたはlocalhostでのみ利用できます。');
        throw new Error('Insecure context');
    }

    try {
        //1. カメラ取得
        if (isCameraEnabled) {
            console.log("Attempting to access camera...");
            try {
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

                //3. UI更新
                window.showCameraOn(
                    video,
                    document.getElementById('cameraOffUI'),
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
            } catch (camErr) {
                console.warn("Camera permission denied or not available.", camErr);
                alert(buildMediaErrorMessage(camErr, 'カメラ'));
                throw camErr;
            }
        }

        //2. 画面共有を取得
        if (isScreenshotEnabled) {
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
                alert(buildMediaErrorMessage(screenErr, '画面共有'));
                //処理を中断
                throw screenErr;
            }
        }

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
