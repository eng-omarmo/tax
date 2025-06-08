@extends('layout.layout')
@php
    $title = 'Income Report By District';
    $subTitle = 'Financial Summary for ' . $currentQuarter . ' ' . $currentYear;
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>District</th>
                                <th>Total Revenue</th>
                                <th>Total Paid</th>
                                <th>Total Outstanding</th>
                                <th>Collection Rate (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomeByDistrict as $district => $income)
                                <tr>
                                    <td>{{ $income['district_name'] }}</td>
                                    <td>{{ number_format($income['totalRevenue'], 2) }}</td>
                                    <td>{{ number_format($income['totalPaid'], 2) }}</td>
                                    <td>{{ number_format($income['totalOutstanding'], 2) }}</td>
                                    <td>{{ round($income['collectionRate'], 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </table>
            </div>
        </div>
    </div>
@endsection
