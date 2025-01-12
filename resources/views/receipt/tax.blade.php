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
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-danger radius-8" onclick="printReceipt()">
                    <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
                    Print
                </button>
            </div>
        </div>
        <div class="card-body py-40">
            <div class="row justify-content-center" id="receipt">
                <div class="col-lg-8">
                    <div class="shadow-4 border radius-8">
                        <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom">
                            <div>
                                <h3 class="text-xl">Receipt #{{ $data['reference'] }}</h3>
                                <p class="mb-1 text-sm">Date Issued: {{ $data['payment_date'] }}</p>
                            </div>
                            <div>
                                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="mb-8" height="80" width="80">
                                <p class="mb-1 text-sm">{{ $data['address'] }}</p>
                                <p class="mb-0 text-sm">{{ $data['email'] }}, {{ $data['phone'] }}</p>
                            </div>
                        </div>
                        <div class="py-28 px-20">
                            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                                <div>
                                    <h6 class="text-md">Issued To:</h6>
                                    <table class="text-sm text-secondary-light">
                                        <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td class="ps-8">: {{ $data['owner'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td class="ps-8">: {{ $data['address'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Phone</td>
                                                <td class="ps-8">: {{ $data['phone'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <table class="text-sm text-secondary-light">
                                        <tbody>
                                            <tr>
                                                <td>Reference</td>
                                                <td class="ps-8">: {{ $data['reference'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Payment Method</td>
                                                <td class="ps-8">: {{ $data['payment_method'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Account No.</td>
                                                <td class="ps-8">: {{ $data['account_number'] ?? $data['mobile_number'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-24">
                                <div class="table-responsive scroll-sm">
                                    <table class="table bordered-table text-sm">
                                        <thead>
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
                                                <td>{{ number_format($data['amount'], 2) }}</td>
                                                <td>{{ $data['payment_date'] }}</td>
                                                <td class="text-end">{{ $data['reference'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between gap-3">
                                    <div>
                                        <p class="text-sm mb-0"><span class="text-primary-light fw-semibold">Generated By:</span> {{ auth()->user()->name }}</p>
                                        <p class="text-sm mb-0">Thank you for your payment!</p>
                                    </div>
                                    <div>
                                        <table class="text-sm">
                                            <tbody>
                                                <tr>
                                                    <td class="pe-64">Total Paid:</td>
                                                    <td class="pe-16">
                                                        <span class="text-primary-light fw-semibold">{{ number_format($data['amount'], 2) }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-64 text-center">
                                <p class="text-secondary-light text-sm fw-semibold">Thank you for your collaboration!</p>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-end mt-64">
                                <div class="text-sm border-top d-inline-block px-12">Signature of {{ $data['owner'] }}</div>
                                <div class="text-sm border-top d-inline-block px-12">Signature of {{ auth()->user()->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
