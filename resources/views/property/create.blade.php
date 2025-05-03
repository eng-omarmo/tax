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




    @if (!isset($lanlord) || empty($lanlord->id))
        <!-- Search Form -->

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('property.lanlord.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="search_lanlord"
                                    name="search_lanlord" placeholder="Enter lanlord Phone Number"
                                    value="{{ old('search_lanlord') }}" required>
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
                                    <input type="hidden" name="lanlord_id" value="{{ $lanlord->id }}">


                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord Name
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" placeholder="Enter property name"
                                                value="{{ $lanlord->name }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" placeholder="Enter property name"
                                                value="{{ $lanlord->phone_number }}" readonly>
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
                                                Image
                                            </label>
                                            <input type="file" class="form-control radius-8" id="image"
                                                name="image">
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
    @endif


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
</script>
