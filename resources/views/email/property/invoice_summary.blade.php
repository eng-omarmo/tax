<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Invoice Summary</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .title {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .subtitle {
            color: #7f8c8d;
            margin: 5px 0 0;
            font-size: 16px;
        }
        .property-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .property-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #7f8c8d;
        }
        .info-value {
            flex: 1;
        }
        .units-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .units-table th {
            background-color: #3498db;
            color: white;
            text-align: left;
            padding: 10px;
        }
        .units-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .units-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .invoice-summary {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }
        .payment-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f4fd;
            border-radius: 6px;
        }
        .status-paid {
            color: #27ae60;
            font-weight: bold;
        }
        .status-unpaid {
            color: #e74c3c;
            font-weight: bold;
        }
        .status-partial {
            color: #f39c12;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo">
            <h1 class="title">Property Invoice Summary</h1>
            <p class="subtitle">Invoice #{{ $invoice->invoice_number }}</p>
        </div>

        <!-- Access property through invoice->unit->property relationship -->
        @php
            $property = $invoice->unit->property;
            $totalTax = 0;
        @endphp

        <div class="property-info">
            <div class="property-name">{{ $property->property_name }}</div>
            <div class="info-row">
                <div class="info-label">Property Code:</div>
                <div class="info-value">{{ $property->house_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Zone:</div>
                <div class="info-value">{{ $property->zone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">District:</div>
                <div class="info-value">{{ $property->district->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact:</div>
                <div class="info-value">{{ $property->property_phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Landlord:</div>
                <div class="info-value">{{ $property->landlord->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Landlord Contact:</div>
                <div class="info-value">{{ $property->landlord->phone_number }}</div>
            </div>
        </div>

        <h2>Unit Invoices</h2>
        <table class="units-table">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Type</th>
                    <th>Rent</th>
                    <th>Tax Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- If we're showing a single invoice -->
                @if(isset($invoice) && !isset($invoices))
                    @php
                        $taxAmount = $invoice->unit->unit_price * 0.05 * 3; // 5% of price for 3 months
                        $totalTax += $taxAmount;
                    @endphp
                    <tr>
                        <td>{{ $invoice->unit->unit_number }} - {{ $invoice->unit->unit_name }}</td>
                        <td>{{ $invoice->unit->unit_type }}</td>
                        <td>${{ number_format($invoice->unit->unit_price, 2) }}</td>
                        <td>${{ number_format($taxAmount, 2) }}</td>
                        <td>
                            @if($invoice->status == 'Paid')
                                <span class="status-paid">Paid</span>
                            @else
                                <span class="status-unpaid">Pending</span>
                            @endif
                        </td>
                    </tr>
                <!-- If we're showing multiple invoices for a property -->
                @elseif(isset($invoices))
                    @foreach($invoices as $inv)
                        @php
                            $taxAmount = $inv->unit->unit_price * 0.05 * 3; // 5% of price for 3 months
                            $totalTax += $taxAmount;
                        @endphp
                        <tr>
                            <td>{{ $inv->unit->unit_number }} - {{ $inv->unit->unit_name }}</td>
                            <td>{{ $inv->unit->unit_type }}</td>
                            <td>${{ number_format($inv->unit->unit_price, 2) }}</td>
                            <td>${{ number_format($taxAmount, 2) }}</td>
                            <td>
                                @if($inv->status == 'Paid')
                                    <span class="status-paid">Paid</span>
                                @else
                                    <span class="status-unpaid">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="info-row">
                <div class="info-label">Total Units:</div>
                <div class="info-value">{{ isset($invoices) ? count($invoices) : 1 }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Invoice Period:</div>
                <div class="info-value">
                    @if(isset($invoices))
                        {{ date('M d, Y', strtotime($invoices->first()->start_date)) }} - {{ date('M d, Y', strtotime($invoices->first()->end_date)) }}
                    @else
                        {{ date('M d, Y', strtotime($invoice->start_date)) }} - {{ date('M d, Y', strtotime($invoice->end_date)) }}
                    @endif
                </div>
            </div>
            <div class="info-row total-row">
                <div class="info-label">Total Tax Due:</div>
                <div class="info-value">${{ number_format($totalTax, 2) }}</div>
            </div>
        </div>

        <div class="payment-info">
            <h3>Payment Information</h3>
            <div class="info-row">
                <div class="info-label">Due Date:</div>
                <div class="info-value">{{ date('M d, Y', strtotime($invoice->due_date)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Methods:</div>
                <div class="info-value">Somxchange </div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value">dial *213# choose pay bill then enter house code </div>
            </div>
            <div class="info-row">
                <div class="info-label">Reference:</div>
                <div class="info-value">{{ $invoice->invoice_number }}</div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('invoice.pay', $invoice->id) }}" class="button">Pay Now</a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automatically generated email. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team at support@taxation.com</p>
            <p>&copy; {{ date('Y') }} Taxation System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
