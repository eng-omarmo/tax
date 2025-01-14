@extends('layout.layout')

@php
    $title = 'Tenants Grid';
    $subTitle = 'Tenants Grid';
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
        <form method="GET" action="{{ route('rent.index') }}" id="filterForm">
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
                    <!-- Filter rent (link to submit filter form) -->
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

                    <!-- Add New rent Button -->
                    <a href="{{ route('rent.create') }}"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Rent
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
                        <th scope="col">Tenant </th>
                        <th scope="col">Property </th>
                        <th scope="col">Rent code  </th>

                        <th scope="col">Rent Amount </th>
                        <th scope="col">Rent Start Date</th>
                        <th scope="col">Rent Start Date</th>

                        <th scope="col">Rent Total Amount</th>
                        {{-- <th scope="col">Rent Balance</th> --}}
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rents as $rent)
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
                            <td>{{ $rent->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->tenant_name }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->property->property_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->rent_code }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->rent_amount }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->rent_start_date }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{ $rent->rent_end_date  }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{
                                        number_format ($rent->rent_total_amount, 2) ?? $rent->rent_total_amount }}</span>
                                </div>
                            </td>


                            {{-- <td>
                                <div class="d-flex align-items-center">
                                    <span class="text-md mb-0 fw-normal text-secondary-light">{{$rent->balance}}</span>
                                </div>
                            </td> --}}

                            <td class="text-center">
                                <span
                                    class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ ucfirst($rent->status) ?? $rent->user->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('rent.edit', $rent->id) }}"
                                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>

                                    <a href="{{ route('rent.delete', $rent->id) }}"
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
            <span>Showing {{ $rents->firstItem() }} to {{ $rents->lastItem() }} of {{ $rents->total() }} entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($rents->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $rents->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $rents->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($rents->getUrlRange(1, $rents->lastPage()) as $page => $url)
                            <li class="page-item {{ $rents->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $rents->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($rents->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $rents->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $rents->url($rents->lastPage()) }}">Last</a>
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

            // Reset the filter form
            document.getElementById('filterForm').submit();
        });
    </script>
@endsection
