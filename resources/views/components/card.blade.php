@props(['title'])

<div class="bg-white rounded-xl shadow-xl overflow-hidden border-none transform p-1">
    <h3 class="text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 p-3 text-center tracking-wider rounded-xl">
        {{ $title }}
    </h3>
    <div {{ $attributes }}>
        {{ $slot }}
    </div>
</div>