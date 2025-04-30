@extends('layout.layout')

@php
    $title = 'Edit Payment Method';
    $subTitle = 'Edit Payment Method';
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

                            <form action="{{ route('payment.method.update', $paymentMethod->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-20">
                                    <label for="name"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Method Name <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="name" name="name"
                                        placeholder="e.g. Hormuud, Somtel, EVC, Zaad"
                                        value="{{ old('name', $paymentMethod->name) }}">
                                </div>

                                <div class="mb-20">
                                    <label for="status"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="1" {{ (old('status', $paymentMethod->is_active) == 1) ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ (old('status', $paymentMethod->is_active) == 0) ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('payment.method.index') }}"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8 text-decoration-none">
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
@endsection
