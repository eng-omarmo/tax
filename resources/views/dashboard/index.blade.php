@extends('layout.layout')

@php

    use App\Models\Property;
    use App\Models\Invoice;
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

                                        <!-- Replace the existing Export button with this: -->
                                        <a href="{{ route('dashboard.export-quarterly-report') }}"
                                            class="btn btn-light border d-flex align-items-center justify-content-center px-3 py-2"
                                            data-bs-toggle="tooltip" title="Export Report" style="min-width: 120px;">
                                            <iconify-icon icon="solar:export-bold" class="me-2"></iconify-icon>
                                            <span class="d-none d-md-inline">Export</span>
                                        </a>

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
                                    <h6 class="fw-semibold">{{ number_format($quarterlyStats['unitsTaxed']) }}</h6>
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
                            <span class="fw-medium">{{ number_format(Property::where('status', 'active')->count()) }}</span>
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
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Tax Collected</span>
                                    <h6 class="fw-semibold">${{ number_format($quarterlyStats['totalPaid'], 2) }}</h6>
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
                            <span class="fw-medium">{{ number_format($quarterlyStats['paidInvoices']) }}</span>
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
                                    <h6 class="fw-semibold">
                                        {{ number_format(Property::where('status', 'active')->count()) }}</h6>
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
                            <span
                                class="fw-medium text-danger">{{ number_format(Property::where('monitoring_status', 'Pending')->count()) }}</span>
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
                                            <h6 class="fw-semibold mb-0">
                                                {{ number_format($quarterlyStats['paidInvoices']) }}</h6>
                                        </div>
                                        <div>
                                            <span class="text-xs text-muted">Pending</span>
                                            <h6 class="fw-semibold mb-0">
                                                {{ number_format(Invoice::whereYear('invoice_date', date('Y'))->where('frequency', $currentQuarter)->where('payment_status', 'Pending')->count()) }}
                                            </h6>
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
                            <span
                                class="fw-medium">{{ number_format(Property::where('monitoring_status', 'Approved')->count()) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>





        <!-- Tax Trend and Revenue Growth -->
        <div class="row gy-4 mt-4">
            <!-- Tax Trend Chart -->
            <div class="col-xxl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">

                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Example Card for One Quarter -->
                            @foreach ($quarterSummaries as $summary)
                                <div class="col-md-6 col-xl-6">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h6 class="mb-2">{{ $summary['label'] ?? 'Q1 2024' }}</h6>
                                            <p class="mb-1 text-muted">Tax Billed: <strong
                                                    class="text-primary">${{ number_format($summary['billed']) }}</strong>
                                            </p>
                                            <p class="mb-1 text-muted">Tax Collected: <strong
                                                    class="text-success">${{ number_format($summary['collected']) }}</strong>
                                            </p>
                                            <p class="mb-0 text-muted">Outstanding: <strong
                                                    class="text-warning">${{ number_format($summary['outstanding']) }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                                {{-- <span
                                    class="bg-success-focus ps-12 pe-12 pt-2 pb-2 rounded-2 fw-medium text-success-main text-sm">{{ round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100) }}%</span> --}}
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


            <!-- Top Performing Properties -->
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"> District Performance </h5>
                            <a href="{{ route('reports.district.income') }}" class="btn btn-sm btn-outline-primary">

                                View All
                            </a>


                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="fw-medium">District</th>
                                        <th scope="col" class="fw-medium">Tax billed</th>
                                        <th scope="col" class="fw-medium">Collection Rate </th>
                                        <th scope="col" class="fw-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topDistricts as $district)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 me-3">
                                                        <iconify-icon icon="mdi:map-marker"
                                                            class="fs-5 text-primary"></iconify-icon>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $district->name }}</h6>
                                                        <small class="text-muted">District</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ number_format($district->total_billed, 2) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2" style="max-width: 100px;">
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ $district->collection_rate }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span>{{ round($district->collection_rate) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($district->collection_rate >= 90)
                                                    <span class="badge bg-success-100 text-success-600">Excellent</span>
                                                @elseif($district->collection_rate >= 70)
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
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Paid Amount</th>
                                    <th scope="col">Due Amount</th>
                                    <th scope="col">Payable Amount</th>
                                </tr>
                            </thead>
                            <tbody>

                            <tbody>
                                @forelse ($payments as $index => $payment)
                                    <tr>
                                        <td><span class="text-secondary-light">{{ $index + 1 }}</span></td>

                                        {{-- Payment Date --}}
                                        <td>
                                            <span class="text-secondary-light">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                            </span>
                                        </td>

                                        {{-- Payment Method --}}
                                        <td>
                                            <span
                                                class="text-secondary-light">{{ ucfirst($payment->invoice->invoice_number ?? 'N/A') }}</span>
                                        </td>

                                        {{-- Paid Amount --}}
                                        <td>
                                            <span
                                                class="text-secondary-light">${{ number_format($payment->amount, 2) }}</span>
                                        </td>

                                        {{-- Due Amount --}}
                                        <td>
                                            <span class="text-secondary-light">
                                                @if ($payment->invoice)
                                                    ${{ number_format($payment->invoice->amount - $payment->invoice->payments()->where('status', 'success')->sum('amount'), 2) }}
                                                @else
                                                    $0.00
                                                @endif
                                            </span>
                                        </td>

                                        {{-- Total Payable --}}
                                        <td>
                                            <span class="text-secondary-light">
                                                ${{ number_format($payment->invoice->amount ?? 0, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
