@extends('layout.layout')

@php
    $title = 'Payment List';
    $subTitle = 'Manage Payment';
    $script = '<script>
        // ================== Image Upload Js Start ===========================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                    $("#imagePreview").hide();
                    $("#imagePreview").fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        // ================== Image Upload Js End ===========================
    </script>';
@endphp

@section('content')

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
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
    @endif

    @if (!isset($tax) || empty($tax->id))
        <!-- Search Form -->
        <div class="card h-100 p-0 radius-12 mb-4">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <form action="{{ route('tax.payment.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1 align-items-center">
                                <input type="text" class="form-control radius-8 me-2 flex-grow-1" id="tax_code"
                                    name="tax_code" placeholder="Enter Tax Code" value="{{ old('tax_code') }}" required>
                            </div>
                            <!-- Add New Tenant Button -->
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
        <!-- Tenant Form -->
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
                                <form action="{{ route('tax.payment.store') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="tax_id" value="{{ isset($tax->id) ? $tax->id : '' }}">
                                    <div class="mb-20">
                                        <label for="property_name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Name <span class="text-danger-600">*</span>
                                        </label>
                                        <div
                                            class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                            {{ $tax->property->property_name }}
                                        </div>

                                        <label for="property_phone"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Property Phone <span class="text-danger-600">*</span>
                                        </label>
                                        <div
                                            class="bg-gray-100 border border-gray-300 p-4 rounded-md shadow-sm text-sm text-gray-800">
                                            {{ $tax->property->property_phone }}
                                        </div>
                                    </div>

                                    <div class="mb-20">
                                        <label for="payment_amount"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Tax Amount <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control radius-8"
                                            id="payment_amount" name="payment_amount" value ="{{ $tax->tax_amount }}"
                                            readonly required>
                                    </div>

                                    <div class="mb-20">
                                        <label for="payment_amount"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Current Balance <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control radius-8" id="balance"
                                            name="balance" value ="{{ $balance }}" readonly required>
                                    </div>

                                    <div class="mb-20">
                                        <label for="payment_amount"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Payment Amount <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control radius-8" id="amount"
                                            name="amount" value="{{ old('amount') }}" placeholder="Enter Payment Amount"
                                            required>
                                    </div>

                                    <div class="mb-20">
                                        <label for="payment_method"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Payment Method <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control radius-8 form-select" id="payment_method"
                                            name="payment_method" required>
                                            @if (auth()->user()->role == 'Admin')
                                                <option value="Cash"
                                                    {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>
                                                    Cash</option>
                                            @endif
                                            <option value="Bank Transfer"
                                                {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank
                                                Transfer</option>
                                            <option value="Mobile Payment"
                                                {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>Mobile
                                                Payment</option>
                                            <!-- Add more payment methods if necessary -->
                                        </select>
                                    </div>


                                    <div class="mb-20" id="payment-details-container">
                                        <!-- Additional input fields will be inserted here -->
                                    </div>


                                    <div class="mb-20">
                                        <label for="payment_date"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Payment Date <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="date" class="form-control radius-8" id="payment_date"
                                            name="payment_date" required>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                            Cancel
                                        </button>
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
    @endif

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionSelect = document.getElementById('description');
        const paymentAmountInput = document.getElementById('payment_amount');

        descriptionSelect.addEventListener('change', function() {
            const selectedType = this.value;

            if (selectedType) {
                fetchPaymentAmount(selectedType);
                console.log(selectedType); // Logs the selected payment type
            } else {
                paymentAmountInput.value = '';
            }
        });

        function fetchPaymentAmount(paymentType) {
            const tenantId = '{{ $tenant->id ?? '' }}'; // Ensure the tenant ID is passed correctly
            const url = `/payment/get-payment-amount/${tenantId}/${paymentType}`;

            fetch(url) // Removed the extra closing parenthesis
                .then(response => response.json())
                .then(data => {
                    if (data && data.payment_amount) {
                        paymentAmountInput.value = data.payment_amount;
                    }
                })
                .catch(error => {
                    console.error('Error fetching payment amount:', error);
                });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentDetailsContainer = document.getElementById('payment-details-container');

        paymentMethodSelect.addEventListener('change', function() {
            const selectedMethod = this.value;
            paymentDetailsContainer.innerHTML = '';
            if (selectedMethod === 'Bank Transfer') {
                paymentDetailsContainer.innerHTML = `
                    <label for="bank_name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Bank Name <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" class="form-control radius-8" id="bank_name" name="bank_name"
                        placeholder="Enter Bank Name" required>

                    <label for="account_number" class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Account Number <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" class="form-control radius-8" id="account_number" name="account_number"
                        placeholder="Enter Account Number" required>
                `;
            } else if (selectedMethod === 'Mobile Money') {
                paymentDetailsContainer.innerHTML = `
                    <label for="mobile_number" class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Mobile Number <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" class="form-control radius-8" id="mobile_number" name="mobile_number"
                        placeholder="Enter Mobile Number" required>
                `;
            }
        });
    });
</script>
