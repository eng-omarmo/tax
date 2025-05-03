@extends('layout.layout')

@php
    $title = 'Property Details';
    $subTitle = 'Property Information';
    $isAvailableBadge = fn($available) => $available
        ? ['success', 'Available', 'check']
        : ['warning', 'Occupied', 'close'];
@endphp

@section('content')
<div class="row gy-4 radius-12 mt-3">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Property Card -->
        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-body p-0">
                <!-- Property Image Section -->
                <div class="position-relative">
                    <img src="{{ asset('storage/' . $property->image) }}"
                         alt="Property Image"
                         class="img-fluid w-100 object-cover"
                         style="height: 400px; object-fit: cover; image-rendering: -webkit-optimize-contrast; transform: translateZ(0);"
                         loading="lazy"
                         decoding="async"
                    />
                    <!-- Property Info Overlay -->
                    <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 backdrop-blur-sm p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-2">{{ $property->house_code }}</h4>
                                <div class="d-flex align-items-center gap-3 text-white-50">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-time-line"></i>
                                        <span>Added {{ $property->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-map-pin-line"></i>
                                        <span>{{ $property->district->name }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-{{ $property->status === 'active' ? 'success' : 'danger' }} px-3 py-2">
                                <i class="ri-checkbox-circle-line me-1"></i>
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Property Info Cards -->
                    <div class="row g-4">
                        <!-- Owner Info -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-primary bg-opacity-10">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="card-title mb-0 text-primary">Property Owner</h6>
                                        <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2">
                                            <i class="ri-user-star-line me-1"></i>Owner
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                            {{ substr($property->landlord->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $property->landlord->user->name }}</h6>
                                            <div class="d-flex align-items-center gap-2 text-primary-600">
                                                <i class="ri-phone-line"></i>
                                                <span class="small">{{ $property->phone_number ??  $property->landlord->phone_number}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Value -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-success bg-opacity-10">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="card-title mb-0 text-success">Total Value</h6>
                                        <span class="badge bg-success bg-opacity-25 text-success px-3 py-2">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Revenue
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <h3 class="mb-0 text-success">${{ number_format($property->units->sum('unit_price'), 2) }}</h3>
                                        <div class="d-flex align-items-center gap-2 text-success-600 small">
                                            <i class="ri-building-line"></i>
                                            <span>{{ $property->units->count() }} Total Units</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Address -->
                        <div class="col-12">
                            <div class="card border-0 bg-whitw">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                            <i class="ri-map-pin-2-line text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Property Address</h6>
                                            <p class="mb-0 text-muted">{{ $property->address }}</p>
                                        </div>
                                        <div class="ms-auto text-muted bg-white small">
                                            Last updated {{ $property->updated_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Units Section -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between">
                <h5 class="mb-0">Property Units</h5>

            </div>
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm overflow-x-auto">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>


                            <tr>
                                <th class="px-4">Unit Code</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->units as $unit)
                                @php [$color, $label, $icon] = $isAvailableBadge($unit->is_available); @endphp
                                <tr class="align-middle">
                                    <td class="px-4 fw-medium">{{ $unit->unit_number }}</td>
                                    <td>{{ $unit->unit_type }}</td>
                                    <td>${{ number_format($unit->unit_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-opacity-25 bg-{{ $color }} text-{{ $color }} d-inline-flex align-items-center">
                                            <i class="ri-{{ $icon }}-circle-fill me-2 fs-xs"></i> {{ $label }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('unit.edit', $unit->id) }}"
                                               class="btn btn-icon btn-sm btn-outline-secondary rounded-8"
                                               title="Edit Unit">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <a href="{{ route('unit.show', $unit->id) }}"
                                               class="btn btn-icon btn-sm btn-outline-info rounded-8"
                                               title="View Details">
                                                <i class="ri-eye-line"></i>
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
        <!-- Property Stats -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom bg-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="ri-bar-chart-box-line text-primary me-2 fs-4"></i>
                    <h5 class="mb-0 text-primary">Property Statistics</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Total Units -->
                    <div class="col-12">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-primary mb-1">Total Units</h6>
                                    <h3 class="mb-0">{{ $property->units->count() }}</h3>
                                </div>
                                <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                                    <i class="ri-home-4-line text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Units -->
                    <div class="col-6">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    <span class="text-success">Available</span>
                                </div>
                                <h4 class="mb-0 text-success">{{ $property->units->where('is_available', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Occupied Units -->
                    <div class="col-6">
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ri-user-line text-warning"></i>
                                    <span class="text-warning">Occupied</span>
                                </div>
                                <h4 class="mb-0 text-warning">{{ $property->units->where('is_available', false)->count() }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Occupancy Rate -->
                    <div class="col-12">
                        @php
                            $totalUnits = $property->units->count();
                            $occupiedUnits = $property->units->where('is_available', false)->count();
                            $occupancyRate = $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0;
                        @endphp
                        <div class="p-3 bg-light rounded-3">
                            <h6 class="text-muted mb-2">Occupancy Rate</h6>
                            <div class="progress bg-white" style="height: 10px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $occupancyRate }}%"
                                     aria-valuenow="{{ $occupancyRate }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">{{ number_format($occupancyRate, 1) }}% Occupied</small>
                                <small class="text-muted">{{ $occupiedUnits }}/{{ $totalUnits }} Units</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-transparent border-bottom px-4 py-3">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('property.edit', $property->id) }}"
                       class="btn btn-outline-primary d-flex align-items-center gap-2">
                        <i class="ri-edit-line"></i> Edit Property Details
                    </a>
                    <a href="{{ route('unit.create', $property->id) }}"
                       class="btn btn-outline-success d-flex align-items-center gap-2">
                        <i class="ri-add-circle-line"></i> Add New Unit
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

@endsection
