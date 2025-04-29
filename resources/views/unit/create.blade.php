@extends('layout.layout')

@php
    $title = 'Unit Registration';
    $subTitle = 'Manage Unit';
@endphp

@section('content')
    @if (empty($property))
        <!-- Search Form - Original Style -->
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="text-primary mb-16 fw-semibold">Property Search</div>
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('unit.property.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <iconify-icon icon="ic:baseline-search"
                                    class="icon text-xl line-height-1 me-8 text-primary-light"></iconify-icon>
                                <input type="text" class="form-control radius-8 flex-grow-1 system-input"
                                    id="search_property" name="search_property"
                                    placeholder="Enter Property House code (PH-XXXX)" value="{{ old('search_property') }}"
                                    required>
                            </div>
                            <button type="submit"
                                class="btn btn-primary text-sm btn-medium px-24 py-8 d-flex align-items-center ms-16">
                                <span class="ms-1">Search</span>
                            </button>
                        </form>
                        <div class="text-xs text-primary-light mt-8">
                            <iconify-icon icon="bi:info-circle" class="me-4"></iconify-icon>
                            Enter the property code found on the property registration documents
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Registration Form - Existing Style Enhanced -->
        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-24">
                    <div class="text-primary fw-semibold">Unit Registration</div>
                    <a href="{{ route('unit.create') }}" class="btn btn-outline-primary btn-medium text-sm">

                        New Search
                    </a>
                </div>

                <!-- Property Info Card - Existing Style -->
                <div class="card bg-primary-50 border-primary-100 radius-8 mb-24">
                    <div class="card-body p-16">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-sm text-primary-light mb-8">Property Details</div>
                                <div class="d-flex align-items-center mb-12">
                                    <iconify-icon icon="ic:twotone-business" class="text-primary me-8"></iconify-icon>
                                    <span class="fw-semibold">{{ $property->property_name }}</span>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="text-sm text-primary-light mb-8">Contact Information</div>
                                <div class="d-flex align-items-center mb-12">
                                    <iconify-icon icon="ic:baseline-phone" class="text-primary me-8"></iconify-icon>
                                    <span class="text-sm">{{ $property->property_phone }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert bg-danger-50 border-danger-200 radius-8 mb-24">
                        <div class="d-flex align-items-center">
                            <iconify-icon icon="ic:baseline-error" class="text-danger me-8"></iconify-icon>
                            <div class="text-sm text-danger">
                                <div class="fw-semibold">Validation Errors:</div>
                                <ul class="mb-0 ps-16">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Registration Form - Maintained Layout -->
                <form action="{{ route('unit.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <!-- Start Grid -->
                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-6">
                            <!-- Unit Type -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Unit Type <span class="text-danger-600">*</span>
                                </label>
                                <select class="form-control radius-8 system-select" id="unit_type" name="unit_type"
                                    required>
                                    <option value="">Select Unit Type</option>
                                    @foreach (['Flat', 'Section', 'Office', 'Shop', 'Other'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('unit_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Unit Name <span class="text-danger-600">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-50 border-primary-100">UN</span>
                                    <input type="text" class="form-control radius-8 system-input" id="unit_name"
                                        name="unit_name" min="0" step="0.01" value="{{ old('unit_name') }}"
                                        required>
                                </div>
                            </div>
                            <!-- Monthly Rent -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Monthly Rent <span class="text-danger-600">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-50 border-primary-100">RM</span>
                                    <input type="number" class="form-control radius-8 system-input" id="unit_price"
                                        name="unit_price" min="0" step="0.01" value="{{ old('unit_price') }}"
                                        required>
                                </div>
                            </div>

                        </div>


                        <!-- Column 2 -->
                        <div class="col-md-6">
                            <!-- Availability -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Availability <span class="text-danger-600">*</span>
                                </label>
                                <div class="d-flex flex-column gap-8">
                                    <div class="form-check radio-card align-items-center p-12 radius-8">
                                        <input class="form-check-input" type="radio" name="is_available"
                                            id="available_yes" value="1"
                                            {{ old('is_available', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm ms-8" for="available_yes">
                                            Available
                                        </label>
                                    </div>
                                    <div class="form-check radio-card align-items-center p-12 radius-8">
                                        <input class="form-check-input" type="radio" name="is_available" id="available_no"
                                            value="0" {{ old('is_available') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm ms-8" for="available_no">
                                            Occupied
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Occupied By -->
                            <div class="mb-16">
                                <label class="form-label text-primary-light text-sm mb-8">
                                    Occupied By <span class="text-danger-600">*</span>
                                </label>
                                <div class="d-flex flex-column gap-8">
                                    <div class="form-check radio-card align-items-center p-12 radius-8">
                                        <input class="form-check-input" type="radio" name="is_owner"
                                            id="occupant_owner" value="1" {{ old('is_owner') ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm ms-8" for="occupant_owner">
                                            Property Owner
                                        </label>
                                    </div>
                                    <div class="form-check radio-card align-items-center p-12 radius-8">
                                        <input class="form-check-input" type="radio" name="is_owner"
                                            id="occupant_tenant" value="0" {{ !old('is_owner') ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm ms-8" for="occupant_tenant">
                                            Tenant
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- End Grid -->

                        <!-- Buttons -->
                        <div class="d-flex align-items-center justify-content-center gap-16 mt-24">
                            <a href="{{ route('unit.create') }}"
                                class="btn btn-outline-danger-600 text-danger-600 btn-medium px-56 py-12 radius-8">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-medium px-56 py-12 radius-8">
                                Save
                            </button>
                        </div>
                </form>

            </div>
        </div>
    @endif
@endsection
