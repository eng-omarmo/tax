@extends('layout.layout')

@php
    $title = 'Property Tax Management';
    $subTitle = 'Dashboard';

    // Process trend data for chart visualization
    $chartData = [];
    foreach ($trendData as $data) {
        $chartData[] = [
            'quarter' => $data['quarter'],
            'billed' => $data['collected'] + $data['outstanding'], // Calculate billed from collected + outstanding
            'collected' => $data['collected'],
            'outstanding' => $data['outstanding'],
            'collection_rate' => $data['collection_rate'],
        ];
    }
    $script = '<script src="' . asset('assets/js/homeTwoChart.js') . '"></script>
               <script src="' . asset('js/charts/taxTrendChart.js') . '"></script>
               <script>
                   document.addEventListener("DOMContentLoaded", function() {
                       // Initialize the tax trend chart with the chart data
                       initTaxTrendChart("taxTrendChart", ' . json_encode($chartData) . ');
                   });
               </script>';

@endphp

@section('content')
    <div class="container-fluid px-4">
        <!-- Dashboard Header with Stats Summary -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-50 rounded-circle p-3">
                                        <iconify-icon icon="solar:home-bold" class="text-primary fs-3"></iconify-icon>

                                    </div>
                                    <div>

                                        <p class="text-muted mb-0">
                                            <span class="badge bg-primary-subtle text-primary">{{ $currentQuarter }}
                                                {{ date('Y') }}</span>
                                            <span class="ms-2">Quarterly Performance Summary</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <!-- Time Period Selector -->
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center"
                                            type="button" id="periodDropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <iconify-icon icon="solar:calendar-bold" class="me-2"></iconify-icon>
                                            Time Period
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <h6 class="dropdown-header">Select Quarter</h6>
                                            </li>
                                            @foreach ($availableQuarters as $quarter)
                                                <li>
                                                    <a class="dropdown-item {{ $quarter === $currentQuarter ? 'active' : '' }}"
                                                        href="#">
                                                        {{ $quarter }} {{ date('Y') }}
                                                    </a>
                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-end align-items-center gap-2 mt-3 flex-wrap">
                                        <button type="button"
                                            class="btn btn-light border d-flex align-items-center justify-content-center px-3 py-2"
                                            data-bs-toggle="tooltip" title="Refresh Data" style="min-width: 120px;">
                                            <iconify-icon icon="solar:refresh-bold" class="me-2"></iconify-icon>
                                            <span class="d-none d-md-inline">Refresh</span>
                                        </button>

                                        <button type="button"
                                            class="btn btn-light border d-flex align-items-center justify-content-center px-3 py-2"
                                            data-bs-toggle="tooltip" title="Export Report" style="min-width: 120px;">
                                            <iconify-icon icon="solar:export-bold" class="me-2"></iconify-icon>
                                            <span class="d-none d-md-inline">Export</span>
                                        </button>

                                        <button type="button"
                                            class="btn btn-primary d-flex align-items-center justify-content-center px-3 py-2"
                                            data-bs-toggle="tooltip" title="Generate Report" style="min-width: 120px;">
                                            <iconify-icon icon="solar:documents-bold" class="me-2"></iconify-icon>
                                            <span class="d-none d-md-inline">Report</span>
                                        </button>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- Key Performance Indicators -->
        <div class="row gy-4 mt-1">
            <!-- Property Units Taxed -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-1">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                    <iconify-icon icon="solar:home-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Property Units Taxed</span>
                                    <h6 class="fw-semibold">{{ number_format(1520) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    5.2%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Active properties:</span>
                            <span class="fw-medium">{{ number_format(1380) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-2">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:wallet-money-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Payment Status</span>
                                    <h6 class="fw-semibold">${{ number_format(985000, 2) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    3.8%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Paid invoices:</span>
                            <span class="fw-medium">{{ number_format(1380) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Active Properties -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-warning text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:home-smile-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Active Properties</span>
                                    <h6 class="fw-semibold">{{ number_format(1380) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    2.1%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Monitoring required:</span>
                            <span class="fw-medium text-danger">{{ number_format(85) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Invoice Status -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-4">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-info text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:document-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Invoice Status</span>
                                    <div class="d-flex gap-3">
                                        <div>
                                            <span class="text-xs text-muted">Paid</span>
                                            <h6 class="fw-semibold mb-0">{{ number_format(1380) }}</h6>
                                        </div>
                                        <div>
                                            <span class="text-xs text-muted">Pending</span>
                                            <h6 class="fw-semibold mb-0">{{ number_format(240) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-danger-light text-danger-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-down-bold" class="me-1"></iconify-icon>
                                    1.2%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Eligible properties:</span>
                            <span class="fw-medium">{{ number_format(1250) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row gy-4 mt-1">
            <!-- Property Units Taxed -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-1">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                    <iconify-icon icon="solar:home-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Property Units Taxed</span>
                                    <h6 class="fw-semibold">{{ number_format(1520) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    5.2%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Active properties:</span>
                            <span class="fw-medium">{{ number_format(1380) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-2">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:wallet-money-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Payment Status</span>
                                    <h6 class="fw-semibold">${{ number_format(985000, 2) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    3.8%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Paid invoices:</span>
                            <span class="fw-medium">{{ number_format(1380) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Active Properties -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-warning text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:home-smile-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Active Properties</span>
                                    <h6 class="fw-semibold">{{ number_format(1380) }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-success-light text-success-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-up-bold" class="me-1"></iconify-icon>
                                    2.1%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Monitoring required:</span>
                            <span class="fw-medium text-danger">{{ number_format(85) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Invoice Status -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-4">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="mb-0 w-48-px h-48-px bg-info text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="solar:document-bold" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Invoice Status</span>
                                    <div class="d-flex gap-3">
                                        <div>
                                            <span class="text-xs text-muted">Paid</span>
                                            <h6 class="fw-semibold mb-0">{{ number_format(1380) }}</h6>
                                        </div>
                                        <div>
                                            <span class="text-xs text-muted">Pending</span>
                                            <h6 class="fw-semibold mb-0">{{ number_format(240) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="bg-danger-light text-danger-main px-2 py-1 rounded-2 fw-medium">
                                    <iconify-icon icon="solar:arrow-down-bold" class="me-1"></iconify-icon>
                                    1.2%
                                </span>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <span class="text-muted">Eligible properties:</span>
                            <span class="fw-medium">{{ number_format(1250) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Tax Trend and Revenue Growth -->
        <div class="row gy-4 mt-4">
            <!-- Tax Trend Chart -->
            <div class="col-xxl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quarterly Tax Collection Trend</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="trendDropdown" data-bs-toggle="dropdown">
                                    Last 4 Quarters
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Last 4 Quarters</a></li>
                                    <li><a class="dropdown-item" href="#">Last Year</a></li>
                                    <li><a class="dropdown-item" href="#">Last 2 Years</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Legend with improved styling -->
                        <div class="d-flex align-items-center gap-4 mb-4 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-600 rounded-circle"
                                    style="width: 10px; height: 10px; padding: 0;"></span>
                                <span class="text-sm fw-medium">Tax Billed</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-600 rounded-circle"
                                    style="width: 10px; height: 10px; padding: 0;"></span>
                                <span class="text-sm fw-medium">Tax Collected</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning-600 rounded-circle"
                                    style="width: 10px; height: 10px; padding: 0;"></span>
                                <span class="text-sm fw-medium">Outstanding</span>
                            </div>
                        </div>
                        <div id="taxTrendChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Growth -->
            <div class="col-xxl-4">
                <div class="card h-100 radius-8 border">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <div>
                                <h6 class="mb-2 fw-bold text-lg">Revenue Growth</h6>
                                <span class="text-sm fw-medium text-secondary-light">Weekly Report</span>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-2 fw-bold text-lg">${{ number_format($quarterlyStats['totalPaid'], 2) }}
                                </h6>
                                <span
                                    class="bg-success-focus ps-12 pe-12 pt-2 pb-2 rounded-2 fw-medium text-success-main text-sm">{{ round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100) }}%</span>
                            </div>
                        </div>
                        <div id="revenue-chart" class="mt-28"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection Metrics and Earning Statistics -->
        <div class="row gy-4 mt-4">
            <!-- Collection Metrics -->
            <div class="col-xxl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-lg">Collection Metrics</h6>
                        <a href="#" class="text-primary d-flex align-items-center gap-1">
                            View Details
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                    <div class="card-body p-4">

                        <!-- Average Collection Days -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Average Collection Days</span>
                                <span class="fw-semibold">{{ round($quarterlyStats['averageCollectionDays']) }}
                                    days</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ min(100, ($quarterlyStats['averageCollectionDays'] / 45) * 100) }}%;">
                                </div>
                            </div>
                            <small class="text-muted">Target: 30 days</small>
                        </div>

                        <!-- Early Payment Rate -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Early Payment Rate</span>
                                <span class="fw-semibold">{{ $quarterlyStats['earlyPaymentRate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $quarterlyStats['earlyPaymentRate'] }}%;">
                                </div>
                            </div>
                            <small class="text-muted">Target: 40%</small>
                        </div>

                        <!-- Overdue Tax Rate -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Overdue Tax Rate</span>
                                <span class="fw-semibold">{{ $quarterlyStats['overdueTaxRate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ $quarterlyStats['overdueTaxRate'] }}%;">
                                </div>
                            </div>
                            <small class="text-muted">Target: &lt; 15%</small>
                        </div>

                        <!-- Payment Pattern Distribution -->
                        <div class="mt-4 pt-2">
                            <h6 class="fw-bold mb-3">Payment Pattern Distribution</h6>

                            <!-- Early -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm text-muted">Early</span>
                                <span class="fw-medium text-sm">{{ $quarterlyStats['earlyPaymentRate'] }}%</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $quarterlyStats['earlyPaymentRate'] }}%;"></div>
                            </div>

                            <!-- On-time -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm text-muted">On-time</span>
                                <span class="fw-medium text-sm">
                                    {{ 100 - $quarterlyStats['earlyPaymentRate'] - $quarterlyStats['overdueTaxRate'] }}%
                                </span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ 100 - $quarterlyStats['earlyPaymentRate'] - $quarterlyStats['overdueTaxRate'] }}%;">
                                </div>
                            </div>

                            <!-- Overdue -->
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="text-sm text-muted">Overdue</span>
                                <span class="fw-medium text-sm">{{ $quarterlyStats['overdueTaxRate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger"
                                    style="width: {{ $quarterlyStats['overdueTaxRate'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earning Statistics -->
            <div class="col-xxl-8">
                <div class="card h-100 radius-8 border-0">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <div>
                                <h6 class="mb-2 fw-bold text-lg">Earning Statistic</h6>
                                <span class="text-sm fw-medium text-secondary-light">Quarterly earning overview</span>
                            </div>
                            <div class="">
                                <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                                    <option>Quarterly</option>
                                    <option>Monthly</option>
                                    <option>Weekly</option>
                                    <option>Today</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-20 d-flex justify-content-center flex-wrap gap-3">
                            <div
                                class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                                <span
                                    class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                                    <iconify-icon icon="fluent:cart-16-filled" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="text-secondary-light text-sm fw-medium">Transactions</span>
                                    <h6 class="text-md fw-semibold mb-0">{{ $quarterlyStats['paidInvoices'] }}</h6>
                                </div>
                            </div>

                            <div
                                class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                                <span
                                    class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                                    <iconify-icon icon="uis:chart" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="text-secondary-light text-sm fw-medium">Income</span>
                                    <h6 class="text-md fw-semibold mb-0">
                                        ${{ number_format($quarterlyStats['totalPaid'], 2) }}</h6>
                                </div>
                            </div>

                            <div
                                class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                                <span
                                    class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                                    <iconify-icon icon="ph:arrow-fat-up-fill" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="text-secondary-light text-sm fw-medium">Collection Rate</span>
                                    <h6 class="text-md fw-semibold mb-0">{{ $quarterlyStats['collectionRate'] }}%</h6>
                                </div>
                            </div>
                        </div>

                        <div id="barChart" class="barChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Monitoring and Top Properties -->
        <div class="row gy-4 mt-4">
            <!-- Risk Monitoring -->
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0">Risk Monitoring</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white hover-shadow transition-all h-100">
                                    <p class="text-sm text-muted mb-1">High Risk Properties</p>
                                    <h4 class="mb-0 d-flex align-items-center gap-2">
                                        {{ count($unpaidUnits) }}
                                        <span class="badge bg-danger-100 text-danger-600 fs-xs">
                                            <iconify-icon icon="solar:danger-triangle-bold" class="me-1"></iconify-icon>
                                            Critical
                                        </span>
                                    </h4>
                                    <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                        Properties with >50% unpaid units
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white hover-shadow transition-all h-100">
                                    <p class="text-sm text-muted mb-1">Overdue Amount</p>
                                    <h4 class="mb-0 d-flex align-items-center gap-2">
                                        ${{ number_format($quarterlyStats['totalOutstanding'], 2) }}
                                        <span class="badge bg-warning-100 text-warning-600 fs-xs">
                                            <iconify-icon icon="solar:clock-circle-bold" class="me-1"></iconify-icon>
                                            Attention
                                        </span>
                                    </h4>
                                    <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                        {{ $quarterlyStats['overdueTaxRate'] }}% of total billed amount
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white hover-shadow transition-all h-100">
                                    <p class="text-sm text-muted mb-1">Collection Efficiency</p>
                                    <h4 class="mb-0 d-flex align-items-center gap-2">
                                        {{ $quarterlyStats['collectionRate'] }}%
                                        @if ($quarterlyStats['collectionRate'] < 70)
                                            <span class="badge bg-warning-100 text-warning-600 fs-xs">
                                                <iconify-icon icon="solar:danger-triangle-bold"
                                                    class="me-1"></iconify-icon>
                                                Below Target
                                            </span>
                                        @else
                                            <span class="badge bg-success-100 text-success-600 fs-xs">
                                                <iconify-icon icon="solar:check-circle-bold"
                                                    class="me-1"></iconify-icon>
                                                On Target
                                            </span>
                                        @endif
                                    </h4>
                                    <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                        Target: 85% collection rate
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-white hover-shadow transition-all h-100">
                                    <p class="text-sm text-muted mb-1">Average Collection Time</p>
                                    <h4 class="mb-0 d-flex align-items-center gap-2">
                                        {{ round($quarterlyStats['averageCollectionDays']) }} days
                                        @if ($quarterlyStats['averageCollectionDays'] > 30)
                                            <span class="badge bg-warning-100 text-warning-600 fs-xs">
                                                <iconify-icon icon="solar:danger-triangle-bold"
                                                    class="me-1"></iconify-icon>
                                                Delayed
                                            </span>
                                        @else
                                            <span class="badge bg-success-100 text-success-600 fs-xs">
                                                <iconify-icon icon="solar:check-circle-bold"
                                                    class="me-1"></iconify-icon>
                                                Good
                                            </span>
                                        @endif
                                    </h4>
                                    <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                        Target: 30 days or less
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Properties -->
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Top Performing Districts</h5>
                            <button class="btn btn-sm btn-outline-primary">View All</button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="fw-medium">District</th>
                                        <th scope="col" class="fw-medium">Tax billed</th>
                                        <th scope="col" class="fw-medium">Collect Amount </th>
                                        <th scope="col" class="fw-medium">Quarter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topProperties as $property)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 me-3">
                                                        <iconify-icon icon="solar:home-2-bold-duotone"
                                                            class="fs-5 text-primary"></iconify-icon>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $property->name }}</h6>
                                                        <small class="text-muted">{{ $property->location }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $property->units_count }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2" style="max-width: 100px;">
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ $property->collection_rate }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span>{{ round($property->collection_rate) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($property->collection_rate >= 90)
                                                    <span class="badge bg-success-100 text-success-600">Excellent</span>
                                                @elseif($property->collection_rate >= 70)
                                                    <span class="badge bg-warning-100 text-warning-600">Good</span>
                                                @else
                                                    <span class="badge bg-danger-100 text-danger-600">Poor</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="col-xxl-12 mt-3">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Recent Transactions</h6>
                        <a href="javascript:void(0)"
                            class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">SL</th>
                                    <th scope="col">Date </th>
                                    <th scope="col">Payment Type</th>
                                    <th scope="col">Paid Amount</th>
                                    <th scope="col">Due Amount</th>
                                    <th scope="col">Payable Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="text-secondary-light">1</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">21 Jun 2024</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">Cash</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$0.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$150.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$150.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-secondary-light">2</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">21 Jun 2024</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">Bank</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$570 </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$0.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$570.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-secondary-light">3</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">21 Jun 2024</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">PayPal</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$300.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$100.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$200.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-secondary-light">4</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">21 Jun 2024</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">Cash</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$0.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$150.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$150.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-secondary-light">3</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">21 Jun 2024</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">PayPal</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$300.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$100.00</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary-light">$200.00</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
