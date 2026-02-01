<div class="mb-2 text-sm text-gray-600">
    カメラ状態：<span id="cameraStatusText">OFF</span>
</div>

<div class="bg-zinc-800 rounded-lg aspect-video flex items-center justify-center relative overflow-hidden w-full max-w-md group">
    <video id="webcam" autoplay playsinline muted class="hidden w-full h-full object-cover"></video>

    <div id="cameraOffUI" class="absolute inset-0 flex flex-row items-center justify-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-white opacity-50">
            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M12 18.75H4.5a2.25 2.25 0 0 1-2.25-2.25V9m12.841 9.091L16.5 19.5m-1.409-1.409c.407-.407.659-.97.659-1.591v-9a2.25 2.25 0 0 0-2.25-2.25h-9c-.621 0-1.184.252-1.591.659m12.182 12.182L2.909 5.909M1.5 4.5l1.409 1.409" />
        </svg>

    </div>

    <div id="pipReconnectArea" class="hidden absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <button id="rePipBtn" title="PiPを再度表示" class="p-1.5 text-white opacity-50 hover:opacity-100 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 -scale-x-100">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </button>
    </div>
</div>