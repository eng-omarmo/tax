@extends('layout.layout')

@php
    $title = 'Payments Grid';
    $subTitle = 'Payments Grid';
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
        <form method="GET" action="{{ route('payment.index') }}" id="filterForm">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Filter Section (Search, Pagination, Status) -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="navbar-search">
                        <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search"
                            value="{{ request()->search }}">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </div>

                    <select name="status" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">Status</option>
                        <option value="Paid" {{ request()->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Pending" {{ request()->status == 'Pending' ? 'selected' : '' }}>Pending</option>

                    </select>
                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Filter Payment (link to submit filter form) -->
                    <a href="javascript:void(0);" id="filterLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                        Filter
                    </a>

                    <!-- Reset Filter (link to reset filter form) -->
                    <a href="javascript:void(0);" id="resetLink"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                        Reset
                    </a>

                    <!-- Add New Payment Button -->
                    <a href="{{ route('payment.create.tax') }}"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Payment
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
                        <th scope="col">
                            <div class="d-flex align-items-center gap-10">
                                <div class="form-check style-check d-flex align-items-center">
                                    <input class="form-check-input radius-4 border input-form-dark" type="checkbox"
                                        name="checkbox" id="selectAll">
                                </div>
                                S.L
                            </div>
                        </th>
                        <th scope="col">SNO</th>

                        <th scope="col">Property</th>
                        <th scope="col">Tenant</th>
                        <th scope="col">Account Name</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Account No</th>
                        <th scope="col">Amount Paid</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input radius-4 border border-neutral-400" type="checkbox"
                                            name="checkbox">
                                    </div>
                                    {{ $loop->iteration }}
                                </div>
                            </td>
                            <td>{{ $payment->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="text-md mb-0 fw-normal text-secondary-light">{{ $payment->invoice->invoice_number ?? 'N/A' }}</span>
                                </div>
                            </td>



                            <td><a href="{{ route('property.edit', $payment->invoice->unit->property->id) }}"
                                    class="text-md mb-0 fw-normal text-secondary-light">
                                    {{  $payment->invoice->unit->property->property_name ?? 'N/A' }}</a></td>

                            <td><span
                                    class="text-md mb-0 fw-normal text-secondary-light">{{ $payment->paymentDetails['bank'] }}</span>
                            </td>
                            <td><span
                                class="text-md mb-0 fw-normal text-secondary-light">{{ $payment->amount }}</span>
                        </td>
                            <td><span
                                    class="text-md mb-0 fw-normal text-secondary-light">{{ $payment->paymentDetails['mobile'] ?? $payment->paymentDetails['account'] }}</span>
                            </td>




                            <td><span
                                    class="text-md mb-0 fw-normal text-secondary-light">{{ $payment->payment_date }}</span>
                            </td>

                            <td class="text-center">
                                <span
                                    class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">
                                    {{ ucfirst($payment->status) ?? $payment->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('payment.edit', $payment->id) }}"
                                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </a>

                                    <a href="{{ route('payment.delete', $payment->id) }}"
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


        <div class="d-flex align-items-center  bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
            <span>Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($payments->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $payments->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $payments->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                            <li class="page-item {{ $payments->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $payments->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($payments->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $payments->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px" href="{{ $payments->url($payments->lastPage()) }}">Last</a>
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
    </div>

    <script>
        // Attach event listener to the Filter link
        document.getElementById('filterLink').addEventListener('click', function() {
            // Submit the form when the filter link is clicked
            document.getElementById('filterForm').submit();
        });

        document.getElementById('resetLink').addEventListener('click', function() {
            const formElements = document.getElementById('filterForm').elements;
            Array.from(formElements).forEach(element => {
                if (element.type === 'select-one' || element.type === 'text') {
                    element.value = '';
                }
            });

            // Reset the filter form
            document.getElementById('filterForm').submit();
        });
    </script>
@endsection
