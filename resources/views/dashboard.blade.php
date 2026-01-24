<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <x-page-container>
        {{-- 上段：累計と今日のカード --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- 累計学習時間 --}}
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
                        累計学習時間
                    </h3>
                    <div class="p-8 text-center">
                        <p class="text-4xl font-black text-slate-800">
                            {{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['total']) }}
                        </p>
                    </div>
                </div>

                {{-- 今日の集中時間：清潔感のあるデザイン --}}
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
                        今日の集中時間
                    </h3>
                    <div class="p-8 text-center">
                        <p class="text-4xl font-black text-slate-800">
                            {{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['today']) }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- 中段：今週・今月・先月の小カード --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
                        今週
                    </h3>
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['week']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
                        今月
                    </h3>
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['month']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
                        先月
                    </h3>
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold">{{ \App\Http\Controllers\LearningSessionController::formatDuration($stats['last_month']) }}</p>
                    </div>
                </div>
            </div>

            {{-- 下段：直近5件のリスト --}}
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
                <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-lg">
                    直近5件の学習セッション
                </h3>

                <div class="overflow-x-auto mt-1">
                    <table class="min-w-full table-fixed">
                        <thead class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-xl">
                            <tr class="bg-transparent">
                                <th class="w-1/5 px-6 py-4 text-left text-xl font-bold text-white uppercase tracking-widest rounded-xl border-x border-white">日付</th>
                                <th class="w-1/5 px-6 py-4 text-left text-xl font-bold text-white uppercase tracking-widest rounded-xl border-x-4 border-white">開始時刻</th>
                                <th class="w-1/5 px-6 py-4 text-left text-xl font-bold text-white uppercase tracking-widest rounded-xl border-x-4 border-white">終了時刻</th>
                                <th class="w-1/5 px-6 py-4 text-left text-xl font-bold text-white uppercase tracking-widest rounded-xl border-x-4 border-white">総計測時間</th>
                                <th class="w-1/5 px-6 py-4 text-left text-xl font-bold text-white uppercase tracking-widest rounded-xl border-x border-white">実質学習時間</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-blue-300 rounded-xl">
                            @forelse ($recentSessions as $session) {{-- dashboardでは $recentSessions を使用 --}}
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ $session->start_at->format('Y/m/d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ $session->start_at->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ $session->end_at ? $session->end_at->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ floor($session->total_duration / 60) }}分{{ $session->total_duration % 60 }}秒
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ floor($session->effective_duration / 60) }}分{{ $session->effective_duration % 60 }}秒
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-800">
                                    学習データがまだありません。
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-page-container>
</x-app-layout>