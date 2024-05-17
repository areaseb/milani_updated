
@if ($paginator->hasPages() && $paginator->currentPage() != $paginator->lastPage())
<div class="pagination-area mt-15 mb-md-5 mb-lg-0 pagination-page">
	<br>
	
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
			<li class="page-item active"><a class="next page-link" href="{{ $paginator->nextPageUrl() }}&attach=true" rel="next">
				{{ __('Load more') }}
			</a></li>
        </ul>
    </nav>
</div>
@endif
