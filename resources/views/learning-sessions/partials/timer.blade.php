<div class="border rounded-lg p-10 bg-white text-center shadow-sm">
    <h2 class="text-2xl text-gray-800 mb-4 font-bold">学習時間</h2>
    <div class="text-5xl font-mono text-gray-800 mb-6" id="timerDisplay">00:00:00</div>

    <div id="buttonGroup" class="flex justify-center space-x-4 mb-8">
        <button id="startBtn" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
            </svg>学習開始
        </button>

        <button id="pauseBtn" class="hidden bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
            </svg>

            一時停止
        </button>

        <button id="stopBtn" class="hidden bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
            </svg>
            学習終了
        </button>
    </div>

    <div class="mt-8 pt-6 border-t border-indigo-300 text-left max-w-sm mx-auto">
        <p class="text-sm text-gray-800 mb-1">
            <span class="inline-flex items-center mr-1">
                <span class="font-bold text-indigo-600 mr-1">※</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0 relative top-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
            </span>学習開始ボタンを押すとカメラと画面共有の確認が表示されます。
        </p>
        <p class="text-sm text-gray-800 mb-1 flex items-center">
            <span class="font-bold text-indigo-600 mr-1">※</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1 flex-shrink-0 relative top-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
            カメラ：離席検知のために使用します。
        </p>
        <p class="text-sm text-gray-800">
            <span class="font-bold text-indigo-600">※</span> 画面共有：学習判定のために画面変化があるかを確認します。
        </p>
    </div>
</div>