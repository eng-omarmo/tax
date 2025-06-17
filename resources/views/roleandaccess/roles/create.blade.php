@extends('layout.layout')
@php
    $title='Create Role';
    $subTitle = 'Add New Role';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h5 class="mb-0">Create New Role</h5>
        </div>

        <div class="card-body p-24">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Role Name</label>
                        <input type="text" name="name" class="form-control radius-8 @error('name') is-invalid @enderror" placeholder="Enter Role Name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                        <div class="d-flex align-items-center flex-wrap gap-28">
                            <div class="form-check checked-success d-flex align-items-center gap-2">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" value="Active" checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check checked-success d-flex align-items-center gap-2">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" value="Inactive">
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-20">
                        <label for="desc" class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                        <textarea class="form-control" id="desc" name="description" rows="4" cols="50" placeholder="Write some text">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12 mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Permissions</label>
                        <div class="row">
                            @foreach($permissions->groupBy(function($permission) {
                                return explode(' ', $permission->name)[0];
                            }) as $group => $items)
                                <div class="col-md-4 mb-3">
                                    <div class="card p-3">
                                        <h6 class="mb-2 text-capitalize">{{ $group }}</h6>
                                        @foreach($items as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission{{ $permission->id }}">
                                                <label class="form-check-label" for="permission{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
