@extends('layout.layout')

@php
    $title = 'Property Details';
    $subTitle = 'Property Information';
    $isAvailableBadge = fn($available) => $available
        ? ['success', 'Available', 'check']
        : ['warning', 'Occupied', 'close'];
@endphp

@section('content')
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Property Header -->
            <div class="card shadow-lg border-0 overflow-hidden">
                <div class="property-header position-relative">
                    <img src="{{ asset('storage/' . $property->image) }}" alt="Property Image"
                        class="img-fluid w-100 property-image" loading="lazy">

                    <div class="property-overlay bg-light  p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="text-white mb-3 mb-md-0">
                                <h2 class="mb-2">{{ $property->house_code }}</h2>
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-community-line"></i>
                                        {{ $property->district->name }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-calendar-2-line"></i>
                                        Added {{ $property->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <span
                                class="badge bg-{{ $property->status === 'active' ? 'success' : 'danger' }} fs-base px-3 py-2">
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Property Essentials -->
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Owner Card -->
                        <div class="col-md-6">
                            <div class="card border-0 h-100 bg-light-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-lg bg-primary text-white">
                                            {{ substr($property->landlord->name, 0, 1) ?? '' }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Property Owner</h6>
                                            <p class="mb-0 fw-medium">{{ $property->landlord->user->name ?? '' }}</p>
                                            <small class="text-muted text-primary-light">
                                                <i class="ri-phone-line text-primary-light"></i>
                                                {{ $property->phone_number ?? ($property->landlord->phone_number ?? '') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="col-md-6">
                            <div class="card border-0 h-100 bg-light-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Property Value</h6>
                                            <h3 class="mb-0 text-success">
                                                ${{ number_format($property->units->sum('unit_price'), 2) }}</h3>
                                        </div>
                                        <i class="ri-pie-chart-2-line fs-2 text-success"></i>
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted text-primary-light">{{ $property->units->count() }}
                                            Units</span>
                                        <span class="text-muted text-primary-light">Registered:
                                            {{ $property->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Card -->
                        <div class="col-12">
                            <div class="card border-0 ">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="ri-map-pin-line fs-2 text-primary "></i>
                                        <div>
                                            <h6 class="mb-1">Property Location</h6>
                                            <p class="mb-0 text-muted text-primary-light">
                                                {{ $property->landlord->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Units Section -->
            <div class="card shadow-sm border-0 mt-12">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="mb-0">Unit Details</h5>
                        <a href="{{ route('unit.create', $property->id) }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-2"></i>Add Unit
                        </a>
                    </div>
                </div>

                <div class="card-body  p-24">
                    <div class="table-responsive scroll-sm overflow-x-auto">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr>
                                    <th>Unit Code</th>
                                    <th>Type</th>
                                    <th>Monthly Rent</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($property->units as $unit)
                                    <tr>
                                        <td class="fw-medium">{{ $unit->unit_number }}</td>
                                        <td>{{ $unit->unit_type }}</td>
                                        <td>${{ number_format($unit->unit_price, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge d-inline-flex align-items-center
                                            {{ $unit->is_available == 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                <i class="ri-circle-fill me-2 fs-xs"></i>
                                                {{ $unit->is_available == 0 ? 'Available' : 'Occupied' }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('monitor.rent.view', $unit->id) }}"
                                                    class="btn btn-icon btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                    title="Rent Management">
                                                    <i class="ri-file-list-2-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics Overview -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0">Property Analytics</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Occupancy Rate -->
                        <div class="col-12">
                            <div class="card border-0 bg-light-primary">
                                <div class="card-body">
                                    @php
                                        $totalUnits = $property->units->count();
                                        $occupied = $property->units->where('is_available', 1)->count();
                                        $rate = $totalUnits > 0 ? ($occupied / $totalUnits) * 100 : 0;
                                    @endphp
                                    <h6 class="mb-3">Occupancy Rate</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $rate }}%"
                                                role="progressbar"></div>
                                        </div>
                                        <span class="fw-medium ">{{ number_format($rate, 1) }}%</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted text-primary-light">{{ $occupied }} Occupied</small>
                                        <small class="text-muted text-primary-light">{{ $totalUnits }} Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="col-6">
                            <div class="card border-0 bg-light-success h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="ri-checkbox-circle-line fs-3 text-success"></i>
                                        <div>
                                            <h6 class="mb-0">Available</h6>
                                            <h3 class="mb-0">{{ $property->units->where('is_available', 0)->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card border-0 bg-light-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="ri-user-line fs-3 text-warning"></i>
                                        <div>
                                            <h6 class="mb-0">Occupied</h6>
                                            <h3 class="mb-0">{{ $property->units->where('is_available', 1)->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-12">
                            <div class="card border-0 ">
                                <div class="card-body ">
                                    <h6 class="mb-3">Property Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('property.edit', $property->id) }}"
                                            class="btn btn-outline-primary d-flex align-items-center gap-2">
                                            <i class="ri-edit-line"></i> Edit Details
                                        </a>
                                        <a href="{{ route('property.report', $property->id) }}"
                                            class="btn btn-outline-info d-flex align-items-center gap-2">
                                            <i class="ri-file-chart-line"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Overview -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <i class="ri-money-dollar-circle-line text-danger me-2"></i>
                        <h5 class="mb-0">Tax Projections</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @php
                        $totalValue = $property->units->sum('unit_price');
                        $taxRate = 0.05;
                        $monthlyTax = $totalValue * $taxRate;
                        $quarterlyTax = $monthlyTax * 3;
                        $yearlyTax = $monthlyTax * 12;
                    @endphp

                    <!-- Quarterly Tax Cards -->
                    <div class="row g-3">
                        <!-- Q1 -->
                        <div class="col-6">
                            <div class="card border-0 bg-light-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <small class="text-primary mb-1">Q1 (Jan-Mar)</small>
                                        <h4 class="mb-0 text-primary">${{ number_format($quarterlyTax, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Q2 -->
                        <div class="col-6">
                            <div class="card border-0 bg-light-success h-100">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <small class="text-success mb-1">Q2 (Apr-Jun)</small>
                                        <h4 class="mb-0 text-success">${{ number_format($quarterlyTax, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Q3 -->
                        <div class="col-6">
                            <div class="card border-0 bg-light-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <small class="text-warning mb-1">Q3 (Jul-Sep)</small>
                                        <h4 class="mb-0 text-warning">${{ number_format($quarterlyTax, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Q4 -->
                        <div class="col-6">
                            <div class="card border-0 bg-light-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <small class="text-danger mb-1">Q4 (Oct-Dec)</small>
                                        <h4 class="mb-0 text-danger">${{ number_format($quarterlyTax, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Yearly Total -->
                        <div class="col-12">
                            <div class="card border-0 bg-light-primary text-primary-light ">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Annual Tax</h6>
                                            <h3 class="mb-0">${{ number_format($yearlyTax, 2) }}</h3>
                                        </div>
                                        <i class="ri-calendar-line fs-2"></i>
                                    </div>
                                    <small class="text-white-50">Based on 5% monthly tax rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .property-header {
            border-radius: 1rem 1rem 0 0;
            overflow: hidden;
        }

        .property-image {
            height: 300px;
            object-fit: cover;
            image-rendering: -webkit-optimize-contrast;
        }

        .property-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            backdrop-filter: blur(4px);
        }

        .card.bg-light-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .card.bg-light-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .card.bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .card.bg-light-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .avatar {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
@endsection
