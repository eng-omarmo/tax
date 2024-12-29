@extends('layout.layout')

@php
    $title = 'Edit Property';
    $subTitle = 'Edit Property Details';
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

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                            <form action="{{ route('property.update', $property->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    <div class="col-md-6 mb-20">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Lanlord Name <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_name"
                                            name="property_name" placeholder="Enter property name"
                                            value="{{ old('property_name', $property->landlord->name) }}">
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Lanlord Phone <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_name"
                                            name="property_name" placeholder="Enter property name"
                                            value="{{ old('property_name', $property->landlord->phone_number) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Name <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_name"
                                            name="property_name" placeholder="Enter property name"
                                            value="{{ old('property_name', $property->property_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="property_phone"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Phone
                                        </label>
                                        <input type="text" class="form-control radius-8" id="property_phone"
                                            name="property_phone" placeholder="Enter property phone"
                                            value="{{ old('property_phone', $property->property_phone) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="nbr"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            NBR
                                        </label>
                                        <input type="text" class="form-control radius-8" id="nbr" name="nbr"
                                            placeholder="Enter NBR" value="{{ old('nbr', $property->nbr) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="house_code"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            House Code
                                        </label>
                                        <input type="text" class="form-control radius-8" id="house_code"
                                            name="house_code" placeholder="Enter house code"
                                            value="{{ old('house_code', $property->house_code) }}">
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="status" name="status">
                                            <option value="Active" {{ old('status', $property->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $property->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="monitoring_status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Monitoring Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="monitoring_status"
                                            name="monitoring_status">
                                            <option value="Pending" {{ old('monitoring_status', $property->monitoring_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ old('monitoring_status', $property->monitoring_status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        </select>
                                    </div>


                                    <div class="col-md-6 mb-20">
                                        <label for="district"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            District <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="district_id" name="district_id">
                                            <option value ="{{ $property->district_id }}">{{ $property->district->name }}</option>
                                         @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                         @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="designation"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Designation <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="designation"
                                            name="designation">
                                            <option value="">Choose Designation</option>
                                            <option value="Deegaan" {{ old('designation', $property->designation) == 'Deegaan' ? 'selected' : '' }}>Deegaan</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="house_type"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            House Type <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="house_type" name="house_type">
                                            <option value="Villa" {{ old('house_type', $property->house_type) == 'Villa' ? 'selected' : '' }}>Villa</option>
                                            <option value="Apartment" {{ old('house_type', $property->house_type) == 'Apartment' ? 'selected' : '' }}>Apartment</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="house_rent"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            House Rent
                                        </label>
                                        <input type="text" class="form-control radius-8" id="house_rent"
                                            name="house_rent" placeholder="Enter house rent"
                                            value="{{ old('house_rent', $property->house_rent) }}">

                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="quarterly_tax_fee"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Quarterly Tax Fee
                                        </label>
                                        <input type="text" class="form-control radius-8" id="quarterly_tax_fee"
                                            name="quarterly_tax_fee" placeholder="Enter quarterly tax fee"
                                            value="{{ old('quarterly_tax_fee', $property->quarterly_tax_fee) }}">

                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="yearly_tax_fee"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Yearly Tax Fee
                                        </label>
                                        <input type="text" class="form-control radius-8" id="yearly_tax_fee"
                                            name="yearly_tax_fee" placeholder="Enter yearly tax fee"
                                            value="{{ old('yearly_tax_fee', $property->yearly_tax_fee) }}">

                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="dalal_company_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Dalal Company Name
                                        </label>
                                        <input type="text" class="form-control radius-8" id="dalal_company_name"
                                            name="dalal_company_name" placeholder="Enter dalal company name"
                                            value="{{ old('dalal_company_name', $property->dalal_company_name) }}">

                                    </div>


                                    <div class="col-md-6 mb-20">
                                        <label for="branch"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Branch
                                        </label>
                                        <input type="text" class="form-control radius-8" id="branch"
                                            name="branch" placeholder="Enter branch name"
                                            value="{{ old('branch', $property->branch) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="zone"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Zone
                                        </label>
                                        <input type="text" class="form-control radius-8" id="zone"
                                            name="zone" placeholder="Enter zone name"
                                            value="{{ old('zone', $property->zone) }}">
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="latitude"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Latitude <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="latitude"
                                            name="latitude" placeholder="Enter latitude"
                                            value="{{ old('latitude', $property->latitude) }}">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="longitude"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Longitude <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" id="longitude"
                                            name="longitude" placeholder="Enter longitude"
                                            value="{{ old('longitude', $property->longitude) }}">
                                    </div>

                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
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
