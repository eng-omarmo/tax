@extends('layout.layout')

@php
    $title = 'Payment Method Grid';
    $subTitle = 'Payment Method ';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp
@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form method="GET" action="{{ route('payment.method.index') }}" id="filterForm">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Filter Section (Search, Pagination, Status) -->
                <div class="d-flex align-items-center gap-3 flex-wrap">


                    <div class="navbar-search">
                        <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search"
                            value="{{ request()->search }}">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </div>

                    <select name="status" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">Status</option>
                        <option value="Active" {{ request()->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request()->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>


                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Filter User (link to submit filter form) -->
                    <a href="javascript:void(0);" id="filterLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                        Filter
                    </a>

                    <!-- Reset Filter (link to reset filter form) -->
                    <a href="javascript:void(0);" id="resetLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                        Reset
                    </a>

                    <!-- Add New User Button -->
                    <a href="{{ route('payment.method.create') }}"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Method
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
                                <div class="form-check style-check d-flex align-items-center">
                                    <input class="form-check-input radius-4 border input-form-dark" type="checkbox"
                                        name="checkbox" id="selectAll">
                                </div>
                                S.L
                            </div>
                        </th>
                        <th scope="col">SNO</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentMethods as $paymentMethod)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border border-neutral-400" type="checkbox"
                                            name="checkbox">
                                    </div>
                                    {{ $loop->iteration }}
                                </div>
                            </td>
                            <td>{{ $paymentMethod->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">

                                    <div class="flex-grow-1">
                                        <span
                                            class="text-md mb-0 fw-normal text-secondary-light">{{ $paymentMethod->name }}</span>
                                    </div>
                                </div>


                            <td class="text-center">
                                <span
                                    class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ $paymentMethod->is_active ? 'Active' : 'Not Active' }}

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('payment.method.edit', $paymentMethod->id) }}"
                                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>


                                    <a href="{{ route('payment.method.destroy', $paymentMethod->id) }}"
                                        class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>


        <div class="d-flex align-items-center  bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
            <span>Showing {{ $paymentMethods->firstItem() }} to {{ $paymentMethods->lastItem() }} of {{ $paymentMethods->total() }} entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($paymentMethods->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $paymentMethods->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $paymentMethods->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($paymentMethods->getUrlRange(1, $paymentMethods->lastPage()) as $page => $url)
                            <li class="page-item {{ $paymentMethods->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $paymentMethods->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($paymentMethods->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $paymentMethods->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $paymentMethods->url($paymentMethods->lastPage()) }}">Last</a>
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
    <script>
        // Attach event listener to the Filter link
        document.getElementById('filterLink').addEventListener('click', function() {
            // Submit the form when the filter link is clicked
            document.getElementById('filterForm').submit();
        });
        document.getElementById('resetLink').addEventListener('click', function() {
            const formElements = document.getElementById('filterForm').elements;
            Array.from(formElements).forEach(element => {
                if (element.type === 'select-one' || element.type === 'text') {
                    element.value = '';
                }
            });
            document.getElementById('filterForm').submit();
        });
    </script>
@endsection
