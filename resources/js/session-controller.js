// セッション制御モジュール
// ボタンイベントハンドラーとセッション操作を管理

/**
 * TensorFlow.jsモデルのロード
 * @param {Object} model - モデル変数（参照）
 * @returns {Promise<Object>} ロードされたモデル
 */
async function loadModelWrapper(model) {
    if (!model) {
        return await window.loadModel();
    }
    return model;
}

/**
 * セッションハンドラーをセットアップ
 * @param {Object} params - パラメータオブジェクト
 */
export function setupSessionHandlers(params) {
    const {
        video,
        timerDisplay,
        startBtn,
        pauseBtn,
        stopBtn,
        getState,
        setState
    } = params;

    // 学習開始ボタン
    startBtn.addEventListener('click', async () => {
        console.log('学習開始ボタンがクリックされました');
        const state = getState();
        console.log('現在の状態:', state);
        
        // 連打防止
        window.setStartButtonLoading(startBtn);

        if (!state.isCameraEnabled && !state.isScreenshotEnabled) {
            alert("最低限どちらかを選択するようにしてください。");
            if (state.profileSettingsUrl) {
                window.location.href = state.profileSettingsUrl;
            }
            window.resetStartButton(startBtn);
            return;
        }

        if (state.sessionId === null) {
            console.log('新規開始処理を実行');
            // 新規開始
            try {
                if (state.isCameraEnabled) {
                    console.log('モデルをロード中...');
                    const loadedModel = await loadModelWrapper(state.model);
                    setState({ model: loadedModel });
                    console.log('モデルロード完了');
                }

                console.log('startSystemを呼び出し中...');
                await window.startSystem({
                    video: video,
                    pauseBtn: pauseBtn,
                    timerDisplay: timerDisplay,
                    isCameraEnabled: state.isCameraEnabled,
                    isScreenshotEnabled: state.isScreenshotEnabled,
                    getIsMonitoring: () => getState().isMonitoring,
                    setIsMonitoring: (value) => setState({ isMonitoring: value }),
                    setSessionId: (id) => setState({ sessionId: id }),
                    setScreenStream: (stream) => setState({ screenStream: stream }),
                    startMonitoringInterval: () => {
                        window.startMonitoringInterval({
                            getIsMonitoring: () => getState().isMonitoring,
                            setIsMonitoring: (value) => setState({ isMonitoring: value }),
                            getMonitoringTimeout: () => getState().monitoringTimeout,
                            setMonitoringTimeout: (timeout) => setState({ monitoringTimeout: timeout }),
                            model: getState().model,
                            video: video,
                            getScreenStream: () => getState().screenStream,
                            getSessionId: () => getState().sessionId,
                            isCameraEnabled: getState().isCameraEnabled,
                            isScreenshotEnabled: getState().isScreenshotEnabled
                        });
                    }
                });

                // startSystemが成功したらボタンを切り替える
                window.showRunningButtons(startBtn, pauseBtn, stopBtn);

                //説明文を非表示にしてカメラを表示
                document.getElementById('instructionsSection')?.classList.add('hidden');
                document.getElementById('cameraSection')?.classList.remove('hidden');

            } catch (err) {
                console.error("学習開始エラー:", err);
                alert("学習の開始に失敗しました。");
                window.resetStartButton(startBtn);
            }
        } else {
            // 再開
            setState({ isMonitoring: true });
            window.startTimerUI(timerDisplay, () => getState().isMonitoring);
            window.startMonitoringInterval({
                getIsMonitoring: () => getState().isMonitoring,
                setIsMonitoring: (value) => setState({ isMonitoring: value }),
                getMonitoringTimeout: () => getState().monitoringTimeout,
                setMonitoringTimeout: (timeout) => setState({ monitoringTimeout: timeout }),
                model: getState().model,
                video: video,
                getScreenStream: () => getState().screenStream,
                getSessionId: () => getState().sessionId,
                isCameraEnabled: getState().isCameraEnabled,
                isScreenshotEnabled: getState().isScreenshotEnabled
            });
            window.showRunningButtons(startBtn, pauseBtn, stopBtn);
        }
    });

    // 学習一時停止ボタン
    pauseBtn.addEventListener('click', () => {
        setState({ isMonitoring: false });
        
        // タイマー停止
        window.stopTimer();
        
        // 監視タイムアウト停止（AIチェック＆ログ記録停止）
        window.stopMonitoringInterval(
            () => getState().monitoringTimeout,
            (timeout) => setState({ monitoringTimeout: timeout })
        );

        // UIの更新
        window.showPausedButtons(startBtn, pauseBtn);
    });

    // 学習終了ボタン
    stopBtn.addEventListener('click', async () => {
        const state = getState();
        
        if (!confirm("本当に学習を終了しますか？")) return;
        if (!state.sessionId) {
            alert("セッションIDが取得できていません。開始後に終了してください。");
            return;
        }

        // 監視停止とタイマーを停止
        setState({ isMonitoring: false });
        window.stopTimer();
        window.stopMonitoringInterval(
            () => getState().monitoringTimeout,
            (timeout) => setState({ monitoringTimeout: timeout })
        );

        try {
            const result = await window.apiStopSession(state.sessionId);

            if (result.success) {
                // カメラストリームの停止（ブラウザアイコンを消すため）
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                }
                // 画面ストリームの停止
                if (state.screenStream) {
                    state.screenStream.getTracks().forEach(track => track.stop());
                }

                alert("お疲れ様でした！学習セッションを終了します。");
                location.reload();
            } else {
                throw new Error("処理に失敗しました");
            }

        } catch (err) {
            console.error("Error in stopSession", err);
            alert("保存中にエラーが発生しました");
        }
    });
}
