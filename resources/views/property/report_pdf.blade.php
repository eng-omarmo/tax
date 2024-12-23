<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Report</title>
    <style>
        /* General styles */
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
            font-size: 10px; /* Smaller font size for compactness */
        }

        th {
            background-color: #f2f2f2;
        }

        /* Print media query */
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 10px; /* Smaller font size for the entire page */
            }

            h1 {
                font-size: 14px; /* Smaller title */
                margin-bottom: 5px;
            }

            table {
                margin: 10px 0;
                width: 100%;
                page-break-inside: auto;
            }

            th, td {
                font-size: 10px; /* Compact font size */
                padding: 4px 6px; /* Reduced padding */
            }

            /* Set page size to A4 */
            @page {
                size: A4;
                margin: 10mm;
            }

            /* Prevent page breaks inside table rows */
            tr {
                page-break-inside: avoid;
            }

            td, th {
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            /* Ensure the table fits on a single page */
            body {
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Property Report</h1>
    <table>
        <thead>
            <tr>
                <th>S.L</th>
                <th>Property Name</th>
                <th>House Nbr</th>
                <th>Property Phone</th>
                <th>Branch</th>
                <th>Zone</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $property->property_name }}</td>
                    <td>{{ $property->nbr }}</td>
                    <td>{{ $property->tenant_phone }}</td>
                    <td>{{ $property->branch }}</td>
                    <td>{{ $property->zone }}</td>
                    <td>{{ $property->latitude }}</td>
                    <td>{{ $property->longitude }}</td>
                    <td>{{ ucfirst($property->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = () => window.print();
    </script>
</body>
</html>
