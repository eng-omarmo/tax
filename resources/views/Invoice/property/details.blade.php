@extends('layout.layout')
@php
    $title = 'Property  Invoice Details';
    $subTitle = 'Property Invoice ';
@endphp
@section('content')
    <div class="container-fluid">
        <!-- Property Overview Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Property Overview</h5>
                <a href="{{ route('invoiceList') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Back to List
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table bordered-table sm-table mb-0">
                            <tr>
                                <th width="35%">Property Name/Code:</th>
                                <td>{{ $property->property_name }} / {{ $property->house_code }}</td>
                            </tr>
                            <tr>
                                <th>Address/Location:</th>
                                <td>{{ $property->district->name }}</td>
                            </tr>
                            <tr>
                                <th>Owner Info:</th>
                                <td>
                                    {{ $property->landlord->name }}<br>
                                    {{ $property->landlord->phone_number }}<br>
                                    {{ $property->landlord->email }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table bordered-table sm-table mb-0">
                            <tr>
                                <th width="35%">Property Type:</th>
                                <td>{{ $property->house_type }}</td>
                            </tr>
                            <tr>
                                <th>Number of Units:</th>
                                <td>{{ $property->units->count() }}</td>
                            </tr>
                            <tr>
                                <th>Property Status:</th>
                                <td>
                                    <span class="badge bg-{{ $property->status === 'active' ? 'success' : 'danger' }}">
                                        {{ strtoupper($property->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ \Carbon\Carbon::parse($property->updated_at)->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Unit Details</h5>
            </div>
            <div class="card-body p-24"> <!-- Add this to match Unit Details card -->

                <div class="row">

                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table sm-table mb-0" aria-describedby="unitDetailsTable">
                            <thead>
                                <tr>
                                    <th scope="col">Unit Number</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Is Owner</th>

                                    <th scope="col">Tax Rate</th>

                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($property->units as $unit)
                                    <tr>
                                        <td>{{ $unit->unit_number }}</td>
                                        <td>{{ $unit->unit_type }}</td>
                                        <td>{{ $unit->unit_price }}</td>

                                        <td>{{ $unit->is_owner == 'yes' ? 'Yes' : 'No' }}</td>

                                        <td>{{ 5 }}%</td>

                                        <td>

                                            <span
                                                class="badge bg-success">{{ $unit->is_available == 1 ? 'Occupied' : 'Available' }}</span>

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Summary Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Invoice Summary</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body">
                                <h6>Total Tax Billed</h6>
                                <h3>${{ number_format($totalBilled, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body">
                                <h6>Total Amount Paid</h6>
                                <h3>${{ number_format($totalPaid, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white">
                            <div class="card-body">
                                <h6>Outstanding Balance</h6>
                                <h3>${{ number_format($totalBilled - $totalPaid, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-info text-white">
                            <div class="card-body">
                                <h6>Current Quarter</h6>
                                <h3>{{ $currentQuarter }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Billing Period</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>


                            @foreach ($property->units as $unit)


                                @foreach ($unit->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->frequency }}</td>
                                        <td>{{ $invoice->invoice_date }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                        <td>${{ $invoice->amount }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'Paid' => 'success',
                                                    'Pending' => 'warning',
                                                    'Overdue' => 'danger',
                                                ][$invoice->payment_status];
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $invoice->payment_status }}
                                            </span>
                                        </td>
                                        <td>

                                            <a href="{{route('receipt.tax' , $invoice->id)}}" class="btn btn-sm btn-primary" title="Print Invoice">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
