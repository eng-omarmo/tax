@extends('layout.layout')

@php
    $title = 'District List';
    $subTitle = 'District List';
@endphp

@section('content')
<div class="card h-100 p-0 radius-12">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('district.index') }}" id="filterForm">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="navbar-search">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search" value="{{ request()->search }}">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a href="javascript:void(0);" id="filterLink"  class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                    <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl"></iconify-icon> Filter
                </a>
                <a href="javascript:void(0);" id="resetLink"
                class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                Reset
            </a>

                <a href="{{ route('branch.create') }}"   class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl"></iconify-icon> Add New Branch
                </a>
            </div>
        </div>
    </form>

</div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th scope="col">
                            <div class="d-flex align-items-center gap-10">
                                <div class="form-check">
                                    <input class="form-check-input radius-4 border" type="checkbox" id="selectAll">
                                </div>
                                S.L
                            </div>
                        </th>
                        <th scope="col">Branch Name</th>
                        <th scope="col">District</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branchs as $branch)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check">
                                        <input class="form-check-input radius-4 border" type="checkbox">
                                    </div>
                                    {{ $loop->iteration }}
                                </div>
                            </td>
                            <td>{{ $branch->name }}</td>
                            <td>{{ optional($branch->district)->name }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('branch.edit', $branch->id) }}" title="Edit" class="text-success">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>
                                    <a href="{{ route('branch.delete', $branch->id) }}" title="Delete" class="text-danger remove-item-btn">
                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No branches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center  bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
            <span>Showing {{ $branchs->firstItem() }} to {{ $branchs->lastItem() }} of {{ $branchs->total() }} entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($branchs->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $branchs->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $branchs->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($branchs->getUrlRange(1, $branchs->lastPage()) as $page => $url)
                            <li class="page-item {{ $branchs->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $branchs->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($branchs->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $branchs->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $branchs->url($branchs->lastPage()) }}">Last</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px " href="javascript:void(0)">Next</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Last</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('filterLink').addEventListener('click', function() {
        document.getElementById('filterForm').submit();
    });
    document.getElementById('resetLink').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        document.getElementById('filterForm').submit();
    });
</script>
@endsection
