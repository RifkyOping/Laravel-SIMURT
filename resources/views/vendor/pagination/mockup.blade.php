@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5 text-xs sm:text-sm font-medium">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-slate-400 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1.5 text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition tactile-btn">
                Sebelumnya
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 py-1.5 text-slate-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-white bg-[#1e385b] rounded-lg shadow-xs font-semibold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition tactile-btn">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1.5 text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition tactile-btn">
                Selanjutnya
            </a>
        @else
            <span class="px-3 py-1.5 text-slate-400 bg-slate-50 border border-slate-200 rounded-lg cursor-not-allowed">
                Selanjutnya
            </span>
        @endif
    </nav>
@endif
