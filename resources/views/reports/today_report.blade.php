@extends('layout.layout')

@php
    $title = 'Today\'s Report';
    $subTitle = 'Daily Summary of Property Tax Management Activities';
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <!-- Left: Header Title -->
                <div class="mb-3 mb-md-0">
                    <h4 class="mb-1">Today's Activity Report</h4>
                    <p class="mb-0 text-muted">
                        <iconify-icon icon="solar:calendar-bold" class="me-2"></iconify-icon>
                        {{ now()->format('l, F j, Y') }}
                    </p>
                </div>

                <!-- Right: Stat Cards -->
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $stats = [
                            [
                                'label' => 'Properties',
                                'value' => count($properties),
                                'icon' => 'mdi:office-building',
                                'color' => 'primary',
                            ],
                            [
                                'label' => 'Paid Invoices',
                                'value' => count($paidUnits),
                                'icon' => 'mdi:home-alert',
                                'color' => 'success',
                            ],
                            [
                                'label' => 'Unpaid Invoices',
                                'value' => count($unpaidUnits),
                                'icon' => 'mdi:home-alert',
                                'color' => 'danger',
                            ],
                            [
                                'label' => 'Landlords',
                                'value' => count($landlords),
                                'icon' => 'mdi:account-multiple',
                                'color' => 'info',
                            ],
                            [
                                'label' => 'Payments',
                                'value' => count($payments),
                                'icon' => 'mdi:cash-register',
                                'color' => 'info',
                            ],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div class="border rounded shadow-sm d-flex align-items-center p-3 bg-white text-{{ $stat['color'] }} gap-3"
                             style="min-width: 160px;">
                            <div class="bg-{{ $stat['color'] }}-subtle rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px;">
                                <iconify-icon icon="{{ $stat['icon'] }}" class="fs-5 text-{{ $stat['color'] }}"></iconify-icon>
                            </div>
                            <div>
                                <div class="fw-bold fs-6 mb-0">{{ $stat['value'] }}</div>
                                <small class="text-muted">{{ $stat['label'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-tabs-custom" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties"
                        type="button" role="tab">
                        <i class="bi bi-building me-1"></i> Properties
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button"
                        role="tab">
                        <i class="bi bi-houses me-1"></i> Invoice Status
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="landlords-tab" data-bs-toggle="tab" data-bs-target="#landlords"
                        type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Landlords
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button"
                        role="tab">
                        <i class="bi bi-graph-up me-1"></i> Payments
                    </button>
                </li>
            </ul>

            <div class="tab-content p-24">
                <!-- Properties Tab -->
                <div class="tab-pane fade show active" id="properties" role="tabpanel">
                    <h5 class="mb-3"><i class="bi bi-building me-2"></i>New Properties Registered Today</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Property Name</th>
                                    <th width="15%">House Code</th>
                                    <th width="15%">Phone</th>
                                    <th width="20%">Branch</th>
                                    <th width="20%">Zone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($properties as $index => $property)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-building text-primary me-2"></i>
                                                {{ $property->property_name }}
                                            </span>
                                        </td>
                                        <td>{{ $property->house_code ?? '-' }}</td>
                                        <td>{{ $property->property_phone }}</td>
                                        <td>{{ $property->branch->name ?? '-' }}</td>
                                        <td>{{ $property->zone ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-building text-muted me-2"></i>
                                            No properties registered today
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Units Status Tab -->
                <div class="tab-pane fade" id="units" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light-danger d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="bi bi-house me-2"></i>Generate Invoice Today
                                    </h5>
                                    <span class="badge bg-danger">{{ count($unpaidUnits) }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="10%">#</th>
                                                    <th width="40%">Unit Name</th>
                                                    <th width="40%">Property</th>
                                                    <th width="10%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($unpaidUnits as $index => $inv)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <span class="d-flex align-items-center">
                                                                <i class="bi bi-house-door text-success me-2"></i>
                                                                {{ $inv->invoice_number }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $$inv->unit->property->property_name ?? '-' }}</td>
                                                        <td><span class="badge bg-danger">Unpaid</span></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            <i class="bi bi-check-circle text-muted me-2"></i>
                                                           No Invoice generated today
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div
                                    class="card-header bg-light-success d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="bi bi-house-door me-2"></i>Paid Invoice
                                    </h5>
                                    <span class="badge bg-success">{{ count($paidUnits) }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="10%">#</th>
                                                    <th width="40%">Unit Name</th>
                                                    <th width="40%">Property</th>
                                                    <th width="10%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($paidUnits as $index => $inv)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <span class="d-flex align-items-center">
                                                                <i class="bi bi-house-door text-success me-2"></i>
                                                                {{ $inv->invoice_number }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $inv->unit->property->property_name ?? '-' }}</td>
                                                        <td><span class="badge bg-success">Paid</span></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            <i class="bi bi-exclamation-circle text-muted me-2"></i>
                                                            No paid units recorded today
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Landlords Tab -->
                <div class="tab-pane fade" id="landlords" role="tabpanel">
                    <h5 class="mb-3"><i class="bi bi-people me-2"></i>New Landlords Registered Today</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Name</th>
                                    <th width="20%">Phone</th>
                                    <th width="30%">Email</th>
                                    <th width="20%">Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($landlords as $index => $landlord)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-person-badge text-info me-2"></i>
                                                {{ $landlord->name }}
                                            </span>
                                        </td>
                                        <td>{{ $landlord->phone }}</td>
                                        <td>{{ $landlord->email }}</td>
                                        <td>{{ $landlord->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-person-x text-muted me-2"></i>
                                            No new landlords registered today
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <h5 class="mb-3"><i class="bi bi-people me-2"></i>New Landlords Registered Today</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Invoice</th>
                                    <th width="20%">Amount</th>
                                    <th width="30%">Reference</th>
                                    <th width="20%">Competed Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-person-badge text-info me-2"></i>
                                                {{ $payment->invoice_number }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-person-x text-muted me-2"></i>
                                            No new Payment registered today
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
                <button class="btn btn-sm btn-outline-primary me-2">
                    <i class="bi bi-download me-1"></i> Export PDF
                </button>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card-header {
            padding: 1rem 1.5rem;
        }

        .table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .bg-light-primary {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-light-success {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-light-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .bg-light-info {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .nav-tabs-custom {
            border-bottom: 1px solid #dee2e6;
            padding: 0 1.5rem;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            position: relative;
        }

        .nav-tabs-custom .nav-link.active {
            color: #0d6efd;
            background: transparent;
        }

        .nav-tabs-custom .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #0d6efd;
        }

        .stat-box {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            color: white;
        }

        .stat-box i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }

        .stat-box .count {
            font-weight: 600;
            font-size: 1.25rem;
            margin-right: 0.5rem;
        }

        .stat-box .label {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .activity-timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .activity-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .activity-badge {
            position: absolute;
            left: -1.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            z-index: 2;
        }

        .activity-content {
            padding-left: 1rem;
        }

        .activity-item:not(:last-child):after {
            content: '';
            position: absolute;
            left: -1.05rem;
            top: 12px;
            height: calc(100% - 12px);
            width: 2px;
            background: #e9ecef;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // You could add tab persistence here if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Example: Remember last active tab
            const reportTabs = document.getElementById('reportTabs');
            const tabButtons = reportTabs.querySelectorAll('button[data-bs-toggle="tab"]');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    localStorage.setItem('lastActiveTab', this.id);
                });
            });

            const lastActiveTab = localStorage.getItem('lastActiveTab');
            if (lastActiveTab) {
                const tab = document.querySelector(`#${lastActiveTab}`);
                if (tab) {
                    new bootstrap.Tab(tab).show();
                }
            }
        });
    </script>
@endsection
