@extends('layout.layout')
@php
    $title = 'Property Invoice Management';
    $subTitle = 'Invoice';
@endphp

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
                <div
                    class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <span class="text-md fw-medium text-secondary-light mb-0">Show</span>
                        <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option>10</option>
                        </select>
                        <form class="navbar-search">
                            <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search">
                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                        </form>
                        <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                            <option>Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>

                    {{-- <!-- Button to submit the form -->
                    <button form="generateInvoice" type="submit"
                        class="d-flex align-items-center gap-2 px-3 py-2 border border-primary rounded text-decoration-none text-primary hover:bg-primary hover:text-white transition"
                        title="Generate invoice for the current quarter">
                        <iconify-icon icon="ic:baseline-money" class="icon text-xl"></iconify-icon>
                        <span class="fw-semibold text-sm">Generate Invoice for Current Quarter</span>
                    </button> --}}

                    <!-- Hidden form -->
                    <form id="generateInvoice" action="{{ route('invoice.generate') }}" method="POST" class="d-none">
                        @csrf
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
                                                <input class="form-check-input radius-4 border input-form-dark"
                                                    type="checkbox" name="checkbox" id="selectAll">
                                            </div>
                                            S.L
                                        </div>
                                    </th>
                                    <th scope="col">Property Code</th>
                                    <th scope="col">Property Name</th>
                                    <th scope="col">Owner</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Total Units</th>
                                    <th scope="col">Quarter</th>
                                    {{-- <th scope="col">Status</th> --}}
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['properties'] as $property)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration }}</td>
                                        <td>{{ $property->house_code }}</td>
                                        <td>{{ $property->property_name }}</td>
                                        <td>{{ $property->landlord->user->name }}</td>
                                        <td>{{ $property->district->name }}</td>
                                        <td>{{ $property->units->count() }}</td>
                                        <td>{{ $data['quarter'] }}</td>  <!-- Changed from $quarter to $data['quarter'] -->
                                        {{-- <td class="text-center">
                                            @php
                                                $paidUnits = $property->units->filter(function($unit) {
                                                    return $unit->invoices->where('payment_status', 'Paid')->count() > 0;
                                                })->count();
                                                $totalUnits = $property->units->count();
                                                $status = $paidUnits === $totalUnits ? 'Paid' : ($paidUnits > 0 ? 'Partial' : 'Pending');
                                                $statusClass = $status === 'Paid' ? 'success' : ($status === 'Partial' ? 'warning' : 'danger');
                                            @endphp
                                            <span class="bg-{{ $statusClass }}-focus text-{{ $statusClass }}-600 border border-{{ $statusClass }}-main px-24 py-4 radius-4 fw-medium text-sm">
                                                {{ $status }}
                                            </span>
                                        </td> --}}
                                        <td>
                                            <a href="{{ route('invoice.property.details', $property->id) }}"
                                                class="d-flex align-items-center gap-2 px-3 py-2 border border-info rounded text-decoration-none text-info hover:bg-light hover:text-white transition"
                                                title="View property invoice details">
                                                <iconify-icon icon="ri:eye-line" class="icon text-xl"></iconify-icon>
                                                <span class="fw-semibold text-sm">Details</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                        <span>Showing {{ $data['invoices']->firstItem() }} to {{ $data['invoices']->lastItem() }} of
                            {{ $data['invoices']->total() }} entries</span>
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            @if ($data['invoices']->onFirstPage())
                                <li class="page-item disabled">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="javascript:void(0)">
                                        <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                    </a>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="{{ $data['invoices']->previousPageUrl() }}">
                                        <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                    </a>
                                </li>
                            @endif

                            @foreach ($data['invoices']->links()->elements as $element)
                                @if (is_string($element))
                                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $data['invoices']->currentPage())
                                            <li class="page-item active"><a
                                                    class="page-link bg-primary-600 text-white fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                                    href="{{ $url }}">{{ $page }}</a></li>
                                        @else
                                            <li class="page-item"><a
                                                    class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                                    href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($data['invoices']->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                                        href="{{ $data['invoices']->nextPageUrl() }}">
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
                                <img src="{{ asset('assets/images/crypto/crypto-img1.png') }}" alt=""
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
                                <img src="{{ asset('assets/images/crypto/crypto-img2.png') }}" alt=""
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
                                <img src="{{ asset('assets/images/crypto/crypto-img5.png') }}" alt=""
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
                                <img src="{{ asset('assets/images/crypto/crypto-img6.png') }}" alt=""
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
                                <img src="{{ asset('assets/images/crypto/crypto-img6.png') }}" alt=""
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

    <form id="q1Form" action="{{ route('invoice.quarter1') }}" method="post">
        @csrf
        @method('POST')
        <input type="hidden" name="q1" value="q1">
    </form>
@endsection
