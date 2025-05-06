<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Report</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
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
    <h1>Property Detailed Report</h1>
    <table>
        <thead>
            <tr>
                <th>S.L</th>
                <th>Property Name</th>
                <th>House Code</th>
                <th>Type</th>
                <th>District</th>
                <th>Branch</th>
                <th>Zone</th>
                <th>Owner</th>
                <th>Status</th>
                <th>Units</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $property->property_name }}</td>
                    <td>{{ $property->house_code }}</td>
                    <td>{{ $property->house_type }}</td>
                    <td>{{ $property->district->name ?? '-' }}</td>
                    <td>{{ $property->branch->name ?? '-' }}</td>
                    <td>{{ $property->zone }}</td>
                    <td>{{ $property->landlord->user->name ?? '-' }}</td>
                    <td>
                        <span style="color: {{ $property->status === 'active' ? 'green' : 'red' }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </td>
                    <td>{{ $property->units_count ?? 0 }}</td>
                    <td>{{ $property->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = () => window.print();
    </script>
</body>
</html>
