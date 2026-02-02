<section>
    <header>
        <h2 class="text-lg font-medium text-gray-800">
            {{ __('カメラ設定・スクショ設定') }}
        </h2>
    </header>

    <div class="mt-6 space-y-4">
        <div class="flex items-center justify-between p-4 border rounded-lg border-gray-300 shadow-sm">
            <div>
                <label for="is_camera_enabled" class="font-medium text-gray-800">カメラを使用する</label>
                <p class="text-sm text-gray-600">学習セッション中にカメラでの離席検知を使用しない場合はオフにしてください。</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_camera_enabled" id="is_camera_enabled" value="1" class="sr-only peer camera-setting"
                    {{ old('is_camera_enabled', $user->profile?->is_camera_enabled ?? false) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        <div class="flex items-center justify-between p-4 border rounded-lg border-gray-300 shadow-sm">
            <div>
                <label for="is_screenshot_enabled" class="font-medium text-gray-800">スクショを使用する</label>
                <p class="text-sm text-gray-600">学習セッション中にスクショでの離席検知を使用しない場合はオフにしてください。</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_screenshot_enabled" id="is_screenshot_enabled" value="1" class="sr-only peer camera-setting"
                    {{ old('is_screenshot_enabled', $user->profile?->is_screenshot_enabled ?? false) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cameraSettings = document.querySelectorAll('.camera-setting');

        cameraSettings.forEach(setting => {
            setting.addEventListener('change', function() {
                updateCameraSettings();
            });
        });

        function updateCameraSettings() {
            const formData = new FormData();
            formData.append('is_camera_enabled', document.getElementById('is_camera_enabled').checked ? 1 : 0);
            formData.append('is_screenshot_enabled', document.getElementById('is_screenshot_enabled').checked ? 1 : 0);
            formData.append('_method', 'PATCH');

            fetch('{{ route("profile.update") }}', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 成功時の処理（オプション）
                        console.log('設定が更新されました');
                    }
                })
                .catch(error => console.error('エラー:', error));
        }
    });
</script>