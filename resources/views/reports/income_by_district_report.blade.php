@extends('layout.layout')
@php
    $title = 'Income Report By District';
    $subTitle = 'Financial Summary for ' . $currentQuarter . ' ' . $currentYear;
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <!-- Left: Header Title -->
                <div class="mb-3 mb-md-0">

                    <p class="mb-0 text-muted">
           
                        {{ $currentQuarter }} {{ $currentYear }} Financial Summary
                    </p>
                </div>

                <!-- Right: Filter Controls -->
                <div class="d-flex gap-2">
                    <form action="{{ route('reports.district.income') }}" method="GET" class="d-flex gap-2">
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
                    <a href="#" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-excel me-1"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <!-- System-wide Summary Cards -->
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
                            <h3 class="mb-0">${{ number_format($systemStats['totalRevenue'], 2) }}</h3>
                            <small class="text-muted">Across all districts</small>
                        </div>
                    </div>
                </div>

                <!-- Total Collected Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-success text-white me-3">
                                    <iconify-icon icon="mdi:check-circle" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Total Collected</h6>
                            </div>
                            <h3 class="mb-0">${{ number_format($systemStats['totalPaid'], 2) }}</h3>
                            <small class="text-muted">Across all districts</small>
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
                            <h3 class="mb-0">${{ number_format($systemStats['totalOutstanding'], 2) }}</h3>
                            <small class="text-muted">Pending collection</small>
                        </div>
                    </div>
                </div>

                <!-- Collection Rate Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-info text-white me-3">
                                    <iconify-icon icon="mdi:percent" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Avg Collection Rate</h6>
                            </div>
                            <h3 class="mb-0">{{ number_format($systemStats['collectionRate'], 1) }}%</h3>
                            <small class="text-muted">System-wide average</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Highlights -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Top Performing District</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-success text-white me-3">
                                    <iconify-icon icon="mdi:trophy" width="24"></iconify-icon>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $performanceMetrics['bestCollectionRate']['district'] }}</h4>
                                    <p class="mb-0">Collection Rate: {{ number_format($performanceMetrics['bestCollectionRate']['rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Highest Revenue District</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary text-white me-3">
                                    <iconify-icon icon="mdi:cash" width="24"></iconify-icon>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $performanceMetrics['highestRevenue']['district'] }}</h4>
                                    <p class="mb-0">Revenue: ${{ number_format($performanceMetrics['highestRevenue']['amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Needs Improvement</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-warning text-white me-3">
                                    <iconify-icon icon="mdi:alert" width="24"></iconify-icon>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $performanceMetrics['worstCollectionRate']['district'] }}</h4>
                                    <p class="mb-0">Collection Rate: {{ number_format($performanceMetrics['worstCollectionRate']['rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- District Performance Table -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">District Performance Comparison</h5>
                    <span class="badge bg-primary">{{ count($incomeByDistrict) }} Districts</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="districts-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>District</th>
                                    <th>Properties</th>
                                    <th>Units</th>
                                    <th>Total Revenue</th>
                                    <th>Total Paid</th>
                                    <th>Outstanding</th>
                                    <th>Collection Rate</th>
                                    <th>Growth</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incomeByDistrict as $index => $district)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                                {{ $district['district_name'] }}
                                            </span>
                                        </td>
                                        <td>{{ $district['propertyCount'] }}</td>
                                        <td>{{ $district['unitCount'] }}</td>
                                        <td>${{ number_format($district['totalRevenue'], 2) }}</td>
                                        <td>${{ number_format($district['totalPaid'], 2) }}</td>
                                        <td>${{ number_format($district['totalOutstanding'], 2) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar {{ $district['collectionRate'] < 50 ? 'bg-danger' : ($district['collectionRate'] < 75 ? 'bg-warning' : 'bg-success') }}"
                                                         role="progressbar"
                                                         style="width: {{ $district['collectionRate'] }}%"
                                                         aria-valuenow="{{ $district['collectionRate'] }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <span>{{ number_format($district['collectionRate'], 1) }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $district['revenueGrowth'] < 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $district['revenueGrowth'] > 0 ? '+' : '' }}{{ number_format($district['revenueGrowth'], 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#districtModal{{ $district['district_id'] }}">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- District Detail Modal -->
                                    <div class="modal fade" id="districtModal{{ $district['district_id'] }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $district['district_name'] }} - Performance Analysis</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <h6>Performance Metrics</h6>
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Total Revenue
                                                                    <span class="badge bg-primary rounded-pill">${{ number_format($district['totalRevenue'], 2) }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Total Collected
                                                                    <span class="badge bg-success rounded-pill">${{ number_format($district['totalPaid'], 2) }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Outstanding Amount
                                                                    <span class="badge bg-warning rounded-pill">${{ number_format($district['totalOutstanding'], 2) }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Collection Rate
                                                                    <span class="badge bg-info rounded-pill">{{ number_format($district['collectionRate'], 1) }}%</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Revenue Growth
                                                                    <span class="badge {{ $district['revenueGrowth'] < 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                                                                        {{ $district['revenueGrowth'] > 0 ? '+' : '' }}{{ number_format($district['revenueGrowth'], 1) }}%
                                                                    </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Property Statistics</h6>
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Total Properties
                                                                    <span class="badge bg-primary rounded-pill">{{ $district['propertyCount'] }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Total Units
                                                                    <span class="badge bg-primary rounded-pill">{{ $district['unitCount'] }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Units per Property
                                                                    <span class="badge bg-info rounded-pill">
                                                                        {{ $district['propertyCount'] > 0 ? number_format($district['unitCount'] / $district['propertyCount'], 1) : 0 }}
                                                                    </span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Total Invoices
                                                                    <span class="badge bg-secondary rounded-pill">{{ $district['invoiceCount'] }}</span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    Paid Invoices
                                                                    <span class="badge bg-success rounded-pill">{{ $district['paidInvoiceCount'] }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <h6>Recommendations</h6>
                                                    <div class="alert alert-info">
                                                        <ul class="mb-0">
                                                            @forelse ($district['recommendations'] as $recommendation)
                                                                <li>{{ $recommendation }}</li>
                                                            @empty
                                                                <li>No specific recommendations at this time.</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href="#" class="btn btn-primary">View Properties</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            <i class="bi bi-exclamation-circle text-muted me-2"></i>
                                            No district data available for this period
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
                <a href="#" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-excel me-1"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
@endsection
