<div class="border rounded-lg p-10 bg-white shadow-sm relative text-center">
    <div class="relative flex items-center justify-center mb-4 min-h-[40px]">
        <h2 class="text-gray-800 font-bold absolute left-0">カメラモニター</h2>

        <div id="pipReconnectArea" class="hidden">
            <button id="rePipBtn" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded text-sm flex items-center justify-center hover:bg-zinc-700 transition">
                PiPを再度表示
            </button>
        </div>

        <span class="text-sm font-bold text-gray-800 absolute right-0" id="cameraStatusText">OFF</span>
    </div>

    <div class="bg-zinc-800 rounded-lg aspect-video flex items-center justify-center relative overflow-hidden mx-auto max-w-xl">
        <video id="webcam" autoplay playsinline muted class="hidden w-full h-full object-cover"></video>
        <p id="offMessage" class="text-white text-xl">カメラがオフです</p>
    </div>
</div>