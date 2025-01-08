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
<style>
    /* Basic styling for demonstration purposes */
    body { font-family: Arial, sans-serif; }
    .receipt { margin: 20px auto; width: 700px; }
    .section-header { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; }
  </style>


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
        <div class="row" id="receipt" style="max-width: 210mm; margin: auto; page-break-after: always;">

            <!-- Customer Receipt -->
            <div class="col-lg-12 mb-5 border-bottom pb-4">
                <div class="shadow-4 border radius-8 p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="mb-2" height="50" width="50">
                        <h3 class="text-xl mb-1">Customer Receipt #{{ $data['reference'] }}</h3>
                        <p class="mb-1 text-sm">Date Issued: {{ $data['payment_date'] }}</p>
                        <p class="mb-1 text-sm">{{ $data['address'] }}</p>
                        <p class="mb-0 text-sm">{{ $data['email'] }}, {{ $data['phone'] }}</p>
                    </div>

                    <div class="text-center">
                        <h6 class="text-md">Payment Details</h6>
                        <table class="text-sm text-secondary-light mx-auto mb-4" style="width: 80%;">
                            <tbody>
                                <tr>
                                    <td class="text-right">Rent Code</td>
                                    <td class="ps-8"> {{ $data['rent_code'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Tenant</td>
                                    <td class="ps-8"> {{ $data['tenant'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Amount Paid</td>
                                    <td class="ps-8"> ${{ number_format($data['amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Payment Method</td>
                                    <td class="ps-8"> {{ $data['payment_method'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Account No.</td>
                                    <td class="ps-8"> {{ $data['account_number']  ?? $data['mobile_number']}}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Reference</td>
                                    <td class="ps-8">{{ $data['reference'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Bank Name</td>
                                    <td class="ps-8"> {{ $data['bank']  }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-center text-secondary-light text-sm fw-semibold">Thank you for your payment!</p>

                        <div class="d-flex justify-content-between mt-4">
                            <div class="text-sm border-top px-12">Customer Signature</div>
                            <div class="text-sm border-top px-12">Authorized Signature</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Receipt -->
            <div class="col-lg-12">
                <div class="shadow-4 border radius-8 p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="mb-2" height="50" width="50">
                        <h3 class="text-xl mb-1">User Receipt #{{ $data['reference'] }}</h3>
                        <p class="mb-1 text-sm">Date Issued: {{ $data['payment_date'] }}</p>
                        <p class="mb-1 text-sm">{{ $data['address'] }}</p>
                        <p class="mb-0 text-sm">{{ $data['email'] }}, {{ $data['phone'] }}</p>
                    </div>

                    <div class="text-center">
                        <h6 class="text-md">Payment Details</h6>
                        <table class="text-sm text-secondary-light mx-auto mb-4" style="width: 80%;">
                            <tbody>
                                <tr>
                                    <td class="text-right">Rent Code</td>
                                    <td class="ps-8"> {{ $data['rent_code'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Tenant Name</td>
                                    <td class="ps-8">{{ $data['tenant'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Amount Paid</td>
                                    <td class="ps-8">${{ number_format($data['amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Payment Method</td>
                                    <td class="ps-8">{{ $data['payment_method'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Account No.</td>
                                    <td class="ps-8"> {{ $data['account_number']  ?? $data['mobile_number']}}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Reference</td>
                                    <td class="ps-8"> {{ $data['reference'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Bank Name</td>
                                    <td class="ps-8"> {{ $data['bank'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-center text-secondary-light text-sm fw-semibold">Thank you for your payment!</p>

                        <div class="d-flex justify-content-between mt-4">
                            <div class="text-sm border-top px-12">Customer Signature</div>
                            <div class="text-sm border-top px-12">Authorized Signature</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
