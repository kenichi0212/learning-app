<div class="border rounded-lg p-10 bg-white text-center shadow-sm">
    <h2 class="text-gray-600 mb-4 font-bold">学習時間</h2>
    <div class="text-5xl font-mono text-gray-400 mb-6" id="timerDisplay">00:00:00</div>

    <div id="buttonGroup" class="flex justify-center space-x-4 mb-8">
        <button id="startBtn" class="bg-zinc-800 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
            <span class="mr-2">▷</span> 学習開始
        </button>

        <button id="pauseBtn" class="hidden bg-zinc-800 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-600 transition">
            <span class="mr-2">⏸</span> 一時停止
        </button>

        <button id="stopBtn" class="hidden bg-zinc-800 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
            <span class="mr-2">⏹</span> 学習終了
        </button>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-100 text-left max-w-sm mx-auto">
        <p class="text-xs text-gray-500 mb-2">
            <span class="font-bold text-indigo-600">※</span> 学習開始ボタンを押すとカメラと画面共有の確認が表示されます。
        </p>
        <p class="text-xs text-gray-500 mb-1">
            <span class="font-bold text-indigo-600">※</span> カメラ：離席検知のために使用します。
        </p>
        <p class="text-xs text-gray-500">
            <span class="font-bold text-indigo-600">※</span> 画面共有：学習判定のために画面変化があるかを確認します。
        </p>
    </div>
</div>