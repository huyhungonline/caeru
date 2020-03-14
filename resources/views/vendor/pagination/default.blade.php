
@if ($sum_line)
    @if ($paginator->total() !== 0)
        <p>{{ $paginator->total() }}件中{{ $paginator->firstItem() }}〜{{ $paginator->lastItem() }}件表示</p>
    @else
        <p>{{ $paginator->total() }}件表示</p>
    @endif
@endif

<ul class="{{ (!$sum_line) ? 'last':'' }}">
    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="left"><a class="active">{{ $page }}</a></li>
                @else
                    @if ($page !== 0)
                        <li class="left"><a href="{{ isset($force_url) ? Caeru::route($force_url, ['page' => $page]) : $url }}">{{ $page }}</a></li>
                    @endif
                @endif
            @endforeach
        @else
            <li class="left"><span>{{ $element }}</span></li>
        @endif
    @endforeach
</ul>
