@extends('layout.layout')

@php
    $title = 'Rent and Unit Management';
    $subTitle = 'Monitoring Property';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp
@section('content')
<div class="container-fluid my-4">
    <div class="row g-4">
        <!-- Unit Info -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header  border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="ri-home-4-line me-2"></i>Unit Information
                        </h5>
                        <a href="{{ route('unit.edit', $unit->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-edit-line me-1"></i>Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted text-primary-light ">Unit Number:</strong>
                        <span class="ms-2 fw-medium ">{{ $unit->unit_number }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light ">Unit Name:</strong>
                        <span class="ms-2 fw-medium">{{ $unit->unit_name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light ">Type:</strong>
                        <span class="ms-2 fw-medium">{{ $unit->unit_type }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light ">Monthly Rate:</strong>
                        <span class="ms-2 fw-medium text-success">
                            <i class="ri-money-dollar-circle-line"></i>
                            {{ number_format($unit->unit_price, 2) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Status:</strong>
                        @php [$color, $label, $icon] = $isAvailableBadge($unit->is_available); @endphp
                        <span class="ms-2 badge bg-{{ $color }}-subtle text-{{ $color }} rounded-pill">
                            <i class="ri-{{ $icon }}-circle-line me-1"></i>{{ $label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Info -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header  border-0">
                    <h5 class="card-title mb-0 text-success">
                        <i class="ri-building-line me-2"></i>Property Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Property Name:</strong>
                        <span class="ms-2 fw-medium">{{ $unit->property->property_name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Property Code:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-hashtag me-1 text-success"></i>
                            {{ $unit->property->house_code }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Contact:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-phone-line me-1 text-success "></i>
                            {{ $unit->property->phone_number }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Location:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-map-pin-line me-1 text-success"></i>
                            {{  $unit->property->district->name . ', ' . $unit->property->branch->name . ', ' . $unit->property->zone }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($unit->currentRent)
        <!-- Rental Info -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header  border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-warning">
                            <i class="ri-file-list-3-line me-2"></i>Rental Information
                        </h5>
                        <a href="{{ route('rent.edit', $unit->currentRent->id) }}" class="btn btn-outline-warning btn-sm">
                            <i class="ri-edit-line me-1"></i>Update
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Tenant Name:</strong>
                        <span class="ms-2 fw-medium">{{ $unit->currentRent->tenant_name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Contact Number:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-phone-line me-1 text-warning"></i>
                            {{ $unit->currentRent->tenant_phone }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Start Date:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-calendar-check-line me-1 text-warning"></i>
                            {{ date('M d, Y', strtotime($unit->currentRent->rent_start_date)) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">End Date:</strong>
                        <span class="ms-2 fw-medium">
                            <i class="ri-calendar-event-line me-1 text-warning"></i>
                            {{ $unit->currentRent->rent_end_date
                                ? date('M d, Y', strtotime($unit->currentRent->rent_end_date))
                                : 'Not Specified' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted  text-primary-light">Status:</strong>
                        <span class="ms-2 badge bg-{{ $unit->currentRent->status === 'active' ? 'success' : 'danger' }}-subtle
                                     text-{{ $unit->currentRent->status === 'active' ? 'success' : 'danger' }} rounded-pill">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            {{ ucfirst($unit->currentRent->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agreement Document -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header  border-0">
                    <h5 class="card-title mb-0 text-danger">
                        <i class="ri-file-text-line me-2"></i>Agreement Document
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="document-icon me-3">
                            <i class="ri-file-pdf-line fs-2 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Rental Agreement</h6>
                            <p class="text-muted mb-0 small  text-primary-light">
                                <i class="ri-time-line me-1 text-primary-light"></i>
                                Uploaded on {{ date('M d, Y', strtotime($unit->currentRent->created_at)) }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ Storage::url($unit->currentRent->rent_document) }}"
                                class="btn btn-outline-danger btn-sm"
                                target="_blank">
                                 <i class="ri-eye-line me-1"></i>View
                             </a>

                            <a href="#"
                               class="btn btn-outline-danger btn-sm">
                                <i class="ri-download-line me-1"></i>Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- No Rent Information -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <img src="{{ asset('images/no-data.svg') }}" alt="No Rent" class="mb-4" style="height: 120px;">
                    <h5 class="text-muted mb-3 text-primary-light">No Active Rental Agreement</h5>
                    <p class="text-muted mb-4 text-primary-light">This unit currently has no active rental agreement.</p>
                    <a href="{{ route('monitor.rent.index', $unit->id) }}" class="btn btn-primary">
                        <i class="ri-add-line me-1 "></i>Register New Agreement
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    border-radius: 20px;
}

.document-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bs-danger-subtle);
    border-radius: 12px;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endsection
