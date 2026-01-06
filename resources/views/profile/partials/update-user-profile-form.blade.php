<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール情報の更新') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('ニックネームと自己紹介を設定してください。') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="display_name" :value="__('ニックネーム')" />
            <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full" :value="old('display_name', $user->profile->display_name ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        <div class="mt-4">
            <x-input-label for="biography" :value="__('自己紹介')" />
            <textarea id="biography" name="biography" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('biography', $user->profile->biography ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('biography')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('保存されました。') }}</p>
            @endif
        </div>
    </form>
</section>