@extends('layout.layout')
@php
    $title = 'Update User';
    $subTitle = 'Update User';
@endphp

@section('content')

<div class="tenant-details">
    <p><strong>Balance Owed:</strong>
        @if($tenant->calculateBalance() > 0)
            ${{ number_format($tenant->calculateBalance(), 2) }}
        @else
            No balance owed.
        @endif
    </p>
</div>


    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('tenant.update', $tenant->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-20">
                                    <label for="tenant_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        update Tenant property <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="property_id" name="property_id" required>
                                        <option value="">Select Property</option>
                                        @foreach ($properties as $property)
                                            <option value="{{ $property->id }}" {{ $property->id == $tenant->property_id ? 'selected' : '' }}>
                                                {{ $property->property_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tenant Name -->
                                <div class="mb-20">
                                    <label for="tenant_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tenant Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_name"
                                           name="tenant_name" placeholder="Enter Tenant Name"
                                           value="{{ old('tenant_name', $tenant->tenant_name) }}" required>
                                </div>

                                <!-- Tenant Phone -->
                                <div class="mb-20">
                                    <label for="tenant_phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Phone Number <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_phone"
                                           name="tenant_phone" placeholder="Enter Phone Number"
                                           value="{{ old('tenant_phone', $tenant->tenant_phone) }}" required>
                                </div>

                                <!-- Rental Dates -->
                                <div class="mb-20">
                                    <label for="rental_start_date" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rental Start Date <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rental_start_date"
                                           name="rental_start_date" value="{{ old('rental_start_date', $tenant->rental_start_date) }}" required>
                                </div>

                                <div class="mb-20">
                                    <label for="rental_end_date" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rental End Date (Optional)
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rental_end_date"
                                           name="rental_end_date" value="{{ old('rental_end_date', $tenant->rental_end_date) }}">
                                </div>

                                <!-- Reference -->
                                <div class="mb-20">
                                    <label for="reference" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Reference Person <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="reference" name="reference"
                                           placeholder="Enter Reference Name" value="{{ old('reference', $tenant->reference) }}" required>
                                </div>

                                <!-- Tax Fee -->
                                <div class="mb-20">
                                    <label for="tax_fee" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tax Fee (Optional)
                                    </label>
                                    <input type="number" step="0.01" class="form-control radius-8"
                                           id="tax_fee" name="tax_fee" placeholder="Enter Tax Fee"
                                           value="{{ old('tax_fee', $tenant->tax_fee) }}">
                                </div>

                                <!-- Status -->
                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status" required>
                                        <option value="Active" {{ old('status', $tenant->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $tenant->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('tenant.index') }}" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Update
                                    </button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
