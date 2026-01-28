<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('学習履歴一覧') }}
        </h2>
    </x-slot>

    <x-page-container>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-1">

            {{-- 学習履歴一覧 --}}
            <x-card title="学習履歴一覧" class="overflow-x-auto mt-1">
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
                        @foreach ($sessions as $session)
                        <tr rounded-xl>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-6 py-4 whitespace-nowrap text-base text-gray-900 text-left">
                                    {{ floor($session->effective_duration / 60) }}分{{ $session->effective_duration % 60 }}秒
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
            </x-card>
        </div>
    </x-page-container>
</x-app-layout>