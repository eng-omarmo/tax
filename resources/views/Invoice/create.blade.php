@extends('layout.layout')

@php
    $title = 'Confirm Payment - ' . $invoice->invoice_number;
    $subTitle = 'Invoice Verification';
@endphp
@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session()->get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@section('content')
    <!-- Main Card Container -->
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-10">
                    <!-- Header Section -->
                    <div class="d-flex align-items-center gap-16 mb-24">
                        <iconify-icon icon="mdi:file-document-check" class="text-primary"
                            style="font-size: 2rem;"></iconify-icon>
                        <div>
                            <h4 class="text-primary mb-4">Payment Confirmation</h4>
                            <p class="text-primary-light mb-0">Verify invoice details before proceeding</p>
                        </div>
                    </div>

                    <!-- Invoice Details Card -->
                    <div class="card bg-primary-50 border-primary-100 radius-8 mb-24">
                        <div class="card-body p-16">
                            <div class="row g-16">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-8 mb-12">
                                        <iconify-icon icon="mdi:identifier" class="text-primary"></iconify-icon>
                                        <div>
                                            <div class="text-sm text-primary-light">Invoice Number</div>
                                            <div class="fw-semibold">{{ $invoice->invoice_number }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-8 mb-12">
                                        <iconify-icon icon="mdi:calendar-clock" class="text-primary"></iconify-icon>
                                        <div>
                                            <div class="text-sm text-primary-light">Due Date</div>
                                            <div class="fw-semibold">{{ $invoice->due_date }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-8 mb-12">
                                        <iconify-icon icon="mdi:office-building" class="text-primary"></iconify-icon>
                                        <div>
                                            <div class="text-sm text-primary-light">Unit Number</div>
                                            <div class="fw-semibold">{{ $invoice->unit->unit_number }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-8">
                                        <iconify-icon icon="mdi:house" class="text-primary"></iconify-icon>
                                        <div>
                                            <div class="text-sm text-primary-light">Unit Type</div>
                                            <div class="fw-semibold">{{ $invoice->unit->unit_type }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="card border-primary-100 radius-8 mb-24">
                        <div class="card-body p-16">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-sm text-primary-light">Total Amount Due</div>
                                <div class="d-flex align-items-center gap-8">
                                    <span class="badge bg-primary-100 text-primary px-16 py-8 radius-4">
                                        {{ number_format($invoice->amount, 2) }}
                                    </span>
                                    <iconify-icon icon="mdi:cash-multiple" class="text-primary"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <form action="{{ route('invoice.transaction') }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="unit_id" value="{{ $invoice->unit->id }}">

                        <div class="card border-primary-100 radius-8 mb-24">
                            <div class="card-body p-16">

                                <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
                                <!-- Payment Method Dropdown -->
                                <div class="mb-24">
                                    <label class="form-label text-primary mb-8">
                                        Select Payment Method
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_method_id"
                                        class="form-select border-primary-100 radius-8 @error('payment_method_id') is-invalid @enderror"
                                        required>
                                        <option value="">Choose payment method...</option>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Reference Number Input -->
                                <div class="mb-16">
                                    <label class="form-label text-primary mb-8">
                                        Payment Reference Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="reference_number"
                                        class="form-control border-primary-100 radius-8 @error('reference_number') is-invalid @enderror"
                                        placeholder="Enter transaction reference number"
                                        value="{{ old('reference_number') }}" required>
                                    @error('reference_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="text-sm text-primary-light mt-8">
                                        <iconify-icon icon="mdi:information" class="me-2"></iconify-icon>
                                        Found in your payment receipt/transaction details
                                    </div>
                                </div>
                                <!-- Reference Number Input -->
                                <div class="mb-16">
                                    <label class="form-label text-primary mb-8">
                                        Sender Account
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="sender_account"
                                        class="form-control border-primary-100 radius-8 @error('sender_account') is-invalid @enderror"
                                        placeholder="Enter transaction Sender Account Number"
                                        value="{{ old('sender_account') }}" required>
                                    @error('sender_account')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="text-sm text-primary-light mt-8">
                                        <iconify-icon icon="mdi:information" class="me-2"></iconify-icon>
                                        The Account Number of the sender
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Check -->
                        <div class="card bg-base border-primary-100 radius-8 mb-24">
                            <div class="card-body p-16">
                                <div class="form-check checked-primary d-flex align-items-center gap-8">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label text-sm text-primary-light">
                                        I confirm that all information is accurate and I authorize this payment
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-16">
                            <a href="{{ url()->previous() }}"
                                class="btn btn-outline-danger-600 text-danger-600 btn-medium px-40 py-12 radius-8">
                                Cancel
                            </a>
                            <button type="button" class="btn btn-primary btn-medium px-40 py-12 radius-8"
                                data-bs-toggle="modal" data-bs-target="#confirmModal">
                                Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-12 border-primary-100">
                <div class="modal-body p-24">
                    <div class="text-center">
                        <iconify-icon icon="mdi:shield-check" class="text-primary"
                            style="font-size: 3rem;"></iconify-icon>
                        <h5 class="text-primary mt-16 mb-8">Final Authorization</h5>
                        <div class="payment-summary bg-primary-50 radius-8 p-16 mb-24">
                            <div class="text-sm text-primary-light">Amount Payable</div>
                            <div class="fw-bold fs-4">USD {{ number_format($invoice->amount, 2) }}</div>
                        </div>
                        <p class="text-primary-light mb-24">
                            For unit: <span class="fw-semibold">{{ $invoice->unit->unit_name }}</span>
                        </p>
                        <div class="d-flex justify-content-center gap-16">
                            <button type="button"
                                class="btn btn-outline-primary-100 text-primary btn-medium px-24 py-12 radius-8"
                                data-bs-dismiss="modal">
                                Review Again
                            </button>
                            <button type="submit" class="btn btn-primary btn-medium px-24 py-12 radius-8"
                                form="paymentForm">
                                Process Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Update modal preview when shown
            document.getElementById('confirmModal').addEventListener('show.bs.modal', function() {
                const methodSelect = document.querySelector('[name="payment_method_id"]');
                const referenceInput = document.querySelector('[name="reference_number"]');

                // Update payment method preview
                const selectedMethod = methodSelect.options[methodSelect.selectedIndex].text;
                document.getElementById('selectedMethod').textContent = selectedMethod.split(' (')[0];

                // Update reference number preview
                document.getElementById('referencePreview').textContent = referenceInput.value || 'N/A';
            });
        </script>
    @endpush
@endsection
