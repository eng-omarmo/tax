@extends('layout.layout')

@php
    $title = 'Income Report';
    $subTitle = 'Financial Summary for ' . $currentQuarter . ' ' . $currentYear;
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-header border-bottom bg-base py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-2">
                <!-- Left: Header Title -->
                <div class="mb-3 mb-md-0">

                    <p class="mb-0 text-muted">
                  
                        {{ $currentQuarter }} {{ $currentYear }} Financial Summary
                    </p>
                </div>

                <!-- Right: Filter Controls -->
                <div class="d-flex gap-2">
                    <form action="{{ route('reports.income') }}" method="GET" class="d-flex gap-2">
                        <select name="quarter" class="form-select form-select-sm">
                            <option value="Q1" {{ $currentQuarter == 'Q1' ? 'selected' : '' }}>Q1</option>
                            <option value="Q2" {{ $currentQuarter == 'Q2' ? 'selected' : '' }}>Q2</option>
                            <option value="Q3" {{ $currentQuarter == 'Q3' ? 'selected' : '' }}>Q3</option>
                            <option value="Q4" {{ $currentQuarter == 'Q4' ? 'selected' : '' }}>Q4</option>
                        </select>
                        <select name="year" class="form-select form-select-sm">
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                    </form>
                    <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <a href="{{ route('dashboard.export-quarterly-report') }}?quarter={{ $currentQuarter }}&year={{ $currentYear }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-excel me-1"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="card-body">
            <div class="row g-3 mb-4">
                <!-- Total Revenue Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-primary text-white me-3">
                                    <iconify-icon icon="mdi:cash-multiple" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Total Revenue</h6>
                            </div>
                            <h3 class="mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                            <small class="text-muted">{{ count($payments) }} payments received</small>
                        </div>
                    </div>
                </div>

                <!-- Total Billed Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-info text-white me-3">
                                    <iconify-icon icon="mdi:file-document-multiple" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Total Billed</h6>
                            </div>
                            <h3 class="mb-0">${{ number_format($totalBilled, 2) }}</h3>
                            <small class="text-muted">For the period</small>
                        </div>
                    </div>
                </div>

                <!-- Outstanding Amount Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-warning text-white me-3">
                                    <iconify-icon icon="mdi:clock-alert" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Outstanding</h6>
                            </div>
                            <h3 class="mb-0">${{ number_format($totalOutstanding, 2) }}</h3>
                            <small class="text-muted">Pending collection</small>
                        </div>
                    </div>
                </div>

                <!-- Collection Rate Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-success text-white me-3">
                                    <iconify-icon icon="mdi:percent" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Collection Rate</h6>
                            </div>
                            <h3 class="mb-0">{{ number_format($collectionRate, 1) }}%</h3>
                            <small class="text-muted">Of total billed amount</small>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Top Properties Table -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Properties by Revenue</h5>
                    <span class="badge bg-primary">{{ count($propertyRevenue) }} Properties</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="properties-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Property Name</th>
                                    <th>House Code</th>
                                    <th>Landlord</th>
                                    <th>Payments</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($propertyRevenue as $index => $property)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-building text-primary me-2"></i>
                                                {{ $property['property']->property_name ?? 'Unknown Property' }}
                                            </span>
                                        </td>
                                        <td>{{ $property['property']->house_code ?? 'N/A' }}</td>
                                        <td>{{ $property['property']->landlord->name ?? 'N/A' }}</td>
                                        <td>{{ $property['count'] }}</td>
                                        <td class="text-end">${{ number_format($property['total'], 2) }}</td>
                                        <td class="text-end">
                                            @if($totalRevenue > 0)
                                                {{ number_format(($property['total'] / $totalRevenue) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-exclamation-circle text-muted me-2"></i>
                                            No property revenue data available for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Payments Table -->
            <div class="card border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Payments</h5>
                    <span class="badge bg-success">{{ count($payments) }} Payments</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="payments-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice</th>
                                    <th>Property</th>
                                    <th>Payment Date</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-receipt text-success me-2"></i>
                                                {{ $payment->invoice->invoice_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->invoice->unit->property->property_name ?? 'N/A' }}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $payment->reference }}</td>
                                        <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-exclamation-circle text-muted me-2"></i>
                                            No payment data available for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">Report generated on {{ now()->format('M d, Y \a\t h:i A') }}</small>
            <div>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
                <a href="{{ route('dashboard.export-quarterly-report') }}?quarter={{ $currentQuarter }}&year={{ $currentYear }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-excel me-1"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
@endsection
