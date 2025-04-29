@extends('layout.layout')

@php
    $title = 'Edit unit';
    $subTitle = 'Edit unit details';
@endphp

@section('content')
    <!-- unit Edit Form -->
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
                            <form action="{{ route('unit.update', $unit->id) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- This is used for method spoofing to send a PUT request -->
                                <input type="hidden" name="property_id" value="{{ $unit->property_id }}">

                                <div class="row">
                                    <div class="col-md-6 mb-20">
                                        <label for="property_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Name
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_name"
                                               name="property_name" value="{{ $unit->property->property_name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="property_phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Phone
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_phone"
                                               name="property_phone" value="{{ $unit->property->property_phone }}" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-20">
                                        <label for="unit_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Unit Name <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="unit_name"
                                               placeholder="Enter Unit Name" name="unit_name"
                                               value="{{ old('unit_name', $unit->unit_name) }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-20">
                                        <label for="unit_price" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Rent Price <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="unit_price"
                                               placeholder="Enter Unit Price" name="unit_price"
                                               value="{{ old('unit_price', $unit->unit_price) }}" required>
                                    </div>

                                    <div class="mb-20">
                                        <label for="unit_type" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Unit Type <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="unit_type" name="unit_type" required>
                                            <option value="">Select Unit Type</option>
                                            <option value="Flat" {{ old('unit_type', $unit->unit_type) == 'Flat' ? 'selected' : '' }}>Flat</option>
                                            <option value="Section" {{ old('unit_type', $unit->unit_type) == 'Section' ? 'selected' : '' }}>Section</option>
                                            <option value="Office" {{ old('unit_type', $unit->unit_type) == 'Office' ? 'selected' : '' }}>Office</option>
                                            <option value="Shop" {{ old('unit_type', $unit->unit_type) == 'Shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="Other" {{ old('unit_type', $unit->unit_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-20">
                                        <label for="unit_availability" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Unit Availability <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="is_available" name="is_available" required>
                                            <option value="0" {{ old('is_available', $unit->is_available) == 0 ? 'selected' : '' }}>Available</option>
                                            <option value="1" {{ old('is_available', $unit->is_available) == 1 ? 'selected' : '' }}>occupied</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button"
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
@endsection
