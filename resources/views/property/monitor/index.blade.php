@extends('layout.layout')

@php
    $title = 'Monitor Property List';
    $subTitle = 'Monetering Property ';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-body p-24">
            <div class="table-responsive scroll-sm overflow-x-auto">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border input-form-dark" type="checkbox"
                                            name="checkbox" id="selectAll">
                                    </div>
                                    S.L
                                </div>
                            </th>
                            <th scope="col">Property Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Landlord</th>
                            <th scope="col">Branch</th>
                            <th scope="col">House Type</th>
                            <th scope="col">Zone</th>
                            <th scope="col">Monitoring Status</th>
                            <th scope="col">Status</th>
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
                                <td>{{ $property->property_phone }}</td>
                                <td><a href="{{ route('landlord.show', $property->landlord->id) }}">{{ $property->landlord->name }}</a></td>
                                <td>{{ $property->branch }}</td>
                                <td>{{ $property->house_type }}</td>
                                <td>{{ $property->zone }}</td>
                                <td class="text-center">
                                    <span class="{{ $property->monitoring_status == 'Approved' ? 'bg-success-focus text-success-600' : 'bg-danger-focus text-danger-600' }} border px-24 py-4 radius-4 fw-medium text-sm">
                                        {{ ucfirst($property->monitoring_status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="{{ $property->status == 'Active' ? 'bg-success-focus text-success-600' : 'bg-danger-focus text-danger-600' }} border px-24 py-4 radius-4 fw-medium text-sm">
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
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Section -->
    <div class="d-flex align-items-center  bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
        <span>Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of {{ $properties->total() }} entries</span>
        <div class="pagination-container">
            <div class="card-body p-24">
                <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                    @if ($properties->onFirstPage())
                        <li class="page-item disabled">
                            <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">First</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Previous</a>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $properties->url(1) }}">First</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $properties->previousPageUrl() }}">Previous</a>
                        </li>
                    @endif

                    @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                        <li class="page-item {{ $properties->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $properties->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    @if ($properties->hasMorePages())
                        <li class="page-item">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $properties->nextPageUrl() }}">Next</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $properties->url($properties->lastPage()) }}">Last</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px " href="javascript:void(0)">Next</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Last</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>


@endsection
