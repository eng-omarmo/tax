@extends('layout.layout')

@php
    $title = 'Confirm Payment - ' . $invoice->invoice_number;
    $subTitle = 'Invoice Verification';
@endphp

@section('content')
    <!-- Main Card Container -->
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-10">
                    <!-- Header Section -->
                    <div class="d-flex align-items-center gap-16 mb-24">
                        <iconify-icon icon="mdi:file-document-check" class="text-primary" style="font-size: 2rem;"></iconify-icon>
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
                                            <div class="fw-semibold">{{ $invoice->due_date}}</div>
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
                                        RM {{ number_format($invoice->amount, 2) }}
                                    </span>
                                    <iconify-icon icon="mdi:cash-multiple" class="text-primary"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Display -->
                    @if ($errors->any())
                        <div class="alert bg-danger-50 border-danger-200 radius-8 mb-24">
                            <div class="d-flex align-items-center gap-8">
                                <iconify-icon icon="mdi:alert-circle" class="text-danger"></iconify-icon>
                                <div class="text-sm text-danger">
                                    <div class="fw-semibold">Verification Required:</div>
                                    <ul class="mb-0 ps-16">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Confirmation Form -->
                    <form action="{{ route('generate.invoice.tax') }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $invoice->unit->id }}">

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
                            <button type="button"
                                    class="btn btn-primary btn-medium px-40 py-12 radius-8"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmModal">
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
                        <iconify-icon icon="mdi:shield-check" class="text-primary" style="font-size: 3rem;"></iconify-icon>
                        <h5 class="text-primary mt-16 mb-8">Final Authorization</h5>
                        <p class="text-primary-light mb-24">
                            You are about to pay RM {{ number_format($invoice->amount, 2) }} for<br>
                            <span class="fw-semibold">{{ $invoice->unit->unit_name }}</span>
                        </p>
                        <div class="d-flex justify-content-center gap-16">
                            <button type="button"
                                    class="btn btn-outline-primary-100 text-primary btn-medium px-24 py-12 radius-8"
                                    data-bs-dismiss="modal">
                                Review Again
                            </button>
                            <button type="submit"
                                    class="btn btn-primary btn-medium px-24 py-12 radius-8"
                                    form="paymentForm">
                                Process Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
