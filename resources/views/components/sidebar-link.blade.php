@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs(str_replace('.', '*', $route));
@endphp

<a href="{{ route($route) }}"
   wire:navigate
   title="{{ $label }}"
   class="w-full flex items-center p-3 rounded-lg transition-colors
         {{ $isActive ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
    <img src="{{ asset('icons/' . $icon) }}"
         class="w-6 aspect-square object-contain flex-shrink-0"
         alt="{{ $label }} Icon">
    <span x-show="open" class="ms-3 font-semibold whitespace-nowrap">{{ $label }}</span>
</a>
