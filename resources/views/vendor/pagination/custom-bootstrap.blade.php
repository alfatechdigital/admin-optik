@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
        {{-- Info text --}}
        <div class="text-muted small">
            Menampilkan
            <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span>
            -
            <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span>
            dari
            <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
            data
        </div>

        {{-- Pagination links --}}
        <ul class="pagination pagination-sm mb-0 gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-3 bg-transparent text-muted px-3">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 rounded-3 bg-white text-secondary shadow-sm px-3 pagination-hover"
                       href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-3 bg-transparent text-muted px-2">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link border-0 rounded-3 text-white fw-semibold px-3 shadow-sm"
                                      style="background: linear-gradient(135deg, #1e2a5e 0%, #3b5998 100%);">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 rounded-3 bg-white text-secondary shadow-sm px-3 pagination-hover"
                                   href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 rounded-3 bg-white text-secondary shadow-sm px-3 pagination-hover"
                       href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-3 bg-transparent text-muted px-3">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .pagination-hover {
            transition: all 0.2s ease;
        }
        .pagination-hover:hover {
            background-color: #eef2ff !important;
            color: #1e2a5e !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 42, 94, 0.15) !important;
        }
        .pagination .page-item.active .page-link {
            box-shadow: 0 4px 12px rgba(30, 42, 94, 0.3) !important;
        }
    </style>
@endif
