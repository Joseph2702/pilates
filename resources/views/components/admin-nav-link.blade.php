@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg transition-colors text-sm {{ $active ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
    {{ $slot }}
</a>
