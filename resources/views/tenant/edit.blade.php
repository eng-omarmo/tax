@extends('layout.layout')
@php
    $title = 'Update User';
    $subTitle = 'Update User';
@endphp

@section('content')

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
                            <form action="{{ route('tenant.update', $tenant->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span
                                            class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name" name="name"
                                        placeholder="Enter Full Name" value="{{ old('name', $tenant->name) }}">
                                </div>
                                <div class="mb-20">
                                    <label for="email"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span
                                            class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="email" name="email"
                                        placeholder="Enter email address" value="{{ old('email', $tenant->email) }}">
                                </div>
                                <div class="mb-20">
                                    <label for="phone"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Phone</label>
                                    <input type="text" class="form-control radius-8" id="phone" name="phone"
                                        placeholder="Enter phone number" value="{{ old('phone', $tenant->phone) }}">
                                </div>
                                <div class="mb-20">
                                    <label for="role"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Role <span
                                            class="text-danger-600">*</span></label>
                                    <select class="form-control radius-8 form-select" id="role" name="role">
                                        <option value="admin" {{ $tenant->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="Tax Officer" {{ $tenant->role == 'Tax Officer' ? 'selected' : '' }}>Tax Officer</option>
                                        <option value="Business Owner" {{ $tenant->role == 'Business Owner' ? 'selected' : '' }}>Business Owner</option>
                                        <option value="landlord" {{ $tenant->role == 'landlord' ? 'selected' : '' }}>Landlord</option>
                                    </select>
                                </div>
                                <div class="mb-20">
                                    <label for="status"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Status <span
                                            class="text-danger-600">*</span></label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('usersList') }}" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">Cancel</a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">Update</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection