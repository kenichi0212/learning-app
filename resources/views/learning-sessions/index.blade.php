<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('学習履歴一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

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
                        @foreach ($sessions as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->start_at->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->start_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->end_at ? $session->end_at->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                {{ floor($session->total_duration / 60) }}分{{ $session->total_duration % 60 }}秒
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $session->session_status === 'finished' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
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
            </div>
        </div>
    </div>
</x-app-layout>