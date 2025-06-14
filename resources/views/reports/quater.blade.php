@extends('layout.layout')

@php
    $title = 'Quarterly Income Report';
    $subTitle = 'Financial Summary by Quarter';
    $currentYear = request()->year ?? now()->year;
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-header border-bottom bg-base py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-2">
                <!-- Left: Header Title -->
                <div class="mb-3 mb-md-0">
                    <p class="mb-0 text-muted">
                        Financial Summary for {{ $currentYear }}
                    </p>
                </div>

                <!-- Right: Filter Controls -->
                <div class="d-flex gap-2">
                    <form action="{{ route('reports.quaterly.income') }}" method="GET" class="d-flex gap-2" id="filterForm">
                        <select name="year" class="form-select form-select-sm">
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <button type="button" id="resetLink" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
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

        <!-- Summary Cards -->
        <div class="card-body">
            <!-- Year Summary Cards -->
            <div class="row g-3 mb-4">
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
                            <h3 class="mb-0">${{ number_format(collect($quarterSummaries)->sum('billed'), 2) }}</h3>
                            <small class="text-muted">For {{ $currentYear }}</small>
                        </div>
                    </div>
                </div>

                <!-- Total Collected Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-success text-white me-3">
                                    <iconify-icon icon="mdi:cash-multiple" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Total Collected</h6>
                            </div>
                            <h3 class="mb-0">${{ number_format(collect($quarterSummaries)->sum('collected'), 2) }}</h3>
                            <small class="text-muted">For {{ $currentYear }}</small>
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
                            <h3 class="mb-0">${{ number_format(collect($quarterSummaries)->sum('outstanding'), 2) }}</h3>
                            <small class="text-muted">Pending collection</small>
                        </div>
                    </div>
                </div>

                <!-- Collection Rate Card -->
                <div class="col-md-3">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-primary text-white me-3">
                                    <iconify-icon icon="mdi:percent" width="24"></iconify-icon>
                                </div>
                                <h6 class="mb-0">Collection Rate</h6>
                            </div>
                            @php
                                $totalBilled = collect($quarterSummaries)->sum('billed');
                                $totalCollected = collect($quarterSummaries)->sum('collected');
                                $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;
                            @endphp
                            <h3 class="mb-0">{{ number_format($collectionRate, 1) }}%</h3>
                            <small class="text-muted">Of total billed amount</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quarterly Performance Table with Visual Indicators -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quarterly Performance</h5>
                    <span class="badge bg-primary">{{ count($quarterSummaries) }} Quarters</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="quarters-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Quarter</th>
                                    <th>Tax Billed</th>
                                    <th>Tax Collected</th>
                                    <th>Outstanding</th>
                                    <th>Collection Rate</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quarterSummaries as $item)
                                    @php
                                        $quarterRate = $item['billed'] > 0 ? ($item['collected'] / $item['billed']) * 100 : 0;
                                        $statusClass = $quarterRate < 50 ? 'bg-danger' : ($quarterRate < 75 ? 'bg-warning' : 'bg-success');
                                        $statusText = $quarterRate < 50 ? 'Needs Improvement' : ($quarterRate < 75 ? 'Satisfactory' : 'Excellent');
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-calendar-quarter text-primary me-2"></i>
                                                {{ $item['label'] }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($item['billed'], 2) }}</td>
                                        <td>${{ number_format($item['collected'], 2) }}</td>
                                        <td>${{ number_format($item['outstanding'], 2) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar {{ $statusClass }}"
                                                        role="progressbar"
                                                        style="width: {{ $quarterRate }}%"
                                                        aria-valuenow="{{ $quarterRate }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <span>{{ number_format($quarterRate, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quarterly Trend Visualization -->
            <div class="card border">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quarterly Collection Trend</h5>
                </div>
                <div class="card-body">
                    <div id="quarterlyTrendChart" style="height: 300px;"></div>
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

    <!-- Add ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset button functionality
            document.getElementById('resetLink')?.addEventListener('click', function() {
                const formElements = document.getElementById('filterForm')?.elements;
                Array.from(formElements || []).forEach(element => {
                    if (element.type === 'select-one' || element.type === 'text') {
                        element.value = '';
                    }
                });
                document.getElementById('filterForm')?.submit();
            });

            // Quarterly Trend Chart
            const quarterlyData = @json($quarterSummaries);

            const labels = quarterlyData.map(item => item.label);
            const billedData = quarterlyData.map(item => parseFloat(item.billed));
            const collectedData = quarterlyData.map(item => parseFloat(item.collected));
            const outstandingData = quarterlyData.map(item => parseFloat(item.outstanding));

            const options = {
                series: [{
                    name: 'Billed',
                    data: billedData
                }, {
                    name: 'Collected',
                    data: collectedData
                }, {
                    name: 'Outstanding',
                    data: outstandingData
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: false,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: labels,
                },
                yaxis: {
                    title: {
                        text: 'Amount ($)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$" + val.toFixed(2)
                        }
                    }
                },
                colors: ['#4e73df', '#1cc88a', '#f6c23e']
            };

            const chart = new ApexCharts(document.querySelector("#quarterlyTrendChart"), options);
            chart.render();
        });
    </script>
@endsection
