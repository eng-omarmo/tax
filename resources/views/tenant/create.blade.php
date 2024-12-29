@extends('layout.layout')

@php
    $title = 'Add Tenant';
    $subTitle = 'Add a new tenant';
    $script = '<script>
        // ================== Image Upload Js Start ===========================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                    $("#imagePreview").hide();
                    $("#imagePreview").fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        // ================== Image Upload Js End ===========================
    </script>';
@endphp

@section('content')

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



@if(!isset($property) || empty($property->id))
    <!-- Search Form -->
    <div class="card h-100 p-0 radius-12 mb-4">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <form action="{{ route('tenant.search') }}" method="GET" class="d-flex align-items-center">
                        <div class="d-flex flex-grow-1 align-items-center">
                            <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="search_property"
                                name="search_property" placeholder="Enter Property Phone Number"
                                value="{{ old('search_property') }}" required>
                        </div>
                        <!-- Add New Tenant Button -->
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

    <!-- Tenant Form -->
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                            @endif

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('tenant.store') }}" method="POST">
                                @csrf                                <!-- Property Info -->
                                <input type="hidden" name="property_id" value="{{ $property->id }}">

                                <!-- Additional Fields -->
                                <div class="mb-20">
                                    <label for="property_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property Name <span class="text-danger-600">*</span>
                                    </label>
                                    <div class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                        {{ $property->property_name  }}
                                    </div>

                                    <label for="property_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property Phone <span class="text-danger-600">*</span>
                                    </label>
                                    <div class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                        {{ $property->property_phone  }}
                                    </div>

                                    <label for="property_rent" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property Rent <span class="text-danger-600">*</span>
                                    </label>
                                    <div class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                        {{ $property->house_rent  }}
                                    </div>
                                </div>
                                <div class="mb-20">
                                    <label for="tenant_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tenant Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_name"
                                        name="tenant_name" placeholder="Enter Tenant Name"
                                        value="{{ old('tenant_name') }}" required>
                                </div>

                                <!-- Additional Fields -->
                                <div class="mb-20">
                                    <label for="tenant_phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Phone Number <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_phone"
                                        name="tenant_phone" placeholder="Enter phone number"
                                        value="{{ old('tenant_phone') }}" required>
                                </div>

                                <div class="mb-20">
                                    <label for="rental_start_date" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rental Start Date <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rental_start_date"
                                        name="rental_start_date" required>
                                </div>

                                <div class="mb-20">
                                    <label for="rental_end_date" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Rental End Date (Optional)
                                    </label>
                                    <input type="date" class="form-control radius-8" id="rental_end_date"
                                        name="rental_end_date">
                                </div>

                                <div class="mb-20">
                                    <label for="reference" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Reference Person <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="reference" name="reference"
                                        placeholder="Enter Reference Name" value="{{ old('reference') }}" required>
                                </div>

                                <div class="mb-20">

                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status" required>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
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
@endif

@endsection
