@extends('layout.layout')

@php
    $title = 'Monitor Property List';
    $subTitle = 'Monitoring Property';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp

@section('content')
<div class="card h-100 p-0 radius-12 mt-5" >
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm overflow-x-auto">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th><div class="d-flex align-items-center gap-10">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input radius-4 border input-form-dark" type="checkbox" id="selectAll">
                            </div>S.L
                        </div></th>
                        <th>Property Name</th>
                        <th>Phone</th>
                        <th>Landlord</th>
                        <th>Branch</th>
                        <th>House Type</th>
                        <th>Zone</th>
                        <th>Monitoring Status</th>

                        <th>Status</th>
                        <th>Created By </th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($properties as $property)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border" type="checkbox">
                                    </div>
                                    {{ $loop->iteration }}
                                </div>
                            </td>
                            <td>{{ $property->property_name }}</td>
                            <td>{{ $property->property_phone }}</td>
                            <td><a href="{{ route('landlord.show', $property->landlord->id) }}">{{ $property->landlord->name }}</a></td>
                            <td>{{ $property->branch->name }}</td>
                            <td>{{ $property->house_type }}</td>
                            <td>{{ $property->zone }}</td>
                            <td class="text-center">
                                <span class="border px-24 py-4 radius-4 fw-medium text-sm
                                    {{ $property->monitoring_status == 'Approved' ? 'bg-success-focus text-success-600' : 'bg-danger-focus text-danger-600' }}">
                                    {{ ucfirst($property->monitoring_status) }}
                                </span>
                            </td>
                            <td>{{ $property->created_by }}</td>
                            <td class="text-center">
                                <span class="border px-24 py-4 radius-4 fw-medium text-sm
                                    {{ $property->status == 'Active' ? 'bg-success-focus text-success-600' : 'bg-danger-focus text-danger-600' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-10">
                                    <a href="{{ route('monitor.show', $property->id) }}" class="bg-success-focus text-success-600 w-40-px h-40-px rounded-circle d-flex justify-content-center align-items-center">
                                        <iconify-icon icon="lucide:view" class="menu-icon"></iconify-icon>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex align-items-center bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
        <span>Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of {{ $properties->total() }} entries</span>
        <div class="pagination-container">
            <ul class="pagination d-flex flex-wrap gap-2 justify-content-center align-items-center">
                {{-- First and Previous --}}
                @if ($properties->onFirstPage())
                    <li class="page-item disabled"><a class="page-link">First</a></li>
                    <li class="page-item disabled"><a class="page-link">Previous</a></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $properties->url(1) }}">First</a></li>
                    <li class="page-item"><a class="page-link" href="{{ $properties->previousPageUrl() }}">Previous</a></li>
                @endif

                {{-- Pages --}}
                @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                    <li class="page-item {{ $properties->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link {{ $properties->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                {{-- Next and Last --}}
                @if ($properties->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $properties->nextPageUrl() }}">Next</a></li>
                    <li class="page-item"><a class="page-link" href="{{ $properties->url($properties->lastPage()) }}">Last</a></li>
                @else
                    <li class="page-item disabled"><a class="page-link">Next</a></li>
                    <li class="page-item disabled"><a class="page-link">Last</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
