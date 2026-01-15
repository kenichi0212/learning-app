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
        let model;
        let sessionId = null;
        let isMonitoring = false;
        let seconds = 0;
        let monitoringTimeout = null;//タイマーを管理する変数
        let timerInterval = null;
        let lastCanvasData = null;
        let screenStream = null;
        let startTime = null;
        let accumukatedSeconds = 0;

        const video = document.getElementById('webcam');
        const timerDisplay = document.getElementById('timerDisplay');

        //ボタン要素を取得
        const startBtn = document.getElementById('startBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const stopBtn = document.getElementById('stopBtn');

        // タイマー表示の更新
        function updateTimerText() {
            const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            timerDisplay.innerText = `${h}:${m}:${s}`;
            timerDisplay.classList.remove('text-gray-400');
            timerDisplay.classList.add('text-black');
        }

        //学習開始ボタン
        startBtn.addEventListener('click', async() => {
            //連打防止
            startBtn.disabled = true;
            startBtn.innerText = "起動中...";

            if (sessionId === null) {
                // 新規開始
                try {
                    await loadModel();
                    await startSystem();

                    // startsystemが成功したらボタンを切り替える
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

            //UIの更新
            pauseBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            startBtn.disabled = false;
            startBtn.innerText = "▷ 学習再開";  
        });

        //学習終了ボタン
        stopBtn.addEventListener('click', async () => {
            if (!confirm("本当に学習を終了しますか？")) return;


            //監視停止とタイマーを停止
            isMonitoring = false;
            if (timerInterval) clearInterval(timerInterval);
            if (monitoringTimeout) clearTimeout(monitoringTimeout);

            //カメラストリームの停止（ブラウザアイコンを消すため）
            if (video.srcObject) {
                const tracks = video.srcObject.getTracks();
                tracks.forEach(track => track.stop());
                video.srcObject = null;
            }
            //画面ストリームの停止
            if (screenStream) {
                const screenTracks = screenStream.getTracks();
                screenTracks.forEach(track => track.stop());
                screenStream = null;
            }

            alert("お疲れ様でした！学習セッションを終了します。");
        
            window.location.href = '/learning';
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
                        const predictions = await model.estimateFaces(video, false);
                        const hasFace = predictions.length > 0;

                        await updateSession(hasFace);

                        if (!hasFace) {
                            isMonitoring = false; // 離席なら時計を止める
                            const resume = confirm("離席を検知しました。学習を再開する場合はOKを押してください。");
                            // OK押下後にただ再開する（秒数はそのまま） — 秒の二重進行を防ぐため increment はしない
                            if (resume) {
                                isMonitoring = true;
                            } else {
                                isMonitoring = false;
                            }
                        }
                    })(); 
                }

                // AIが計算している間も、この「見張り役」はすぐに次の1秒待機に入る
                monitoringTimeout = setTimeout(check, 1000);
            }

            // 顔認証チェック（独立関数）は不要になりました。監視は startMonitoringInterval の中で非同期に実行します。

            // 監視を開始
            check();
        }

        async function updateSession(isLearning) {
            if (!sessionId) return;
            await fetch(`/learning/update/${sessionId}`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_learning: isLearning })
            });
        }

    </script>
</x-app-layout>