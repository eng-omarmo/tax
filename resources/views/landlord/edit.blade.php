@extends('layout.layout')

@php
    $title = 'Edit Landlord';
    $subTitle = 'Edit Landlord Details';
@endphp

@section('content')
{{-- @if (session('error'))
<div class="alert alert-danger   alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
    @endif --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header  border-bottom p-24">
                        <h5 class="card-title text-primary-600 mb-0">
                            <i class="ri-user-settings-line me-2"></i>Edit Landlord Information
                        </h5>
                    </div>

                    <div class="card-body p-24">
                        <form action="{{ route('lanlord.update', $lanlord->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Profile Image Column -->
                                <div class="col-md-4">
                                    <div class="form-section mb-4">
                                        <h6 class="section-header text-primary-600 mb-3">
                                            <i class="ri-camera-line me-2"></i>Profile Image
                                        </h6>

                                        <div class="text-center">
                                            <div class="image-preview mb-3">

                                                <img id="profileImagePreview"
                                                    src="{{ isset($lanlord->user) && $lanlord->user->profile_image ? asset('storage/' . $lanlord->user->profile_image) : asset('assets/images/default-avatar.png') }}"
                                                    alt="Profile Preview" class="img-thumbnail rounded-circle shadow-sm"
                                                    style="width: 180px; height: 180px; object-fit: cover; border: 2px solid var(--bs-primary-subtle);">
                                            </div>

                                            <div class="form-group">
                                                <input type="file"
                                                    class="form-control radius-8 @error('profile_image') is-invalid @enderror"
                                                    id="profile_image" name="profile_image" accept="image/*"
                                                    onchange="previewImage(event)">
                                                @error('profile_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <small class="text-muted d-block mt-2 text-primary-light">
                                                <i class="ri-information-line me-1"></i>
                                                Allowed formats: JPG, PNG, GIF. Max size: 2MB
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information Column -->
                                <div class="col-md-8">
                                    <div class="form-section mb-4">
                                        <h6 class="section-header text-primary-600 mb-3">
                                            <i class="ri-profile-line me-2"></i>Personal Information
                                        </h6>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label text-secondary-600">Full
                                                    Name</label>
                                                <input type="text"
                                                    class="form-control radius-8 @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name', $lanlord->name) }}"
                                                    placeholder="Enter full name">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email" class="form-label text-secondary-600">Email
                                                    Address</label>
                                                <input type="email"
                                                    class="form-control radius-8 @error('email') is-invalid @enderror"
                                                    id="email" name="email"
                                                    value="{{ old('email', $lanlord->email) }}"
                                                    placeholder="name@example.com">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="phone_number" class="form-label text-secondary-600">Phone
                                                    Number</label>
                                                <input type="tel"
                                                    class="form-control radius-8 @error('phone_number') is-invalid @enderror"
                                                    id="phone_number" name="phone_number"
                                                    value="{{ old('phone_number', $lanlord->phone_number) }}"
                                                    placeholder="+1 (555) 123-4567">
                                                @error('phone_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="address" class="form-label text-secondary-600">Address</label>
                                                <input type="text"
                                                    class="form-control radius-8 @error('address') is-invalid @enderror"
                                                    id="address" name="address"
                                                    value="{{ old('address', $lanlord->address) }}"
                                                    placeholder="Enter address">
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-4">
                                <a href="{{ route('lanlord.index') }}" class="btn btn-danger btn-sm">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary bt-sm ">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Properties and Units Section -->
                @if ($lanlord->properties && $lanlord->properties->count() > 0)
                    <div class="card border-0 shadow-sm mt-12">
                        <div class="card-header  border-bottom p-24">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-primary-600 mb-0">
                                    <i class="ri-building-2-line me-2"></i>Properties & Units
                                </h5>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $lanlord->properties->count() }} Properties
                                    </span>
                                    <span class="badge bg-success-subtle text-success">
                                        {{ $lanlord->properties->sum(function ($property) {return $property->units->count();}) }}
                                        Total Units
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-24">
                            <div class="row g-4">
                                @foreach ($lanlord->properties as $property)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="property-card border rounded-3 p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="text-primary-600 mb-0">{{ $property->name }}</h6>
                                                <div class="property-status">
                                                    <span class="badge bg-info-subtle text-info">
                                                        {{ $property->units->count() }} Units
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="property-details mb-3">
                                                <p class="mb-2 text-primary-light">
                                                    <i class="ri-map-pin-line me-2"></i>N/A
                                                </p>
                                                <p class="mb-2 text-primary-light">
                                                    <i class="ri-phone-line me-2"></i>{{ $property->property_phone }}
                                                </p>
                                            </div>

                                            <div class="units-summary  rounded-2 mb-3">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="d-flex align-items-center">
                                                            <div class="status-dot bg-success rounded-circle me-2"></div>
                                                            <small class="text-primary-light">
                                                                {{ $property->units->where('is_available', 0)->count() }}
                                                                Available
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="d-flex align-items-center">
                                                            <div class="status-dot bg-danger rounded-circle me-2"></div>
                                                            <small class="text-primary-light">
                                                                {{ $property->units->where('is_available', 1)->count() }}
                                                                Occupied
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="property-actions d-flex gap-2">
                                                <a href="{{ route('monitor.show', $property->id) }}"
                                                    class="btn btn-light-primary btn-sm  text-primary-light flex-grow-1">
                                                    <i class="ri-eye-line me-1 text-primary-light"></i>View Details
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body p-24 text-center">
                            <div class="empty-state">
                                <i class="ri-building-2-line text-muted" style="font-size: 48px;"></i>
                                <h6 class="mt-3 text-secondary-600">No Properties Found</h6>
                                <p class="text-muted">This landlord hasn't added any properties yet.</p>
                                <a href="{{ route('property.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="ri-add-line me-1"></i>Add New Property
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- <script>
        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('profileImagePreview');

            reader.onload = function() {
                if (reader.readyState === 2) {
                    preview.src = reader.result;
                }
            }

            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script> --}}
@endsection
