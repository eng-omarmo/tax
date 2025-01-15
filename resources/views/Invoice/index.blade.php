@extends('layout.layout')
@php
    $title = 'Property Invoice Management';
    $subTitle = 'Invoice';
@endphp

@section('content')
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
                    <button type="button"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                        <iconify-icon icon="ic:baseline-print" class="icon text-xl line-height-1"></iconify-icon>
                   Print
                    </button>
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
                                    <th scope="col">Invoice No</th>
                                    <th scope="col">Property</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Quater</th>

                                    <th scope="col">Amount</th>
                                    <th scope="col"> Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['invoices'] as $invoice)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->unit->property->property_name }}</td>
                                        <td>{{ $invoice->unit->unit_number }}</td>
                                        <td>{{ $invoice->unit->unit_type }}</td>
                                        <td>{{ $invoice->frequency }}</td>
                                        <td>{{ $invoice->amount }}</td>

                            <td class="text-center">
                                <span
                                    class="{{ $invoice->status == 'Paid' ? 'bg-danger-focus text-danger-600 border border-danger-main' : 'bg-success-focus text-success-600 border border-success-main' }} px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ $invoice->status == 'Pending' ? 'Paid' : 'Pending' }}
                                </span>
                            </td>


                                    </tr>
                                @endforeach



                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                        <span>Showing {{ $data['invoices']->firstItem() }} to {{ $data['invoices']->lastItem() }} of {{ $data['invoices']->total() }} entries</span>
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            @if ($data['invoices']->onFirstPage())
                                <li class="page-item disabled">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
                                        <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                    </a>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="{{ $data['invoices']->previousPageUrl() }}">
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
                                            <li class="page-item active"><a class="page-link bg-primary-600 text-white fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="{{ $url }}">{{ $page }}</a></li>
                                        @else
                                            <li class="page-item"><a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($data['invoices']->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="{{ $data['invoices']->nextPageUrl() }}">
                                        <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
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
                        <div
                            class="mt-24 pb-24 mb-24 border-bottom d-flex align-items-center gap-16 justify-content-between flex-wrap">
                            <div class="text-center d-flex align-items-center  flex-column">
                                <button form="q1Form"
                                    class="w-60-px h-60-px bg-primary-50 text-primary-600 text-2xl d-inline-flex justify-content-center align-items-center rounded-circle ">
                                    <i class="ri-add-line"></i>
                                </button>
                                <span class="text-primary-light fw-medium mt-6">Quater 1 invoice</span>
                            </div>
                            <div class="text-center d-flex align-items-center  flex-column">
                                <span
                                    class="w-60-px h-60-px bg-primary-50 text-primary-600 text-2xl d-inline-flex justify-content-center align-items-center rounded-circle ">
                                    <i class="ri-add-line"></i>
                                </span>
                                <span class="text-primary-light fw-medium mt-6">Quater 2 invoice</span>
                            </div>
                            <div class="text-center d-flex align-items-center  flex-column">
                                <span
                                    class="w-60-px h-60-px bg-primary-50 text-primary-600 text-2xl d-inline-flex justify-content-center align-items-center rounded-circle ">
                                    <i class="ri-add-line"></i>
                                </span>
                                <span class="text-primary-light fw-medium mt-6">Quater 3 invoice</span>
                            </div>
                            <div class="text-center d-flex align-items-center  flex-column">
                                <span
                                    class="w-60-px h-60-px bg-primary-50 text-primary-600 text-2xl d-inline-flex justify-content-center align-items-center rounded-circle ">
                                    <i class="ri-add-line"></i>
                                </span>
                                <span class="text-primary-light fw-medium mt-6">Quater 4 invoice</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between gap-8 pb-24 border-bottom">
                            <h6 class="text-lg mb-0">Active Properties Watchlist</h6>
                            <a href="{{route('unit.index')}}" class="text-primary-600 fw-medium text-md">See all</a>
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
                                <span class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['flatIncome']}}</span>
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
                                <span class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['shopIncome']}}</span>
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
                                <span class="text-md mb-0 fw-medium text-primary-light d-block">${{ $data['otherIncome']}}</span>
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
