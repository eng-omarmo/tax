@extends('layout.layout')

@php
    $title = 'User Details';
    $subTitle = 'User Activity Summary';
@endphp

@section('content')
<div class="container-fluid">
    <!-- User Profile Card -->
    <div class="card mb-4 radius-12">
        <div class="card-body p-24">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{ asset('assets/images/user-list/user-list1.png') }}" alt="{{ $user->name }}"
                         class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px;">
                    <h4 class="fw-bold">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $user->phone }}</p>
                    <p class="mb-1"><strong>District:</strong> {{ $user->district->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Status:</strong>
                        <span class="badge bg-{{ $user->status == 'Active' ? 'success' : 'danger' }}">{{ $user->status }}</span>
                    </p>
                </div>
                <div class="col-md-9">
                    <h4 class="border-bottom pb-2 mb-3">Performance Summary</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary-focus text-primary-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $stats['total_properties'] }}</h3>
                                    <p class="mb-0">Properties Registered</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success-focus text-success-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $stats['total_units'] }}</h3>
                                    <p class="mb-0">Units Managed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info-focus text-info-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $stats['total_landlords'] }}</h3>
                                    <p class="mb-0">Landlords Added</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-warning-focus text-warning-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $stats['total_taxes'] }}</h3>
                                    <p class="mb-0">Taxes Processed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-danger-focus text-danger-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">{{ $stats['total_logins'] }}</h3>
                                    <p class="mb-0">Total Logins</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-secondary-focus text-secondary-600 radius-12">
                                <div class="card-body text-center">
                                    <h3 class="fw-bold">
                                        {{ isset($stats['last_login']) ? \Carbon\Carbon::parse($stats['last_login'])->diffForHumans() : 'Never' }}
                                    </h3>

                                    <h7 class="mb-0">Last Login</h7>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Recent Properties -->
        <div class="col-md-6">
            <div class="card radius-12 h-100">
                <div class="card-header bg-base py-16 px-24">
                    <h5 class="mb-0">Recent Property Registrations</h5>
                </div>
                <div class="card-body p-24">
                    @if($recentProperties->count() > 0)
                        <div class="table-responsive">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Property Name</th>
                                        <th>House Code</th>
                                        <th>District</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProperties as $property)
                                    <tr>
                                        <td>{{ $property->property_name }}</td>
                                        <td>{{ $property->house_code }}</td>
                                        <td>{{ $property->district->name ?? 'N/A' }}</td>
                                        <td>{{ $property->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('property.show', $property->id) }}" class="btn btn-sm btn-info">
                                                <iconify-icon icon="mdi:eye" class="menu-icon"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('property.index') }}?user_id={{ $user->id }}" class="btn btn-primary">View All Properties</a>
                        </div>
                    @else
                        <div class="alert alert-info">No properties registered yet.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Logins -->
        <div class="col-md-6">
            <div class="card radius-12 h-100">
                <div class="card-header bg-base py-16 px-24">
                    <h5 class="mb-0">Recent Login Activities</h5>
                </div>
                <div class="card-body p-24">
                    @if($recentLogins->count() > 0)
                        <div class="table-responsive">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLogins as $login)
                                    <tr>
                                        <td>{{ $login->logged_in_at }}</td>
                                        <td>{{ $login->ip_address }}</td>
                                        <td>{{ $login->device }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('login.activities.index') }}?user_id={{ $user->id }}" class="btn btn-primary">View All Login Activities</a>
                        </div>
                    @else
                        <div class="alert alert-info">No login activities recorded yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Landlords and Units -->
    <div class="row mt-4">
        <!-- Landlords Added -->
        <div class="col-md-12">
            <div class="card radius-12">
                <div class="card-header bg-base py-16 px-24 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Landlords Added by {{$user->name}}</h5>
                </div>
                <div class="card-body p-24">
                    @if($user->landlords->count() > 0)
                        <div class="table-responsive">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Properties</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->landlords as $landlord)
                                    <tr>
                                        <td>{{ $landlord->name }}</td>
                                        <td>{{ $landlord->email }}</td>
                                        <td>{{ $landlord->phone_number }}</td>
                                        <td>{{ $landlord->properties->count() }}</td>
                                        <td>
                                            <a href="{{ route('landlord.show', $landlord->id) }}" class="btn btn-sm btn-info">
                                                <iconify-icon icon="mdi:eye" class="menu-icon"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No landlords added yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
