<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-xs sm:text-sm font-medium transition tactile-btn cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-[#1e385b] focus:border-[#1e385b]']) }}>
    {{ $slot }}
</button>
