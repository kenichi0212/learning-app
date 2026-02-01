<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('学習セッション') }}
        </h2>
    </x-slot>

    <x-page-container>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- タイマー部分 --}}
            @include('learning-sessions.partials.timer')
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface"></script>

        <script>
            // API関数と監視関数はapp.jsでグローバルに公開済み
            // セッション状態管理
            const sessionState = {
                model: null,
                screenStream: null,
                sessionId: null,
                isMonitoring: false,
                startTime: null,
                monitoringTimeout: null
            };

            // 状態取得関数
            const getState = () => sessionState;

            // 状態更新関数
            const setState = (updates) => {
                Object.assign(sessionState, updates);
            };

            // HTML要素の取得
            const video = document.getElementById('webcam');
            const timerDisplay = document.getElementById('timerDisplay');
            const startBtn = document.getElementById('startBtn');
            const pauseBtn = document.getElementById('pauseBtn');
            const stopBtn = document.getElementById('stopBtn');

            // セッションハンドラーをセットアップ（モジュール読み込み完了後）
            document.addEventListener('DOMContentLoaded', () => {
                // setupSessionHandlersが利用可能になるまで待つ
                const setupHandlers = () => {
                    if (typeof window.setupSessionHandlers === 'function') {
                        window.setupSessionHandlers({
                            video,
                            timerDisplay,
                            startBtn,
                            pauseBtn,
                            stopBtn,
                            getState,
                            setState
                        });
                    } else {
                        // まだ読み込まれていない場合は少し待って再試行
                        setTimeout(setupHandlers, 50);
                    }
                };
                setupHandlers();
            });
        </script>
    </x-page-container>
</x-app-layout>