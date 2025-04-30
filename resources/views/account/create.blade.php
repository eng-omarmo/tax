@extends('layout.layout')

@php
    $title = 'Account Manangment';
    $subTitle = 'Account';
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

                            <form action="{{ route('payment.method.store') }}" method="POST">
                                @csrf

                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Method Name <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 system-select" id="unit_type" name="unit_type"
                                        required>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method->id }}">
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="1" {{ old('status') === 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') === 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('payment.method.index') }}"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8 text-decoration-none">
                                        Cancel
                                    </a>
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
