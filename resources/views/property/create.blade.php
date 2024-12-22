@extends('layout.layout')

@php
    $title = 'Register Property';
    $subTitle = 'Property Registration';
    $script = '<script>
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
    </script>';
@endphp

@section('content')

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row justify-content-center">
            <div class="col-xxl-10 col-xl-12 col-lg-12">
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

                        <form action="{{ route('property.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-20">
                                    <label for="property_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="property_name" name="property_name" placeholder="Enter property name" value="{{ old('property_name') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="property_phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Property Phone
                                    </label>
                                    <input type="text" class="form-control radius-8" id="property_phone" name="property_phone" placeholder="Enter property phone" value="{{ old('property_phone') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="nbr" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        NBR
                                    </label>
                                    <input type="text" class="form-control radius-8" id="nbr" name="nbr" placeholder="Enter NBR" value="{{ old('nbr') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="house_code" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        House Code
                                    </label>
                                    <input type="text" class="form-control radius-8" id="house_code" name="house_code" placeholder="Enter house code" value="{{ old('house_code') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="tenant_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tenant Name
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_name" name="tenant_name" placeholder="Enter tenant name" value="{{ old('tenant_name') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="tenant_phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tenant Phone
                                    </label>
                                    <input type="text" class="form-control radius-8" id="tenant_phone" name="tenant_phone" placeholder="Enter tenant phone" value="{{ old('tenant_phone') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="branch" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Branch
                                    </label>
                                    <input type="text" class="form-control radius-8" id="branch" name="branch" placeholder="Enter branch name" value="{{ old('branch') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="zone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Zone
                                    </label>
                                    <input type="text" class="form-control radius-8" id="zone" name="zone" placeholder="Enter zone name" value="{{ old('zone') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="house_type" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        House Type
                                    </label>
                                  <select name="house_type" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                                    <option value="">House Type</option>
                                    <option value="villa">Villa</option>
                                    <option value="Apartment">Apartment</option>

                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="house_rent" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        House Rent
                                    </label>
                                    <input type="text" class="form-control radius-8" id="house_rent" name="house_rent" placeholder="Enter house rent" value="{{ old('house_rent') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="latitude" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Latitude <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="latitude" name="latitude" placeholder="Enter latitude" value="{{ old('latitude') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="longitude" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Longitude <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="longitude" name="longitude" placeholder="Enter longitude" value="{{ old('longitude') }}">
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
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

@endsection
