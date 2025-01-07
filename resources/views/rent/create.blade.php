@extends('layout.layout')

@php
    $title = 'Rent Registration';
    $subTitle = 'Manage Rent';
@endphp

@section('content')

    @if (empty($unit))
        <!-- Search Form -->
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('rent.property.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="search_unit_number"
                                    name="search_unit_number" placeholder="Enter Property Phone Number"
                                    value="{{ old('search_unit_number') }}" required>
                            </div>
                            <button type="submit"
                                class="btn btn-primary text-sm btn-medium px-4 py-2 d-flex align-items-center ms-2">
                                <iconify-icon icon="ic:baseline-search" class="icon text-xl line-height-1"></iconify-icon>
                                <span class="ms-1">Search</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Rent Registration Form -->
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

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('rent.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                                    <input type="hidden" name="property_id" value="{{ $unit->property->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Name
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" value="{{ $unit->property->property_name }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $unit->property->property_phone }}" readonly>
                                        </div>

                                        <!-- Rent Amount -->
                                        <div class="mb-20">
                                            <label for="rent_amount"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Rent Amount <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text" class="form-control radius-8" id="rent_amount"
                                                name="rent_amount" value="{{ $unit->unit_price }}" readonly>
                                        </div>

                                    </div>



                                    <!-- Tenant -->
                                    <div class="mb-20">
                                        <label for="tenant_id"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Tenant <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="tenant_id" name="tenant_id"
                                            required>
                                            <option value="">Select Tenant</option>
                                            @foreach ($tenants as $tenant)
                                                <option value="{{ $tenant->id }}"
                                                    {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                    {{ $tenant->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <!-- Rent Start Date -->
                                    <div class="mb-20">
                                        <label for="rent_start_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Rent Start Date <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="date" class="form-control radius-8" id="rent_start_date"
                                            name="rent_start_date" value="{{ old('rent_start_date') }}" required>
                                    </div>

                                    <!-- Rent End Date -->
                                    <div class="mb-20">
                                        <label for="rent_end_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Rent End Date (Optional)
                                        </label>
                                        <input type="date" class="form-control radius-8" id="rent_end_date"
                                            name="rent_end_date" value="{{ old('rent_end_date') }}">
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-20">
                                        <label for="status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="status" name="status"
                                            required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="terminated"
                                                {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                            Register Rent
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
