@extends('layout.layout')

@php
    $title = 'Tax Dashboard';
    $subTitle = 'Overview';
@endphp

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Dashboard Controls -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <label class="me-2 text-sm fw-medium"> Quarter:</label>
                        <select class="form-select form-select-sm">
                            <option>Current Quarter</option>
                            <option>Previous Quarter</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <label class="me-2 text-sm fw-medium">Property:</label>
                        <select class="form-select form-select-sm">
                            <option>All Properties</option>

                            @foreach ($topProperties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-sm btn-primary">

                        Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-secondary  ms-2">

                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Quarter Stats -->
    <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Total Tax Billed</p>
                            <h6 class="mb-0">${{ number_format($quarterlyStats['totalBilled'], 2) }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fa6-solid:file-invoice-dollar"
                                class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Current Quarter ({{ $currentQuarter }})</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-success-light mb-1">Total Tax Collected</p>
                            <h6 class="mb-0">${{ number_format($quarterlyStats['totalPaid'], 2) }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-success rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:hand-money-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-success-main mt-12 mb-0">{{ $quarterlyStats['collectionRate'] }}%
                        Collection Rate</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Tax Paid</p>
                            <h6 class="mb-0">${{ number_format($quarterlyStats['totalPaid'], 2) }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-success rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">{{ $quarterlyStats['collectionRate'] }}%
                        Collection Rate</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Outstanding Tax</p>
                            <h6 class="mb-0">${{ number_format($quarterlyStats['totalOutstanding'], 2) }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:bill-warning-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-danger-main mt-12 mb-0">{{ 100 - $quarterlyStats['collectionRate'] }}%
                        Unpaid</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Units Taxed</p>
                            <h6 class="mb-0">{{ $quarterlyStats['unitsTaxed'] }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fluent:building-multiple-20-filled"
                                class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Active Units This Quarter</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-5 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Paid Invoices</p>
                            <h6 class="mb-0">{{ $quarterlyStats['paidInvoices'] ?? 0 }}</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:document-check-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-success-main mt-12 mb-0">This Quarter</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-6 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Top 5 Taxpayers</p>
                            <h6 class="mb-0">$1,250,000</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-primary rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:medal-star-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Highest Contributors</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-7 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Next Payment Due</p>
                            <h6 class="mb-0">15 Days</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-orange rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:calendar-mark-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-warning-main mt-12 mb-0">Due: March 31, 2024</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-8 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Recent Payments</p>
                            <h6 class="mb-0">12 New</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-indigo rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:clock-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Last 24 Hours</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-none border bg-gradient-start-9 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Payment Methods</p>
                            <h6 class="mb-0">4 Active</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-pink rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:card-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                    <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Bank, Mobile, Card</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Charts -->
    <div class="row gy-4 mt-4">
        <div class="col-xxl-8 col-lg-7">
            <!-- Example of improved card design with consistent styling -->
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white p-24 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:chart-line-bold-duotone"
                            class="text-primary-600 text-xl"></iconify-icon>
                        Quarterly Tax Collection Trend
                    </h6>
                    <div class="form-group mb-0">
                        <select class="form-select form-select-sm">
                            <option>Q{{ ceil(date('n') / 3) }} {{ date('Y') }}</option>
                            <option>Q{{ ceil(date('n') / 3) > 1 ? ceil(date('n') / 3) - 1 : 4 }}
                                {{ ceil(date('n') / 3) > 1 ? date('Y') : date('Y') - 1 }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-24">
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
                    <div id="taxTrendChart" class="mb-4"></div>
                    <!-- Metrics with improved styling -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white hover-shadow transition-all">
                                <p class="text-sm text-muted mb-1">Average Collection</p>
                                <h4 class="mb-0 d-flex align-items-center gap-2">
                                    ${{ number_format($quarterlyStats['totalPaid'], 0) }}</h4>
                                <small class="text-success d-flex align-items-center gap-1 mt-1">
                                    <iconify-icon icon="solar:arrow-up-bold" class="text-sm"></iconify-icon>
                                    12.5% vs Last Quarter
                                </small>
                            </div>
                        </div>
                        <!-- Other metrics follow the same pattern -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-lg mb-4">Collection Rate Comparison</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>This Quarter</th>
                                    <th>Last Quarter</th>
                                    <th>Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tax Billed</td>
                                    <td>${{ number_format($quarterlyStats['totalBilled'], 2) }}</td>
                                    <td>${{ number_format($quarterlyStats['totalBilled'] ?? 0, 2) }}</td>
                                    <td class="text-muted">--</td>
                                </tr>
                                <tr>
                                    <td>Collection Rate</td>
                                    <td>{{ number_format($quarterlyStats['collectionRate'], 1) }}%</td>
                                    <td>{{ number_format($quarterlyStats['collectionRate'] ?? 0, 1) }}%</td>
                                    <td class="text-muted">--</td>
                                </tr>
                                <tr>
                                    <td>Total Paid</td>
                                    <td>${{ number_format($quarterlyStats['totalPaid'], 2) }}</td>
                                    <td>${{ number_format($quarterlyStats['totalPaid'] ?? 0, 2) }}</td>
                                    <td class="text-muted">--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Monitoring -->
    <div class="row gy-4 mt-4">
        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-header bg-white p-24 border-bottom">
                    <h6 class="mb-0">Unpaid Units by Property</h6>
                </div>
                <div class="card-body p-24">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Units</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unpaidUnits as $property)
                                    <tr>
                                        <td>{{ $property->name }}</td>
                                        <td>{{ $property->unpaid_units }} units</td>
                                        <td>${{ number_format($property->amount_due, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $property->status === 'Overdue' ? 'bg-danger' : 'bg-warning' }}">
                                                {{ $property->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No unpaid units found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-header bg-white p-24 border-bottom">
                    <h6 class="mb-0">Occupancy & Revenue Forecast</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="text-md mb-2">Occupancy Rate</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="mb-0">{{ number_format($quarterlyStats['unitsTaxed'], 0) }}</h3>
                                    <span class="badge bg-success">
                                        Active Units
                                    </span>
                                </div>
                                <p class="text-muted mb-0">Total Units Taxed This Quarter</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="text-md mb-2">Projected Revenue</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="mb-0">${{ number_format($quarterlyStats['totalBilled'], 0) }}</h3>
                                    <span class="badge bg-primary">
                                        Current Quarter
                                    </span>
                                </div>
                                <p class="text-muted mb-0">Next Quarter (Q{{ ceil(date('n') / 3) + 1 }}
                                    {{ date('Y') }})</p>
                            </div>
                        </div>
                    </div>
                    <div id="forecastChart" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Properties -->
    <div class="row gy-4 mt-4">
        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-header bg-white p-24 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Top Performing Properties</h6>
                    <select class="form-select form-select-sm w-auto">
                        <option>By Collection Rate</option>
                        <option>By Revenue</option>
                    </select>
                </div>
                <div class="card-body p-24">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Units</th>
                                    <th>Collection Rate</th>
                                    <th>Revenue</th>
                                    <th>YoY Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProperties as $property)
                                    <tr>
                                        <td>{{ $property->name }}</td>
                                        <td>{{ $property->occupied_units }}/{{ $property->total_units }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 5px">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $property->collection_rate }}%"></div>
                                                </div>
                                                <span>{{ number_format($property->collection_rate, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td>${{ number_format($property->revenue, 2) }}</td>
                                        <td class="text-{{ $property->yoy_growth >= 0 ? 'success' : 'danger' }}">
                                            {{ $property->yoy_growth >= 0 ? '+' : '' }}{{ number_format($property->yoy_growth, 1) }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No properties found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-6">
            <div class="card h-100">
                <div class="card-header bg-white p-24 border-bottom">
                    <h6 class="mb-0">Collection Efficiency Analysis</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="text-md mb-2">Average Collection Time</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="mb-0">
                                        {{ number_format($trendData[count($trendData) - 1]['average_days_to_pay'], 1) }}
                                    </h3>
                                    <span class="text-muted">days</span>
                                </div>
                                <p class="text-muted mb-0">Average payment time this quarter</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <h6 class="text-md mb-2">Early Payment Rate</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="mb-0">{{ $quarterlyStats['collectionRate'] }}%</h3>
                                </div>
                                <p class="text-muted mb-0">Collection rate this quarter</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Pattern Analysis -->
                    <div class="mt-4">
                        <h6 class="text-md mb-3">Payment Pattern Distribution</h6>
                        <div class="d-flex gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Early Payment</span>
                                    <span class="text-success">{{ $quarterlyStats['collectionRate'] }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $quarterlyStats['collectionRate'] }}%"></div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>On-time Payment</span>
                                    <span
                                        class="text-primary">{{ $quarterlyStats['totalBilled'] > 0 ? round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100 - $quarterlyStats['collectionRate'], 1) : 0 }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $quarterlyStats['totalBilled'] > 0 ? round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100 - $quarterlyStats['collectionRate'], 1) : 0 }}%">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Late Payment</span>
                                    <span
                                        class="text-warning">{{ $quarterlyStats['totalBilled'] > 0 ? round(100 - ($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100, 1) : 0 }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px">
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $quarterlyStats['totalBilled'] > 0 ? round(100 - ($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100, 1) : 0 }}%">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Overdue</span>
                                    <span
                                        class="text-danger">{{ round(100 - $quarterlyStats['collectionRate'], 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px">
                                    <div class="progress-bar bg-danger"
                                        style="width: {{ round(100 - $quarterlyStats['collectionRate'], 1) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Revenue Analysis -->
    <div class="row gy-4 mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white p-24 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Detailed Revenue Analysis</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-light period-selector" data-period="monthly">Monthly</button>
                            <button class="btn btn-sm btn-primary period-selector"
                                data-period="quarterly">Quarterly</button>
                            <button class="btn btn-sm btn-light period-selector" data-period="yearly">Yearly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="row g-4">
                        @foreach ($revenueAnalysis as $metric)
                            <div class="col-md-3">
                                <div class="small-stat p-3 border rounded">
                                    <h6 class="text-md mb-2">{{ $metric['label'] }}</h6>
                                    <h3 class="mb-1">${{ number_format($metric['value'], 2) }}</h3>
                                    <p class="text-muted mb-0">{{ $metric['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize the tax trend chart with improved styling
        const taxTrendChart = document.getElementById('taxTrendChart');
        const trendData = @json($trendData);

        // Format data for the chart
        const quarters = trendData.map(item => item.quarter);
        const collectedAmounts = trendData.map(item => item.collected);
        const outstandingAmounts = trendData.map(item => item.outstanding);
        const collectionRates = trendData.map(item => item.collection_rate);

        // Create the chart using ApexCharts with improved styling
        const chart = new ApexCharts(taxTrendChart, {
            series: [{
                name: 'Collected',
                type: 'column',
                data: collectedAmounts
            }, {
                name: 'Outstanding',
                type: 'column',
                data: outstandingAmounts
            }, {
                name: 'Collection Rate',
                type: 'line',
                data: collectionRates
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
                toolbar: {
                    show: false
                },
                fontFamily: 'inherit',
                background: 'transparent'
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
                padding: {
                    left: 0,
                    right: 0
                }
            },
            xaxis: {
                categories: quarters,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: [{
                title: {
                    text: 'Amount ($)',
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    formatter: function(val) {
                        return '$' + val.toLocaleString();
                    }
                }
            }, {
                opposite: true,
                title: {
                    text: 'Collection Rate (%)',
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                max: 100,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    }
                }
            }],
            colors: ['#487FFF', '#FF9F29', '#16A34A'],
            stroke: {
                width: [0, 0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '13px',
                fontWeight: 500,
                markers: {
                    width: 12,
                    height: 12,
                    radius: 12
                },
                itemMargin: {
                    horizontal: 15
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(y, {
                        seriesIndex
                    }) {
                        if (seriesIndex === 2) {
                            return y.toFixed(1) + '%';
                        }
                        return '$' + y.toLocaleString();
                    }
                }
            }
        });

        chart.render();

        // Add responsive behavior
        window.addEventListener('resize', function() {
            chart.updateOptions({
                legend: {
                    position: window.innerWidth < 768 ? 'bottom' : 'top'
                }
            });
        });
    </script>
@endsection
