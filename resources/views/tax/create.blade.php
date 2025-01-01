@extends('layout.layout')

@php
    $title = 'Tax List';
    $subTitle = 'Manage Taxes';
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

    @if (session('error'))
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

    @if (!isset($property) || empty($property->id))
        <!-- Search Form -->
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('property.tax.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="search_property"
                                    name="search_property" placeholder="Enter Property Name"
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
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('tax.store') }}" method="POST">
                                    @csrf

                                    <!-- Property Info -->
                                    <input type="hidden" name="property_id" value="{{isset($property->id) ? $property->id : '' }}">
                                    <!-- Tenant Details -->
                                    <div class="mb-20">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Name <span class="text-danger-600">*</span>
                                        </label>
                                        <div
                                            class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                            <href="{{ route('property.show', $property->id) }}">
                                            {{ $property->property_name }}
                                        </div>

                                        <label for="property_phone"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Phone <span class="text-danger-600">*</span>
                                        </label>
                                        <div
                                            class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                            {{ $property->property_phone }}
                                        </div>
                                    </div>

                                    <!-- Tax Details -->
                                    <div class="mb-20">
                                        <label for="tax_amount"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Tax Amount <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control radius-8"
                                            id="tax_amount" name="tax_amount" value ="{{ $property->yearly_tax_fee }}"
                                            readonly required>
                                    </div>
                                    <div class="mb-20">
                                        <label for="payment_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Due Date <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="date" class="form-control radius-8" id="due_date"
                                            name="due_date" required>
                                    </div>

                                    <!-- Buttons -->
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
    @endif

@endsection
