@extends('layout.layout')

@php
    $title = 'Taxes List';
    $subTitle = 'Manage Taxes';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form method="GET" action="{{ route('tax.index') }}" id="filterForm">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Filter Section (Search, Status) -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="navbar-search">
                        <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search by Property" value="{{ request()->search }}">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </div>

                    <select name="status" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">Status</option>
                        <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request()->status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Overdue" {{ request()->status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Filter Tax -->
                    <a href="javascript:void(0);" id="filterLink" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                        Filter
                    </a>

                    <!-- Reset Filter -->
                    <a href="javascript:void(0);" id="resetLink" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th scope="col">S.L</th>
                        <th scope="col">Property</th>
                        <th scope="col">Tax Amount</th>
                        <th scope="col">Tax Code</th>
                        <th scope="col">Due Date</th>
                        <th scope="col">Balance</th>

                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taxes as $tax)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td><a href="{{ route('property.show', $tax->property->id) }}">{{ $tax->property->property_name }}</a></td>

                            <td>${{ number_format($tax->tax_amount, 2) }}</td>
                            <td>{{ $tax->tax_code }}</td>
                            <td>{{ $tax->due_date}}</td>
                            <td>${{ $tax->balance}}</td>
                            <td class="text-center">
                                <span class="{{ $tax->status == 'paid' ? 'bg-success-focus text-success-600' : 'bg-warning-focus text-warning-600' }} border px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ ucfirst($tax->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('tax.edit', $tax->id) }}" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>

                                    <a href="{{ route('tax.delete', $tax->id) }}" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
            <span>Showing {{ $taxes->firstItem() }} to {{ $taxes->lastItem() }} of {{ $taxes->total() }} entries</span>
            <div class="pagination-container">
                {{ $taxes->links() }}
            </div>
        </div>
    </div>

    <script>
        document.getElementById('filterLink').addEventListener('click', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('resetLink').addEventListener('click', function() {
            const formElements = document.getElementById('filterForm').elements;
            Array.from(formElements).forEach(element => {
                if (element.type === 'select-one' || element.type === 'text') {
                    element.value = '';
                }
            });
            document.getElementById('filterForm').submit();
        });
    </script>
@endsection
