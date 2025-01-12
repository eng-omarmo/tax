@extends('layout.layout')

@php
    $title = 'invoice Generation';
    $subTitle = 'Manage invoice';
@endphp

@section('content')

    @if (empty($tax) || empty($tax->id))
        <!-- Search Form -->
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('invoice.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="tax_code"
                                    name="tax_code" placeholder="Enter Property Tax Code" value="{{ old('tax_code') }}"
                                    required>
                            </div>
                            <button type="submit"
                                class="btn btn-primary text-sm btn-medium px-4 py-2 d-flex align-items-center ms-2">
                                <iconify-icon icon="ic:baseline-search" class="icon text-xl line-height-1"></iconify-icon>
                                <span class="ms-1">Search</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Rent Registration Form -->
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

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('generate.invoice.tax') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="property_id" value="{{ $tax->property->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label for="property_name"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Name
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_name"
                                                name="property_name" value="{{ $tax->property->property_name }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Property Phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $tax->property->property_phone }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                House type
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $tax->property->house_type }}" readonly>

                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                House Code
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $tax->property->house_code }}" readonly>

                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord Name
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $tax->property->landlord->user->name }}"
                                                readonly>
                                        </div>
                                        <div class="col-md-6 mb-20">
                                            <label for="property_phone"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Lanlord Phone
                                            </label>
                                            <input type="text" class="form-control radius-8" id="property_phone"
                                                name="property_phone" value="{{ $tax->property->landlord->user->phone }}"
                                                readonly>
                                        </div>

                                        <div class="mb-20">
                                            <label for="unit_price"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                Tax Total Amount <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text" class="form-control radius-8" id="unit_price"
                                                placeholder="Enter Unit Price" name="unit_price"
                                                value="{{ $tax->tax_amount }}" required>
                                        </div>
                                        <div class="mb-20">
                                            <label for="unit_price"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                tax Balance <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text" class="form-control radius-8" id="unit_price"
                                                placeholder="Enter Unit Price" name="unit_price"
                                                value="{{ $balance }}" required>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center gap-3">
                                            <button type="button"
                                                class="border border-danger-600 bg-hover-danger-200 text-danger-600  text-md px-56 py-11 radius-8">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                                Generete Invoice
                                            </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
