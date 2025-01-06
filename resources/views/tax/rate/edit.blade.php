@extends('layout.layout')

@php
    $title = 'Tax Rate Edit';
    $subTitle = 'update tax rate';
@endphp

@section('content')
    <!-- Tax Rate Creation Form -->
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

                            <!-- Tax Rate Creation Form -->
                            <form action="{{ route('tax.rate.update',$taxRate->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Name -->
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tax Rate Type <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="name" value="{{ $taxRate->tax_type }}"
                                        name="name" placeholder="Enter Tax Rate Name"
                                        value="{{ old('name') }}" required>
                                </div>

                                <!-- Rate -->
                                <div class="mb-20">
                                    <label for="rate" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tax Rate (%) <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control radius-8" id="rate"
                                        name="rate" placeholder="Enter Tax Rate Percentage" value="{{ $taxRate->rate }}"
                                        value="{{ old('rate') }}" required>
                                </div>

                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Tax Rate Effective Date <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="date" class="form-control radius-8" id="date" value="{{ $taxRate->effective_date }}"
                                        name="date"
                                        value="{{ old('date') }}" required>
                                </div>

                                <!-- Status -->
                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status" required>
                                        <option value="{{$taxRate->status }}"> {{ $taxRate->status }}</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>

                                    </select>
                                </div>

                                <!-- Submit Button -->
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
