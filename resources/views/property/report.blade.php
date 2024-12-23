@extends('layout.layout')

@php
    $title = 'Property Report';
    $subTitle = 'Property Report';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form method="get" action="{{ route('property.report.fech') }}" id="filterForm">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">

                <!-- Search and Filters -->
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div class="d-flex align-items-center gap-3 flex-grow">
                        <label for="start_date" class="form-label fw-bold text-neutral-900">From</label>
                        <input type="date" id="start_date" name="start_date" class="form-control h-40-px w-auto" />

                        <label for="end_date" class="form-label fw-bold text-neutral-900">To</label>
                        <input type="date" id="end_date" name="end_date" class="form-control h-40-px w-auto" />
                        <input type="text" class="form-control h-40-px" name="nbr" placeholder="Enter House NBR" />

                        <select name="status" class="form-select h-40-px w-auto">
                            <option value="">Select District</option>

                            <option value="">All</option>
                            @foreach ($data['districts'] as $district)
                                <option value="{{ $district }}">{{ $district }}</option>
                            @endforeach
                        </select>

                        <select name="branch" class="form-select h-40-px w-auto">
                            <option value="">Select Branch</option>
                            <option value="">All</option>
                            @foreach ($data['branches'] as $branch)
                                <option value="{{ $branch }}">{{ $branch }}</option>
                            @endforeach
                        </select>

                        <select name="zone" class="form-select h-40-px w-auto">
                            <option value="">Select Zone</option>
                            <option value="">All</option>
                            @foreach ($data['zones'] as $zone)
                                <option value="{{ $zone }}">{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" id="filterButton"
                            class="btn btn-primary text-xs px-8 py-8 radius-4 d-flex align-items-center">
                            <iconify-icon icon="ic:baseline-filter-alt" class="icon text-lg line-height-1"></iconify-icon>
                            Load Report
                        </button>

                        <a href="javascript:void(0);" id="print"
                            class="btn btn-secondary text-xs px-8 py-8 radius-4 d-flex align-items-center">
                            <iconify-icon icon="ic:baseline-filter-alt-off"
                                class="icon text-lg line-height-1"></iconify-icon>
                            Print
                        </a>
                    </div>
                </div>

                @if (isset($properties))
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm overflow-x-auto">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <div class="d-flex align-items-center gap-10">
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input radius-4 border input-form-dark"
                                                        type="checkbox" name="checkbox" id="selectAll">
                                                </div>
                                                S.L
                                            </div>
                                        </th>
                                        <th scope="col">Property Name</th>
                                        <th scope="col">House Nbr</th>

                                        <th scope="col">Propery phone </th>
                                        <th scope="col">Branch</th>
                                        <th scope="col">Zone</th>
                                        <th scope="col">Latitude</th>
                                        <th scope="col">Longitude</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($properties as $property)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-10">
                                                    <div class="form-check style-check d-flex align-items-center">
                                                        <input class="form-check-input radius-4 border border-neutral-400"
                                                            type="checkbox" name="checkbox">
                                                    </div>
                                                    {{ $loop->iteration }}
                                                </div>
                                            </td>
                                            <td>{{ $property->property_name }}</td>
                                            <td>{{ $property->property_nbr }}</td>
                                            <td>{{ $property->tenant_phone }}</td>

                                            <td>{{ $property->branch }}</td>
                                            <td>{{ $property->zone }}</td>
                                            <td>{{ $property->latitude }}</td>
                                            <td>{{ $property->longitude }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="{{ $property->status == 'Available' ? 'bg-success-focus text-success-600' : 'bg-danger-focus text-danger-600' }} border px-24 py-4 radius-4 fw-medium text-sm">
                                                    {{ ucfirst($property->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                                    <a href="{{ route('property.edit', $property->id) }}"
                                                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                    </a>

                                                    <a href="{{ route('property.delete', $property->id) }}"
                                                        class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                        <iconify-icon icon="fluent:delete-24-regular"
                                                            class="menu-icon"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                            <span>Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of
                                {{ $properties->total() }} entries</span>
                            <div class="pagination-container">
                                {{ $properties->links() }}
                            </div>
                        </div> --}}
                    </div>
                @endif


            </div>
        </form>

    </div>
@endsection
