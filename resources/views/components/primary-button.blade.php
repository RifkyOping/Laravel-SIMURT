<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#1e385b] hover:bg-[#152843] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition tactile-btn cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]']) }}>
    {{ $slot }}
</button>
