@extends('layout.layout')

@php
    $title = 'Edit Property';
    $subTitle = 'Edit Property Details';
@endphp

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">



    @if ($property->monitoring_status != 'Approved')
        <div
            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="ms-auto d-flex align-items-center gap-3 flex-wrap">
                <button id="approveBtn" class="btn btn-success text-sm btn-sm px-12 py-12 radius-4">
                    Approve Property
                </button>
                <a href="javascript:void(0);" id="rejectAllBtn" class="btn btn-danger text-sm btn-sm px-12 py-12 radius-4">
                    Reject Property
                </a>
            </div>
        </div>
    @endif



    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-10 col-xl-12 col-lg-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-8 col-xl-8 col-lg-8">
                                    <h6 class="mb-0">Edit Property</h6>
                                </div>
                                <div class="mapouter flex justify-center">
                                    <div class="gmap_canvas">
                                        <iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
                                            marginwidth="0"
                                            src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=hodan&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">
                                        </iframe>
                                        <a
                                            href="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=olow tower&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a
                                                href="https://sprunkin.com/">Sprunki</a>
                                    </div>
                                    <style>
                                        .mapouter {
                                            position: relative;
                                            text-align: center;
                                            width: 100%;
                                            /* Set to take up full width of parent */
                                            height: 0;
                                            padding-bottom: 56.25%;
                                            /* Maintain aspect ratio (16:9) */
                                        }

                                        .gmap_canvas {
                                            overflow: hidden;
                                            background: none !important;
                                            width: 100%;
                                            /* Responsive width */
                                            height: 100%;
                                            /* Responsive height */
                                            position: absolute;
                                            /* Absolute positioning for aspect ratio */
                                            top: 0;
                                            left: 0;
                                        }

                                        .gmap_iframe {
                                            width: 100% !important;
                                            /* Fully responsive width */
                                            height: 100% !important;
                                            /* Fully responsive height */
                                        }
                                    </style>

                                </div>
                            </div>
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
                                        <label for="status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="status" name="status">
                                            <option value="Active"
                                                {{ old('status', $property->status) == 'Active' ? 'selected' : '' }}>{{$property->status}}
                                            </option>

                                            <option value="Active"
                                                {{ old('status', $property->status) == 'Active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="Inactive"
                                                {{ old('status', $property->status) == 'Inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="monitoring_status"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Monitoring Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="monitoring_status"
                                            name="monitoring_status">
                                            <option value="Pending"
                                                {{ old('monitoring_status', $property->monitoring_status) == 'Pending' ? 'selected' : '' }}>
                                                {{ $property->monitoring_status }} </option>
                                            <option value="Approved"
                                                {{ old('monitoring_status', $property->monitoring_status) == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>


                                            <option value="Approved"
                                                {{ old('monitoring_status', $property->monitoring_status) == 'Approved' ? 'selected' : '' }}>
                                                Approved</option>
                                        </select>
                                    </div>


                                    <div class="col-md-6 mb-20">
                                        <label for="district"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            District <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="district_id"
                                            name="district_id">
                                            <option value ="{{ $property->district_id }}">{{ $property->district->name }}
                                            </option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="house_type"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            House Type <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="house_type"
                                            name="house_type">
                                            <option value="Villa"
                                                {{ old('house_type', $property->house_type) == 'Villa' ? 'selected' : '' }}>
                                                Villa</option>
                                            <option value="Apartment"
                                                {{ old('house_type', $property->house_type) == 'Apartment' ? 'selected' : '' }}>
                                                Apartment</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-20">
                                        <label for="branch"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Branch
                                        </label>
                                        <select class="form-control radius-8 form-select" id="branch" name="branch">

                                            <option value="{{ $property->branch_id }}" selected>
                                                {{ $property->branch->name }}</option>

                                            @foreach ($branches as $branch)
                                                @if ($branch->id !== $property->branch_id)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>

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
                                        <label for="image" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Image
                                        </label>
                                        <input type="file" class="form-control radius-8" id="image" name="image" onchange="previewImage()">
                                        <div id="previewImage" class="mt-2">
                                            <!-- Document preview will be displayed here -->
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="document" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Document
                                        </label>
                                        <input type="file" class="form-control radius-8" id="document" name="document" onchange="previewDocument()">
                                        <div id="documentPreview" class="mt-2">
                                            <!-- Document preview will be displayed here -->
                                        </div>
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
                                    <a href="{{ route('property.index') }}"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </a>
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
    <!-- Include jQuery before any script that uses it -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include SweetAlert2 after jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#approveBtn').on('click', function() {
                Swal.fire({

                    title: 'Are you sure?',
                    text: 'Do you want to approve this property?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, approve it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true,

                    willOpen: () => {
                        Swal.showLoading();
                    },
                    didClose: () => {
                        Swal.hideLoading();
                    },
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        confirmButton: 'custom-swal-confirm-button',
                        cancelButton: 'custom-swal-cancel-button'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('monitor.approve') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                body: JSON.stringify({
                                    property_id: {{ $property->id }}
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.success) {
                                    Swal.fire('Approved!', 'The property has been approved.',
                                        'success');
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);

                                } else {
                                    Swal.fire('Failed!', 'Approval failed: ' + data.message,
                                        'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'There was an error with the request.',
                                    'error');
                            });
                    } else {
                        Swal.fire('Cancelled', 'The approval has been cancelled.', 'info');
                    }
                });
            });
        });
    </script>

    <style>
        /* Custom SweetAlert Popup Styling */
        .custom-swal-popup {
            background: #fefefe;
            border-radius: 12px;
            border: 2px solid #6c757d;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Title styling */
        .custom-swal-title {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
        }

        /* Confirm Button Styling */
        .custom-swal-confirm-button {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .custom-swal-confirm-button:hover {
            background-color: #218838;
            transform: translateY(-3px);
        }

        /* Cancel Button Styling */
        .custom-swal-cancel-button {
            background-color: #dc3545;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .custom-swal-cancel-button:hover {
            background-color: #c82333;
            transform: translateY(-3px);
        }

        /* Animation for fade-in effect */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>



@endsection
<script>
    function previewDocument() {
        const documentInput = document.getElementById('document');
        const documentPreview = document.getElementById('documentPreview');
        const file = documentInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                documentPreview.innerHTML = `<embed src="${e.target.result}" width="100%" height="200px" type="application/pdf">`;
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
                documentPreview.innerHTML = `<embed src="${e.target.result}" width="100%" height="200px" type="application/pdf">`;
            };
            reader.readAsDataURL(file);
        } else {
            documentPreview.innerHTML = '';
        }
    }
</script>
