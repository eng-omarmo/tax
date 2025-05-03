@extends('layout.layout')

@php
    $title = 'Rent Registration';
    $subTitle = 'Manage Rent';
@endphp

@section('content')

        <!-- Search Form -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Rent Registration Form -->
        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <div class="card border mb-4">
                            <div class="card-body">
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endforeach

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <form action="{{ route('rent.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                                    <input type="hidden" name="property_id" value="{{ $unit->property->id }}">

                                    <div class="mb-4">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Property Name
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_name"
                                            name="property_name" value="{{ $unit->property->property_name }}" readonly>
                                    </div>
                                    <div class="mb-4">
                                        <label for="property_phone"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Property Phone
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_phone"
                                            name="property_phone" value="{{ $unit->property->property_phone }}" readonly>
                                    </div>
                                    <div class="mb-4">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Property unit Info
                                        </label>
                                        <input type="text" class="form-control radius-8" id="unit_name"
                                            name="unit_name" value="{{ $unit->unit_name . ' - ' . $unit->unit_number  }}" readonly>
                                    </div>


                                    <div class="mb-4">
                                        <label for="rent_amount"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Rent Amount <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="rent_amount"
                                            name="rent_amount" value="{{ $unit->unit_price }}" readonly>
                                    </div>

                                    <div class="mb-4">
                                        <label for="tenant_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Tenant <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="tenant_name"
                                            name="tenant_name" placeholder="Enter Tenant Name"
                                            value="{{ old('tenant_name') }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="tenant_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Tenant Phone <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="tenant_phone"
                                            name="tenant_phone" placeholder="Enter Tenant Phone"
                                            value="{{ old('tenant_phone') }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="rent_document"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Rent Agreement Document  <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="file" class="form-control radius-8" id="rent_document"
                                            name="rent_document" value="{{ old('rent_document') }}" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="rent_start_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Rent Start Date <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="date" class="form-control radius-8" id="rent_start_date"
                                            name="rent_start_date" value="{{ old('rent_start_date') }}" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="rent_end_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
                                            Rent End Date (Optional)
                                        </label>
                                        <input type="date" class="form-control radius-8" id="rent_end_date"
                                            name="rent_end_date" value="{{ old('rent_end_date') }}">
                                    </div>

                                    <div class="mb-4">
                                        <label for="status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-2">
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

                                        <button type="button" onclick="document.getElementById('cancelForm').submit()"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Form -->
        <form id="cancelForm" action="{{ route('monitor.index') }}" method="GET" class="d-flex align-items-center">
        </form>

@endsection
