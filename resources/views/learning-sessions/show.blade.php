<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('学習セッション') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- タイマー部分 --}}
            @include('learning-sessions.partials.timer')

            {{-- カメラ部分 --}}
            @include('learning-sessions.partials.camera')

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface"></script>

    <script>
        // TensorFlow.js モデル変数
        let model;
        let lastScreenCanvas = null;
        let screenStream = null;

        // セッション管理用変数
        let sessionId = null;
        let isMonitoring = false;
        let startTime = null;

        // タイマー管理用変数
        let seconds = 0;
        let accumulatedSeconds = 0;
        let lastUpdateSeconds = 0; // 前回の更新時刻（秒）を記録
        let timerInterval = null;
        let monitoringTimeout = null; //タイマーを管理する変数

        // 画面変化検出用閾値
        const DIFF_THRESHOLD = 0.1; // 画面変化の閾値（調整可能）

        // HTML要素の取得
        const video = document.getElementById('webcam');
        const timerDisplay = document.getElementById('timerDisplay');
        const startBtn = document.getElementById('startBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const stopBtn = document.getElementById('stopBtn');

        // タイマー表示更新関数
        function updateTimerText() {
            const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            timerDisplay.innerText = `${h}:${m}:${s}`;
            timerDisplay.classList.remove('text-gray-400');
            timerDisplay.classList.add('text-black');
        }

        //学習開始ボタン
        startBtn.addEventListener('click', async () => {
            //連打防止
            startBtn.disabled = true;
            startBtn.innerText = "起動中...";

            if (sessionId === null) {
                // 新規開始
                try {
                    await loadModel();
                    await startSystem();

                    // startSystemが成功したらボタンを切り替える
                    startBtn.classList.add('hidden');
                    pauseBtn.classList.remove('hidden');
                    stopBtn.classList.remove('hidden');

                } catch (err) {
                    console.error("学習開始エラー:", err);
                    alert("学習の開始に失敗しました。");
                    startBtn.disabled = false;
                    startBtn.innerText = "▷ 学習開始";
                }
            } else {
                // 再開
                isMonitoring = true;
                startTimerUI();
                startMonitoringInterval();
                startBtn.classList.add('hidden');
                pauseBtn.classList.remove('hidden');
                stopBtn.classList.remove('hidden');
            }
        });

        //学習一時停止ボタン
        pauseBtn.addEventListener('click', () => {
            isMonitoring = false;
            //タイマー停止
            if (timerInterval) clearInterval(timerInterval);
            //監視タイムアウト停止（AIチェック＆ログ記録停止）
            if (monitoringTimeout) clearTimeout(monitoringTimeout);

            //UIの更新
            pauseBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            startBtn.disabled = false;
            startBtn.innerText = "▷ 学習再開";
        });

        //学習終了ボタン
        stopBtn.addEventListener('click', async () => {
            if (!confirm("本当に学習を終了しますか？")) return;
            if (!sessionId) {
                alert("セッションIDが取得できていません。開始後に終了してください。");
                return;
            }

            //監視停止とタイマーを停止
            isMonitoring = false;
            if (timerInterval) clearInterval(timerInterval);
            if (monitoringTimeout) clearTimeout(monitoringTimeout);

            try {
                const res = await fetch(`/learning/stop/${sessionId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) {
                    // サーバがHTMLを返した場合のJSONパースエラーを避ける
                    const text = await res.text();
                    throw new Error(text || "停止APIエラー");
                }

                const result = await res.json();

                if (result.success) {
                    //カメラストリームの停止（ブラウザアイコンを消すため）
                    if (video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                    //画面ストリームの停止
                    if (screenStream) {
                        screenStream.getTracks().forEach(track => track.stop());
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

        // TensorFlow.js モデルのロード
        async function loadModel() {
            if (!model) {
                model = await blazeface.load();
                console.log("Model Loaded");
            }
        }

        // システム開始
        async function startSystem() {
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


                //3. 画面共有を取得
                console.log("Attempting to access screen share...");
                try {
                    screenStream = await navigator.mediaDevices.getDisplayMedia({
                        video: true
                    });

                    //画面共有が停止されたときの処理（ブラウザの共有を停止ボタン）
                    screenStream.getVideoTracks()[0].onended = () => {
                        alert("画面共有が停止されました。学習判定には画面共有が必要です。");
                        //処理を中断
                        pauseBtn.click();
                    };
                    // 画面共有の映像を利用する場合の処理（必要に応じて実装）
                } catch (screenErr) {
                    console.warn("Screen share permission denied or not available.", screenErr);
                    alert("画面共有の権限が拒否されました。学習判定には画面共有が必要です。");
                    //処理を中断
                    return;
                }

                //4. UI更新
                video.classList.remove('hidden');
                document.getElementById('offMessage').classList.add('hidden');
                document.getElementById('cameraStatusText').innerText = 'ON';

                // pip起動
                if (document.pictureInPictureEnabled) {
                    try {
                        await video.requestPictureInPicture().catch(console.error);
                    } catch (pipErr) {
                        console.warn("PiP failed:", pipErr);
                    }
                }

                // 要素の取得
                const pipReconnectArea = document.getElementById('pipReconnectArea');
                const rePipBtn = document.getElementById('rePipBtn');

                // --- 1. タブの切り替え（戻ってきた時）を検知 ---
                document.addEventListener('visibilitychange', () => {
                    // タブが「表示」状態になり、かつモニタリング中である場合
                    if (document.visibilityState === 'visible' && isMonitoring) {
                        checkPiPStatus();
                    }
                });

                // --- 2. PiPの状態をチェックしてUIを更新する関数 ---
                function checkPiPStatus() {
                    // document.pictureInPictureElement が null なら PiP は閉じてい
                    if (!document.pictureInPictureElement) {
                        pipReconnectArea.classList.remove('hidden');
                    } else {
                        pipReconnectArea.classList.add('hidden');
                    }
                }

                // --- 3. 再開ボタンをクリックした時の処理 ---
                rePipBtn.addEventListener('click', async () => {
                    try {
                        if (document.pictureInPictureEnabled) {
                            // 再度PiPをリクエスト
                            await video.requestPictureInPicture();
                            // 成功したらボタンエリアを隠す
                            pipReconnectArea.classList.add('hidden');
                        }
                    } catch (err) {
                        console.error("PiP再開に失敗しました:", err);
                    }
                });

                // --- 4. PiPが閉じられた瞬間も検知（手動で閉じられた場合） ---
                video.addEventListener('leavepictureinpicture', () => {
                    // 監視中であれば再開ボタンを出す
                    if (isMonitoring) {
                        pipReconnectArea.classList.remove('hidden');
                    }
                });

                // セッション開始（API送信）
                const res = await fetch("{{ route('learning.start') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                //json()でレスポンスを取得
                const data = await res.json();

                //2.その変数を使って成功・失敗を判定
                if (!res.ok) {
                    //dataの中身をそのまま使う。
                    throw new Error(data.message || "セッションの開始に失敗しました。");
                }

                //正常であればそのままsessionIdを取得
                sessionId = data.session_id;

                // タイマー開始
                isMonitoring = true;
                //1秒ごとにタイマー更新
                startTimerUI();
                //5分おきにTensorFlowと画面キャプチャで判定してAPI送信
                startMonitoringInterval();

            } catch (err) {
                console.error("Error in startSystem:", err);
                alert("カメラの起動に失敗しました。権限を許可してください。");
            }
        }

        function checkScreenDifference() {
            if (!screenStream) return true; // 画面共有がない場合は常に変化ありとする

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

        function startTimerUI() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                if (isMonitoring) {
                    seconds++;
                    updateTimerText();

                    // 10秒ごとの判定は startMonitoringInterval の非同期ロジックに任せる
                    // （ここで同期的に呼ぶとタイマーが一時停止してしまうため呼ばない）
                    // if (seconds > 0 && seconds % 10 === 0) { checkFaceStatus(); }
                }
            }, 1000);
        }

        async function checkFaceStatus() {
            console.log(`${seconds}秒地点：AI検知開始`);

            // 判定中はタイマーを止める（10秒で固定）
            isMonitoring = false;

            try {
                const predictions = await model.estimateFaces(video, false);
                const hasFace = predictions.length > 0;

                // サーバーに学習成果（10秒分）を送信
                await updateSession(hasFace);

                if (!hasFace) {
                    // 離席の場合：タイマーを止めてダイアログでOKを待ち、OKで再開
                    isMonitoring = false;
                    const resume = confirm("離席を検知しました。学習を再開する場合はOKを押してください。");
                    if (resume) {
                        isMonitoring = true;
                    } else {
                        // ユーザーがキャンセルした場合はそのまま停止（後で再確認されます）
                        isMonitoring = false;
                    }
                } else {
                    // 顔あり：そのまま継続
                    isMonitoring = true;
                }
            } catch (error) {
                console.error("AI検知エラー:", error);
                isMonitoring = true;
            }
        }

        // 監視機能（改善版）
        async function startMonitoringInterval() {
            if (monitoringTimeout) clearTimeout(monitoringTimeout);

            async function check() {
                // 監視中でない、または10秒地点でない場合は、何もせず1秒後に再確認
                // （AIを動かさないので、時計の表示には影響しません）
                if (isMonitoring && seconds > 0 && seconds % 10 === 0) {

                    // 【重要】AI処理を「非同期」で実行し、タイマーの進行を邪魔しないようにする
                    // async/awaitをあえて切り離して実行
                    (async () => {
                        console.log(`${seconds}秒地点：AI検知開始`);

                        // 判定中はタイマー加算を止める（11秒目に進まないようにする）
                        isMonitoring = false;

                        //顔認証(TensorFlow.js)
                        const predictions = await model.estimateFaces(video, false);
                        const hasFace = predictions.length > 0;

                        //画面変化チェック
                        const screenChanged = await checkScreenDifference();

                        //最終的な学習中判定
                        const isEffective = hasFace && screenChanged;

                        // 前回の更新から経過した時間（インターバル）を計算
                        const interval = seconds - lastUpdateSeconds;
                        lastUpdateSeconds = seconds;

                        await updateSession(isEffective, hasFace, screenChanged, interval);

                        if (!hasFace) {
                            const resume = confirm("離席を検知しました。学習を再開する場合はOKを押してください。");
                            // OK押下後にただ再開する（秒数はそのまま） — 秒の二重進行を防ぐため increment はしない
                            if (resume) {
                                isMonitoring = true; // OK押下で再開
                            }
                        } else if (!screenChanged) {
                            console.warn("画面の変化がありません。静止画または放置の可能性があります。");
                            // 顔がある場合は監視を継続
                            isMonitoring = true;
                        } else {
                            // 通常継続
                            isMonitoring = true;
                        }
                    })();
                }
                monitoringTimeout = setTimeout(check, 1000);
            }
            check();
        }

        async function updateSession(isLearning, hasFace = true, screenChanged = true, interval = 10) {
            if (!sessionId) return;
            await fetch(`/learning/update/${sessionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_learning: isLearning,
                    has_face: hasFace,
                    screen_changed: screenChanged,
                    interval: interval
                })
            });
        }
    </script>
</x-app-layout>