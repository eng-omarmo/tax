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

                            <form action="{{ route('account.store') }}" method="POST">
                                @csrf

                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Method Name <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 system-select" id="payment_method" name="payment_method"
                                        required>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method->id }}">
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-20">
                                    <label for="opening_balance"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Opening Balance <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="number" class="form-control radius-8" id="opening_balance"
                                        name="opening_balance" placeholder="Enter Opening Balance"
                                        value="{{ old('opening_balance') }}">
                                </div>



                                <div class="mb-20">
                                    <label for="account_number"
                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Account Number <span class="text-danger-600">*</span>
                                    </label>
                                    <input type="text" class="form-control radius-8" id="account_number"
                                        name="account_number" placeholder="Enter Account Number"
                                        value="{{ old('account_number') }}">

                                </div>

                                <div class="mb-20">
                                    <label for="status" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Status <span class="text-danger-600">*</span>
                                    </label>
                                    <select class="form-control radius-8 form-select" id="status" name="status">
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
