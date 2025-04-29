@extends('layout.layout')

@php
    $title = 'Unit Properties Grid';
    $subTitle = 'Unit Properties Grid';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp

@section('content')
    <div class="card h-100  p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif





        <form method="GET" action="{{ route('unit.index') }}" id="filterForm">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="navbar-search">
                        <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search"
                            value="{{ request()->search }}">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </div>
                    <select name="status" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">Status</option>
                        <option value="0" {{ request()->status == 0 ? 'selected' : '' }}>Available
                        </option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Occupied</option>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">

                    <a href="javascript:void(0);" id="filterLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                        Filter
                    </a>
                    <a href="javascript:void(0);" id="resetLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                        Reset
                    </a>
                    <a href="{{ route('unit.create') }}"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Unit Property
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
                        <th scope="col">Property</th>
                        <th scope="col">Property Code</th>
                        <th scope="col">Price</th>
                        <th scope="col">Unit Name</th>
                        <th scope="col">Unit Number</th>
                        <th scope="col">Unit Type</th>

                        <th scope="col">Availability</th>
                        <th scope="col">Owner</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $unit)
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

                            <td> <a href="{{ route('property.edit', $unit->property->id) }}">
                                    {{ $unit->property->property_name }}
                                </a></td>
                            <td>
                                <a href="{{ route('property.edit', $unit->property->id) }}">
                                    {{ $unit->property->house_code }}
                                </a>
                            </td>


                            <td>{{ '$' . $unit->unit_price }}</td>
                            <td>{{ $unit->unit_name }}</td>
                            <td>{{ $unit->unit_number }}</td>
                            <td>{{ $unit->unit_type }}</td>
                            {{-- <td>{{ $unit->unit_type }}</td> --}}
                            <td class="text-center">
                                <span
                                    class="{{ $unit->is_available == 1 ? 'bg-danger-focus text-danger-600 border border-danger-main' : 'bg-success-focus text-success-600 border border-success-main' }} px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ $unit->is_available == 0 ? 'Available' : 'Occupied' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span
                                    class="{{ $unit->is_owner == 'yes' ? 'bg-danger-focus text-danger-600 border border-danger-main' : 'bg-success-focus text-success-600 border border-success-main' }} px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ $unit->is_owner == 'No' ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('unit.edit', $unit->id) }}"
                                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>
                                    <a href="{{ route('unit.delete', $unit->id) }}"
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
            <span>Showing {{ $units->firstItem() }} to {{ $units->lastItem() }} of {{ $units->total() }}
                entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($units->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $units->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $units->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($units->getUrlRange(1, $units->lastPage()) as $page => $url)
                            <li class="page-item {{ $units->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $units->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}"
                                    href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($units->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $units->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $units->url($units->lastPage()) }}">Last</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px "
                                    href="javascript:void(0)">Next</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">Last</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('filterLink').addEventListener('click', function() {
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
