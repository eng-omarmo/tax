@extends('layout.layout')
@php
    $title = 'Invoice List';
    $subTitle = 'Invoice List';
    $script = '<script>
        function printInvoice() {
            var printContents = document.getElementById("invoice").innerHTML;
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
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                <a href="javascript:void(0)"
                    class="btn btn-sm btn-primary-600 radius-8 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="pepicons-pencil:paper-plane" class="text-xl"></iconify-icon>
                    Send Invoice
                </a>
                {{-- <a href="javascript:void(0)" class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:download-linear" class="text-xl"></iconify-icon>
                            Download
                        </a> --}}
                {{-- <a href="javascript:void(0)" class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="uil:edit" class="text-xl"></iconify-icon>
                            Edit
                        </a> --}}
                <button type="button" class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1"
                    onclick="printInvoice()">
                    <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
                    Print
                </button>
            </div>
        </div>
        <div class="card-body py-40">
            <div class="row justify-content-center" id="invoice">
                <div class="col-lg-8">
                    <div class="shadow-4 border radius-8">
                        <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom">
                            <div>
                                <h3 class="text-xl">Invoice #{{ $data['tax_code'] }}</h3>
                                <p class="mb-1 text-sm">Date Issued: 29/08/2020</p>
                                <p class="mb-0 text-sm">Date Due: {{ $data['due_date'] }}</p>
                            </div>
                            <div>
                                <img src="{{ asset('assets/images/logo.png') }}" alt="image" class="mb-8"
                                    height="80" width="80">
                                <p class="mb-1 text-sm">{{ $data['address'] }}</p>
                                <p class="mb-0 text-sm">{{ $data['email'] }}, {{ $data['phone'] }}</p>
                            </div>
                        </div>
                        <div class="py-28 px-20">
                            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                                <div>
                                    <h6 class="text-md">Issus For:</h6>
                                    <table class="text-sm text-secondary-light">
                                        <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td class="ps-8">:{{ $data['owner'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td class="ps-8">:{{ $data['address'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Phone number</td>
                                                <td class="ps-8">:{{ $data['phone'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <table class="text-sm text-secondary-light">
                                        <tbody>
                                            <tr>
                                                <td>Issus Date</td>
                                                <td class="ps-8">:{{ $data['issue_date']->format('Y-m-d') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tax Code</td>
                                                <td class="ps-8">:{{ $data['tax_code'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Property code</td>
                                                <td class="ps-8">:{{ $data['property'] }}</td>
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
                                                <th scope="col" class="text-sm">Tax amount</th>
                                                <th scope="col" class="text-sm">Amount Paid</th>
                                                <th scope="col" class="text-sm">Amount Due</th>
                                                <th scope="col" class="text-end text-sm">due date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $data['tax_code'] }}</td>
                                                <td>{{ $data['amount'] }}</td>
                                                <td>{{ $data['amountPaid'] }}</td>
                                                <td>{{ $data['balance'] }}</td>
                                                <td class="text-end">{{ $data['due_date'] }}</td>

                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between gap-3">
                                    <div>
                                        <p class="text-sm mb-0"><span class="text-primary-light fw-semibold">generate
                                                By:</span> {{ auth()->user()->name }}</p>
                                        <p class="text-sm mb-0">Thanks for your Collabaration</p>
                                    </div>
                                    <div>
                                        <table class="text-sm">
                                            <tbody>
                                                <tr>

                                                <tr>
                                                    <td class="pe-64">Amount Due:</td>
                                                    <td class="pe-16">
                                                        <span
                                                            class="text-primary-light fw-semibold">{{ $data['balance'] }}</span>
                                                    </td>
                                                </tr>
                                                <td class="pe-64">Amount Paid:</td>
                                                <td class="pe-16">
                                                    <span
                                                        class="text-primary-light fw-semibold">{{ $data['amountPaid'] }}</span>
                                                </td>
                                                </tr>


                                                <tr>
                                                    <td class="pe-64 pt-4">
                                                        <span class="text-primary-light fw-semibold">Total Amount:</span>
                                                    </td>
                                                    <td class="pe-16 pt-4">
                                                        <span
                                                            class="text-primary-light fw-semibold">{{ $data['amount'] }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-64">
                                <p class="text-center text-secondary-light text-sm fw-semibold">Thank you for your
                                    Collabation!</p>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-end mt-64">
                                <div class="text-sm border-top d-inline-block px-12">Signature of {{ $data['owner'] }}
                                </div>
                                <div class="text-sm border-top d-inline-block px-12">Signature of
                                    {{ auth()->user()->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
