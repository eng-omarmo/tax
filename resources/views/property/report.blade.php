@extends('layout.layout')

@php
    $title = 'Property Report';
    $subTitle = 'Property Report';
@endphp

@section('content')
<div class="card h-100 p-0 radius-12">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session()->get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <form method="get" action="{{ route('property.report.fech') }}" id="filterForm">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">

            <!-- Search and Filters -->
            <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                <div class="d-flex align-items-center gap-3 flex-grow">
                    <label for="start_date" class="form-label fw-bold text-neutral-900">From</label>
                    <input type="date" id="start_date" name="start_date" class="form-control h-40-px w-auto" />

                    <label for="end_date" class="form-label fw-bold text-neutral-900">To</label>
                    <input type="date" id="end_date" name="end_date" class="form-control h-40-px w-auto" />
                    <input type="text" class="form-control h-40-px" name="nbr" placeholder="Enter House NBR" />

                    <select name="status" class="form-select h-40-px w-auto">
                        <option value="">Select District</option>
                        <option value="all">All</option>
                    </select>

                    <select name="branch" class="form-select h-40-px w-auto">
                        <option value="">Select Branch</option>
                        <option value="all">All</option>
                    </select>

                    <select name="zone" class="form-select h-40-px w-auto">
                        <option value="">Select Zone</option>
                        <option value="all">All</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" id="filterButton"
                        class="btn btn-primary text-xs px-8 py-8 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-lg line-height-1"></iconify-icon>
                        Load Report
                    </button>

                    <a href="javascript:void(0);" id="print"
                        class="btn btn-secondary text-xs px-8 py-8 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-lg line-height-1"></iconify-icon>
                        Print
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
