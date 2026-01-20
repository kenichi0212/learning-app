<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 上段：累計と今日のカード --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- 累計学習時間 --}}
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-500 mb-2">累計学習時間</h3>
                    <p class="text-3xl font-bold">
                        {{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['total']) }}
                    </p>
                </div>

                {{-- 今日の学習時間 --}}
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-500 mb-2">今日の集中時間</h3>
                    <p class="text-3xl font-bold text-indigo-600">
                        {{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['today']) }}
                    </p>
                </div>
            </div>

            {{-- 中段：今週・今月・先月の小カード --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">今週</h3>
                    <p class="text-xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['week']) }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">今月</h3>
                    <p class="text-xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['month']) }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">先月</h3>
                    <p class="text-xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['last_month']) }}</p>
                </div>
            </div>

            {{-- 下段：直近5件のリスト --}}
            <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                <h3 class="text-lg font-bold mb-6 text-gray-700 border-b pb-4">直近5件の学習セッション</h3>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-x font-medium text-gray-500 uppercase tracking-wider">日付</th>
                            <th class="px-6 py-3 text-left text-x font-medium text-gray-500 uppercase tracking-wider">開始時刻</th>
                            <th class="px-6 py-3 text-left text-x font-medium text-gray-500 uppercase tracking-wider">終了時刻</th>
                            <th class="px-6 py-3 text-left text-x font-medium text-gray-500 uppercase tracking-wider">総計測時間</th>
                            <th class="px-6 py-3 text-left text-x font-medium text-gray-500 uppercase tracking-wider">実質学習時間</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($recentSessions as $session) {{-- dashboardでは $recentSessions を使用 --}}
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->start_at->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $session->start_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $session->end_at ? $session->end_at->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ floor($session->total_duration / 60) }}分{{ $session->total_duration % 60 }}秒
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                {{ floor($session->effective_duration / 60) }}分{{ $session->effective_duration % 60 }}秒
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                学習データがまだありません。
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
            </div>
        </div>

    </div>
    </div>
</x-app-layout>