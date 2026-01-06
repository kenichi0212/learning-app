<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('ゴール設定') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('以下の項目を埋めてください。') }}
        </p>
    </header>    

        <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="title" :value="__('目標は？')" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $user->goal->title ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="deadline" :value="__('いつまでに達成するか？')" />
                <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline', $user->goal->deadline ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
            </div>

            <div class="mt-4">
                <x-input-label for="stop_doing" :value="__('目標のために止めること')" />
                <x-text-input id="stop_doing" name="stop_doing" type="text" class="mt-1 block w-full" :value="old('stop_doing', $user->goal->stop_doing ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('stop_doing')" />
            </div>

            <div class="mt-10">
                <x-input-label for="if_then_busy" :value="__('忙しいときの習慣化プラン')" />
                <textarea id="if_then_busy" name="if_then_busy" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('if_then_busy', $user->goal->if_then_busy ?? '') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('if_then_busy')" />
            </div>
    
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('習慣化') }}
                </h2>
            </header>

            <div class="mt-4">
                <x-input-label for="if_then_normal" :value="__('通常時の習慣化プラン')" />
                <textarea id="if_then_normal" name="if_then_normal" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('if_then_normal', $user->goal->if_then_normal ?? '') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('if_then_normal')" />
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