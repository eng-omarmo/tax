@extends('layout.layout')

@php
    $title = 'Edit Tenant';
    $subTitle = 'Edit tenant details';
@endphp

@section('content')
    <!-- Tenant Edit Form -->
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

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <!-- Tenant Edit Form -->
                            <form action="{{ route('tenant.update', $tenant->id) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Indicating this is an update request -->

                                <!-- Name -->
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Full Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="name"
                                        name="name" placeholder="Enter Full Name"
                                        value="{{ old('name', $tenant->user->name) }}" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-20">
                                    <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Email Address <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="email" class="form-control radius-8" id="email"
                                        name="email" placeholder="Enter Email Address"
                                        value="{{ old('email', $tenant->user->email) }}" required>
                                </div>

                                <!-- Phone -->
                                <div class="mb-20">
                                    <label for="phone" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Phone Number <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="phone"
                                        name="phone" placeholder="Enter Phone Number"
                                        value="{{ old('phone', $tenant->user->phone) }}" required>
                                </div>

                                <!-- Status -->
                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status" required>
                                        <option value="Active" {{ old('status', $tenant->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $tenant->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('tenant.index') }}" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Update Tenant
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
