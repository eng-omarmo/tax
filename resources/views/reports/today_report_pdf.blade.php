<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Activity Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .report-date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-box {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 18%;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
        }

        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <h1>Today's Activity Report</h1>
    <div class="report-date">Report generated on {{ now()->format('M d, Y \a\t h:i A') }}</div>


    <!-- Properties Section -->
    <h2>New Properties Registered Today</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Property Name</th>
                <th>House Code</th>
                <th>Phone</th>
                <th>Branch</th>
                <th>Zone</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($properties as $index => $property)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $property->property_name }}</td>
                    <td>{{ $property->house_code ?? '-' }}</td>
                    <td>{{ $property->property_phone }}</td>
                    <td>{{ $property->branch->name ?? '-' }}</td>
                    <td>{{ $property->zone ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No properties registered today</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Unpaid Units Section -->
    <h2>Generated Invoices Today</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Unit Name</th>
                <th>Property</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($unpaidUnits as $index => $inv)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->unit->property->property_name ?? '-' }}</td>
                    <td>Unpaid</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No Invoice generated today</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paid Units Section -->
    <h2>Paid Invoices Today</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Unit Name</th>
                <th>Property</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paidUnits as $index => $inv)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->unit->property->property_name ?? '-' }}</td>
                    <td>Paid</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No paid units recorded today</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Landlords Section -->
    <h2>New Landlords Registered Today</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($landlords as $index => $landlord)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $landlord->name }}</td>
                    <td>{{ $landlord->phone }}</td>
                    <td>{{ $landlord->email }}</td>
                    <td>{{ $landlord->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No new landlords registered today</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Payments Section -->
    <h2>New Payments Today</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Amount</th>
                <th>Reference</th>
                <th>Completed Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->invoice_number }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->reference }}</td>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No new Payment registered today</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
