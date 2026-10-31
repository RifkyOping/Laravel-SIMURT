<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn cursor-pointer focus:outline-none focus:ring-1 focus:ring-rose-500']) }}>
    {{ $slot }}
</button>
