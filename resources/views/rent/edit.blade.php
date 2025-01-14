@extends('layout.layout')

@php
    $title = 'Edit Rent';
    $subTitle = 'Edit rent details';
@endphp

@section('content')
    <!-- Rent Edit Form -->
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            <!-- Display Errors -->
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

                            <!-- Rent Edit Form -->
                            <form action="{{ route('rent.update', $rent->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-20">
                                    <label for="property_id" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="property_id" name="property_id" value="{{ $rent->property->property_name }}"
                                        readonly required>
                                </div>
                                <!-- Unit ID -->
                                <div class="mb-20">
                                    <label for="unit_id" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Unit  <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="unit_id" name="unit_id"
                                        placeholder="Enter Unit ID" value="{{ old('unit_id', $rent->unit->unit_type) }}" readonly required>
                                </div>


                                <div class="mb-20">
                                    <label for="tenant_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tenant Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_name" name="tenant_name"
                                        placeholder="Enter Tenant ID" value="{{ $rent->tenant_name }}" readonly required>
                                </div>


                                <!-- Rent Code -->
                                <div class="mb-20">
                                    <label for="rent_code" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rent Code <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="rent_code" name="rent_code"
                                        placeholder="Enter Rent Code" value="{{ old('rent_code', $rent->rent_code) }}" readonly
                                        required>
                                </div>

                                <!-- Rent Amount -->
                                <div class="mb-20">
                                    <label for="rent_amount" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rent Amount <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="number" class="form-control radius-8" id="rent_amount" name="rent_amount"
                                        placeholder="Enter Rent Amount" value="{{ old('rent_amount', $rent->rent_amount) }}" readonly
                                        required>
                                </div>

                                <!-- Rent Start Date -->
                                <div class="mb-20">
                                    <label for="rent_start_date"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rent Start Date <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rent_start_date"
                                        name="rent_start_date" value="{{ old('rent_start_date', $rent->rent_start_date) }}"
                                        required>
                                </div>

                                <!-- Rent End Date -->
                                <div class="mb-20">
                                    <label for="rent_end_date"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rent End Date <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rent_end_date"
                                        name="rent_end_date" value="{{ old('rent_end_date', $rent->rent_end_date) }}"
                                        required>
                                </div>

                                <div class="mb-20">
                                    <label for="rent_document" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rent Document <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="file" class="form-control radius-8" id="rent_document" name="rent_document" value="{{ old('rent_document', $rent->rent_document) }}" required>
                                    @if ($rent->rent_document)
                                        <a href="{{ Storage::url($rent->rent_document) }}" target="_blank" class="text-primary-600 underline hover:text-primary-800 mt-4 block">View Rent Document</a>
                                    @endif
                                </div>




                                <!-- Status -->
                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status"
                                        required>
                                        <option value="Active"
                                            {{ old('status', $rent->status) == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive"
                                            {{ old('status', $rent->status) == 'Inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('rent.index') }}"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                        class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Update Rent
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
