@extends('layout.layout')

@php
    $title = 'Register Property';
    $subTitle = 'Property Registration';
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

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif


                                <form action="{{ route('property.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="lanlord_id" value="{{ $landlord->id }}">


                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord Name
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" placeholder="Enter property name"
                                                value="{{ $landlord->name }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" placeholder="Enter property name"
                                                value="{{ $landlord->phone_number }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Name <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" placeholder="Enter property name"
                                                value="{{ old('property_name') }}">
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" placeholder="Enter property phone"
                                                value="{{ old('property_phone') }}">
                                        </div>

                                        <div class="col-md-6 mb-20">
                                            <label for="house_type"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                House Type <span class="text-danger-600">*</span>
                                            </label>
                                            <select class="form-control radius-8 form-select" id="house_type"
                                                name="house_type">
                                                <option value="Villa">Villa</option>
                                                <option value="Apartment">Apartment</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-20">
                                            <label for="district"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                District <span class="text-danger-600">*</span>
                                            </label>
                                            <select class="form-control radius-8 form-select" id="district_id"
                                                name="district_id" onchange="fetchBranches()">
                                                <option value="">Choose District</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-20">
                                            <label for="branch"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Branch
                                            </label>
                                            <select class="form-control radius-8 form-select" id="branch_id"
                                                name="branch_id">
                                                <option value="">Choose Branch</option>
                                            </select>
                                        </div>


                                        <div class="col-md-6 mb-20">
                                            <label for="zone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Zone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="zone"
                                                name="zone" placeholder="Enter zone name"
                                                value="{{ old('zone') }}">
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="latitude"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Latitude <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="number" class="form-control radius-8" id="latitude"
                                                name="latitude" placeholder="Enter latitude"
                                                value="{{ old('latitude') }}">
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="longitude"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Longitude <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="number" class="form-control radius-8" id="longitude"
                                                name="longitude" placeholder="Enter longitude"
                                                value="{{ old('longitude') }}">
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="image"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Image
                                            </label>
                                            <input type="file" class="form-control radius-8" id="image"
                                                name="image" onchange="previewImage() ">
                                                <div id="previewImage" class="mt-2">
                                                    <!-- Image preview will be displayed here -->
                                                </div>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="document"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property document
                                            </label>
                                            <input type="file" class="form-control radius-8" id="document"
                                                name="document" onchange="previewDocument()">
                                                <div id="documentPreview" class="mt-2">
                                                    <!-- Document preview will be displayed here -->
                                                </div>
                                        </div>

                                    </div>


                                    <!-- Add this before the form buttons -->
                                    <div class="row mb-20">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="skip_monitoring" name="skip_monitoring" value="1">
                                                <label class="form-check-label" for="skip_monitoring">Skip monitoring (set as Approved)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="continue_to_unit" name="continue_to_unit" value="1" checked>
                                                <label class="form-check-label" for="continue_to_unit">Continue to unit registration after saving</label>
                                            </div>
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
<script>
    function fetchBranches() {
        var districtId = document.getElementById('district_id').value;
        if (districtId) {
            var url = `{{ url('property/branches') }}/${districtId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {

                    var branchSelect = document.getElementById('branch_id');
                    branchSelect.innerHTML = '<option value="">Choose Branch</option>';
                    data.forEach(branch => {
                        branchSelect.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                    });
                })
                .catch(error => console.error('Error fetching branches:', error));
        } else {
            document.getElementById('branch_id').innerHTML = '<option value="">Choose Branch</option>';
        }
    }

    function previewDocument() {
        const documentInput = document.getElementById('document');
        const documentPreview = document.getElementById('documentPreview');
        const file = documentInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                documentPreview.innerHTML =
                    `<embed src="${e.target.result}" width="100%" height="200px" type="application/pdf">`;
            };
            reader.readAsDataURL(file);
        } else {
            documentPreview.innerHTML = '';
        }
    }

    function previewImage() {
        const documentInput = document.getElementById('image');
        const documentPreview = document.getElementById('previewImage');
        const file = documentInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                documentPreview.innerHTML =
                    `<embed src="${e.target.result}" width="100%" height="200px" type="application/pdf">`;
            };
            reader.readAsDataURL(file);
        } else {
            documentPreview.innerHTML = '';
        }
    }
</script>
