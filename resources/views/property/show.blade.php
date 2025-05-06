@extends('layout.layout')

@php
    $title = 'Edit Property';
    $subTitle = 'Edit Property Details';
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">



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

                            <form action="{{ route('property.update', $property->id) }}" method="POST"
                                enctype="multipart/form-data">
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
                                            <option value="Deegaan"
                                                {{ old('designation', $property->designation) == 'Deegaan' ? 'selected' : '' }}>
                                                Deegaan</option>
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
                                Swal.fire('Approved!', 'The property has been approved.', 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);

                            } else {
                                Swal.fire('Failed!', 'Approval failed: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'There was an error with the request.', 'error');
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
