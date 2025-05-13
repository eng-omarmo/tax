@extends('layout.layout')
@php
    $title = 'Property Invoice Management';
    $subTitle = 'Invoice';
@endphp
<style>
    /* Custom Select2 styling */
    .table-responsive {
        table-layout: fixed;
        width: 100%;
    }

    .table-responsive th,
    .table-responsive td {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .text-wrap {
        white-space: normal;
        word-break: break-word;
    }

    .text-nowrap {
        white-space: nowrap;
    }

    /* Compact the action button */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>

@section('content')
    @if (session('error'))
        <div class="alert alert-danger   alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row gy-4">
        <div class="col-lg-9">
            <div class="card h-100 p-0 radius-12">
                <form method="GET" action="{{ route('invoiceList') }}" id="filterForm">
                    <div
                        class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="navbar-search">
                                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search"
                                        value="{{ request()->search }}">
                                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                                </div>
                            </div>
                            <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px"
                                name="house_number">
                                <option value="">House Number</option>
                                @foreach ($data['houseNumbers'] ?? [] as $houseNumber)
                                    <option value="{{ $houseNumber }}"
                                        {{ request()->house_number == $houseNumber ? 'selected' : '' }}>{{ $houseNumber }}
                                    </option>
                                @endforeach
                            </select>
                            <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" name="zone">
                                <option value="">Zone</option>
                                @foreach ($data['zones'] ?? [] as $zone)
                                    <option value="{{ $zone }}" {{ request()->zone == $zone ? 'selected' : '' }}>
                                        {{ $zone }}</option>
                                @endforeach
                            </select>
                            <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" name="district">
                                <option value="">District</option>
                                @foreach ($data['districts'] ?? [] as $district)
                                    <option value="{{ $district->id }}"
                                        {{ request()->district == $district->id ? 'selected' : '' }}>{{ $district->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px"
                                name="property_type">
                                <option value="">Property Type</option>
                                @foreach ($data['propertyTypes'] ?? [] as $propertyType)
                                    <option value="{{ $propertyType }}"
                                        {{ request()->property_type == $propertyType ? 'selected' : '' }}>
                                        {{ $propertyType }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="d-flex align-items-center gap-3 flex-wrap">

                                <a href="javascript:void(0);" id="filterLink"
                                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                                    <iconify-icon icon="ic:baseline-filter-alt"
                                        class="icon text-xl line-height-1"></iconify-icon>
                                    Filter
                                </a>

                                <a href="javascript:void(0);" id="resetLink"
                                    class="btn btn-info text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                                    <iconify-icon icon="ic:baseline-filter-alt-off"
                                        class="icon text-xl line-height-1"></iconify-icon>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-body p-24">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 radius-12 border shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-0">Total Properties</h6>
                                            <h3 class="text-dark mb-0">{{ $data['properties']->count() }}</h3>
                                        </div>
                                        <div class="bg-light p-2 rounded-circle">
                                            <iconify-icon icon="mdi:home-city" width="32" height="32" class="text-secondary"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 radius-12 border shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-0">Total Units</h6>
                                            <h3 class="text-dark mb-0">{{$data['properties']->sum(function($property) { return $property->units->count(); }) }}</h3>
                                        </div>
                                        <div class="bg-light p-2 rounded-circle">
                                            <iconify-icon icon="mdi:office-building" width="32" height="32" class="text-secondary"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 radius-12 border shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-0">Current Quarter</h6>
                                            <h3 class="text-dark mb-0">{{ $data['quarter'] }}</h3>
                                        </div>
                                        <div class="bg-light p-2 rounded-circle">
                                            <iconify-icon icon="mdi:calendar-clock" width="32" height="32" class="text-secondary"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 radius-12 border shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-0">Potential Income</h6>
                                            <h3 class="text-dark mb-0">${{$data['potentialIncomeAfterFilter']}} </h3>
                                        </div>
                                        <div class="bg-light p-2 rounded-circle">
                                            <iconify-icon icon="mdi:cash-multiple" width="32" height="32" class="text-secondary"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Summary Cards -->
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0 table-fixed">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 5%;">SNO</th>
                                    <th scope="col" style="width: 10%;">Property Code</th>
                                    <th scope="col" style="width: 15%;">Property Name</th>
                                    <th scope="col" style="width: 10%;">Type</th>
                                    <th scope="col" style="width: 15%;">Owner</th>
                                    <th scope="col" style="width: 15%;">Location</th>
                                    <th scope="col" style="width: 8%;">Units</th>
                                    <th scope="col" style="width: 8%;">Quarter</th>
                                    <th scope="col" style="width: 14%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['properties'] as $property)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-nowrap">{{ $property->house_code }}</td>
                                        <td class="text-wrap">{{ $property->property_name }}</td>
                                        <td>{{ $property->house_type }}</td>
                                        <td class="text-wrap">{{ $property->landlord->name ?? '' }}</td>
                                        <td class="text-wrap">{{ $property->district->name }}</td>
                                        <td class="text-center">{{ $property->units->count() }}</td>
                                        <td>{{ $data['quarter'] }}</td>
                                        <td>
                                            <a href="{{ route('invoice.property.details', $property->id) }}"
                                                class="btn btn-sm btn-info d-flex align-items-center justify-content-center gap-1"
                                                title="View property invoice details">
                                                <iconify-icon icon="ri:eye-line" class="icon"></iconify-icon>
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                        <span>Showing {{ $data['properties']->firstItem() }} to {{ $data['properties']->lastItem() }} of
                            {{ $data['properties']->total() }} entries</span>
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <!-- Previous Page Link -->
                            @if ($data['properties']->onFirstPage())
                                <li class="page-item disabled">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="javascript:void(0)">
                                        <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                    </a>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="{{ $data['properties']->previousPageUrl() }}">
                                        <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                    </a>
                                </li>
                            @endif

                            <!-- Pagination Elements -->
                            @foreach ($data['properties']->getUrlRange(1, $data['properties']->lastPage()) as $page => $url)
                                <li class="page-item {{ $data['properties']->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link {{ $data['properties']->currentPage() == $page ? 'bg-primary text-white' : 'bg-neutral-200 text-secondary-light' }} fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="{{ $url }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endforeach

                            <!-- Next Page Link -->
                            @if ($data['properties']->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="{{ $data['properties']->nextPageUrl() }}">
                                        <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="javascript:void(0)">
                                        <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-body p-0">
                    <div class="px-24 py-20">
                        <span class="mb-8"> Active Property Units Potential Income</span>
                        <h5 class="text-2xl">${{ $data['potentialIncome'] }}</h5>


                        <div class="d-flex align-items-center justify-content-between gap-8 pb-24 border-bottom">
                            <h6 class="text-lg mb-0">Active Properties Watchlist</h6>
                            <a href="{{ route('unit.index') }}" class="text-primary-600 fw-medium text-md">See all</a>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-8 py-16 border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="https://img.icons8.com/color/48/000000/office-building.png" alt="Office"
                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-md mb-0 fw-medium text-primary-light d-block">Offices</span>
                                    <span class="text-xs mb-0 fw-normal text-secondary-light">Active Offices</span>
                                </div>
                            </div>
                            <div class=" d-flex flex-column">
                                <span
                                    class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['officeIncome'] }}</span>
                                <span class="text-xs mb-0 fw-normal text-secondary-light">Potential Income</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-8 py-16 border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="https://img.icons8.com/color/48/000000/apartment.png" alt="Flat"
                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-md mb-0 fw-medium text-primary-light d-block">Flats</span>
                                    <span class="text-xs mb-0 fw-normal text-secondary-light">Active offices</span>
                                </div>
                            </div>
                            <div class=" d-flex flex-column">
                                <span
                                    class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['flatIncome'] }}</span>
                                <span class="text-xs mb-0 fw-normal text-secondary-light">Potential Income</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-8 py-16 border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="https://img.icons8.com/color/48/000000/shop.png" alt="Shop"
                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-md mb-0 fw-medium text-primary-light d-block">shops</span>
                                    <span class="text-xs mb-0 fw-normal text-secondary-light">Active shops</span>
                                </div>
                            </div>
                            <div class=" d-flex flex-column">
                                <span
                                    class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['shopIncome'] }}</span>
                                <span class="text-xs mb-0 fw-normal text-secondary-light">Potential Income</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-8 py-16">
                            <div class="d-flex align-items-center">
                                <img src="https://img.icons8.com/color/48/000000/department.png" alt="Section"
                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-md mb-0 fw-medium text-primary-light d-block">Section</span>
                                    <span class="text-xs mb-0 fw-normal text-secondary-light">Active Sections</span>
                                </div>
                            </div>
                            <div class=" d-flex flex-column">
                                <span
                                    class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['sectionIncome'] }}</span>
                                <span class="text-xs mb-0 fw-normal text-secondary-light">Potential Income</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-8 py-16">
                            <div class="d-flex align-items-center">
                                <img src="https://img.icons8.com/color/48/000000/real-estate.png" alt="Others"
                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1 d-flex flex-column">
                                    <span class="text-md mb-0 fw-medium text-primary-light d-block">Others</span>
                                    <span class="text-xs mb-0 fw-normal text-secondary-light">Active others</span>
                                </div>
                            </div>
                            <div class=" d-flex flex-column">
                                <span
                                    class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['otherIncome'] }}</span>
                                <span class="text-xs mb-0 fw-normal text-secondary-light">Potential Income</span>
                            </div>
                        </div>

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
