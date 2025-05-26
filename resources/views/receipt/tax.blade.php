@extends('layout.layout')

@php
    $title = 'Receipt';
    $subTitle = 'Payment Receipt';
    $script = '<script>
                    function printReceipt() {
                        var printContents = document.getElementById("receipt").innerHTML;
                        var originalContents = document.body.innerHTML;

                        document.body.innerHTML = printContents;

                        window.print();

                        document.body.innerHTML = originalContents;
                    }
                </script>';
@endphp

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary-600 fw-semibold">Payment Receipt</h5>
            <button type="button" class="btn btn-primary radius-8 d-flex align-items-center gap-2" onclick="printReceipt()">
                <iconify-icon icon="basil:printer-outline" class="text-lg"></iconify-icon>
                Print Receipt
            </button>
        </div>
        <div class="card-body py-40">
            <div class="row justify-content-center" id="receipt">
                <div class="col-lg-8">
                    <div class="shadow-4 border radius-8 position-relative overflow-hidden">
                        <!-- Status Banner -->


                        <!-- Ministry of Finance Header -->
                        <div class="p-3 text-center bg-primary-50 border-bottom">
                            <h4 class="fw-bold mb-1">MINISTRY OF FINANCE</h4>
                            <p class="mb-0 text-sm">Tax Revenue Department</p>
                        </div>

                        <!-- Header Section -->
                        <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom bg-light-50">
                            <div>
                                <h3 class="text-xl fw-bold mb-2">Receipt #{{ $data['invoice_number'] }}</h3>
                                <div class="d-flex align-items-center gap-2 text-sm">
                                    <iconify-icon icon="mdi:calendar" class="text-primary-600"></iconify-icon>
                                    <span>Date Issued: <strong>{{ $data['payment_date'] }}</strong></span>
                                </div>

                            </div>
                            <div class="text-end">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="mb-8" height="80" width="80">
                                <p class="mb-1 text-sm">{{ $data['address'] }}</p>
                                <p class="mb-0 text-sm">{{ $data['email'] }}, {{ $data['phone'] }}</p>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="py-28 px-20">
                            <!-- Customer & Payment Info -->
                            <div class="row mb-4">
                                <!-- Customer Info -->
                                <div class="col-md-6">
                                    <div class="card bg-light-50 border-0 h-100">
                                        <div class="card-body p-3">
                                            <h6 class="text-md d-flex align-items-center gap-2 mb-3">
                                                <iconify-icon icon="mdi:account" class="text-primary-600"></iconify-icon>
                                                Customer Information
                                            </h6>
                                            <table class="text-sm text-secondary-light w-100">
                                                <tbody>
                                                    <tr>
                                                        <td class="py-1"><strong>Name</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['owner'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1"><strong>Address</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['address'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1"><strong>Phone</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['phone'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Info -->
                                <div class="col-md-6">
                                    <div class="card bg-light-50 border-0 h-100">
                                        <div class="card-body p-3">
                                            <h6 class="text-md d-flex align-items-center gap-2 mb-3">
                                                <iconify-icon icon="mdi:credit-card" class="text-primary-600"></iconify-icon>
                                                Payment Information
                                            </h6>
                                            <table class="text-sm text-secondary-light w-100">
                                                <tbody>
                                                    <tr>
                                                        <td class="py-1"><strong>Reference</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['reference'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1"><strong>Payment Method</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['payment_method'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1"><strong>Account No.</strong></td>
                                                        <td class="ps-8 py-1">: {{ $data['account_number'] ?? $data['mobile_number'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Details -->
                            <div class="card border mb-4 shadow-sm">
                                <div class="card-header bg-primary-50 py-3">
                                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                        <iconify-icon icon="mdi:file-document" class="text-primary-600"></iconify-icon>
                                        Transaction Details
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 text-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th scope="col" class="text-sm">Tax Code</th>
                                                    <th scope="col" class="text-sm">Amount Paid</th>
                                                    <th scope="col" class="text-sm">Payment Date</th>
                                                    <th scope="col" class="text-end text-sm">Reference</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $data['tax_code'] }}</td>
                                                    <td class="fw-semibold text-primary-600">${{ number_format($data['amount'], 2) }}</td>
                                                    <td>{{ $data['payment_date'] }}</td>
                                                    <td class="text-end">{{ $data['reference'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Summary -->
                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <div class="d-flex flex-column h-100 justify-content-end">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <iconify-icon icon="mdi:account" class="text-primary-600"></iconify-icon>
                                            <p class="text-sm mb-0"><span class="fw-semibold">Generated By:</span> {{ auth()->user()->name }}</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <iconify-icon icon="mdi:information" class="text-primary-600"></iconify-icon>
                                            <p class="text-sm mb-0">Thank you for your payment!</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card bg-success-50 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <h6 class="text-md mb-2">Payment Summary</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-sm fw-semibold">Total Paid:</span>
                                                <span class="text-xl fw-bold text-success">${{ number_format($data['amount'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thank You Message -->
                            <div class="text-center py-3 my-3 bg-light-50 radius-8">
                                <p class="text-primary-600 fw-semibold mb-0">Thank you for your collaboration!</p>
                            </div>

                            <!-- Signatures -->
                            <div class="d-flex flex-wrap justify-content-between align-items-end mt-5">
                                <div class="text-sm border-top d-inline-block px-12">
                                    <div class="d-flex flex-column align-items-center mt-2">
                                        <iconify-icon icon="mdi:account-signature" class="text-primary-600 text-xl mb-1"></iconify-icon>
                                        <span>Signature of {{ $data['owner'] }}</span>
                                    </div>
                                </div>
                                <div class="text-sm border-top d-inline-block px-12">
                                    <div class="d-flex flex-column align-items-center mt-2">
                                        <iconify-icon icon="mdi:account-signature" class="text-primary-600 text-xl mb-1"></iconify-icon>
                                        <span>Signature of {{ auth()->user()->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
