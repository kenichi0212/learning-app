<div class="border rounded-lg p-4 bg-white text-center shadow-sm">
    <div class="flex flex-col md:flex-row md:items-start gap-8">
        <div class="flex-1 text-center">
            <h2 class="text-xl text-gray-800 mb-2 font-bold">学習時間</h2>
            <div class="text-4xl font-mono text-gray-800 mb-4" id="timerDisplay">00:00:00</div>

            <div id="buttonGroup" class="flex flex-col items-center gap-4">
                <button id="startBtn" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>学習開始
                </button>
                <div class="flex flex-col gap-2">
                    <button id="pauseBtn" class="hidden bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                        </svg>一時停止
                    </button>

                    <button id="stopBtn" class="hidden bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-10 py-3 rounded text-xl flex items-center justify-center hover:bg-zinc-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                        </svg>
                        学習終了
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center text-center">
            <div id="instructionsSection" class="mt-2 md:mt-0 pt-6 md:pt-0 border-t md:border-t-0 md:border-l border-indigo-300 md:pl-6 max-w-sm mx-auto text-left">
                <ul class="space-y-3 text-sm text-gray-800">
                    <li class="flex items-start">
                        <div class="flex items-center flex-shrink-0 w-10 text-indigo-600 pt-0.5">
                            <span class="font-bold mr-1">※</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                            </svg>
                        </div>
                        <span class="leading-relaxed">学習開始ボタンを押すとカメラと画面共有の確認が表示されます。</span>
                    </li>

                    <li class="flex items-start">
                        <div class="flex items-center flex-shrink-0 w-10 text-indigo-600 pt-0.5">
                            <span class="font-bold mr-1">※</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <span class="leading-relaxed">カメラ：離席検知のために使用します。</span>
                    </li>

                    <li class="flex items-start">
                        <div class="flex items-center flex-shrink-0 w-10 text-indigo-600 pt-0.5">
                            <span class="font-bold mr-1">※</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                            </svg>
                        </div>
                        <span class="leading-relaxed">画面共有：学習判定のために画面変化があるかを確認します。</span>
                    </li>
                </ul>
            </div>

            <!-- camera.blade.phpを初期状態でhidden-->
            <div id="cameraSection" class="hidden">
                @include('learning-sessions.partials.camera')
            </div>
        </div>
    </div>
</div>