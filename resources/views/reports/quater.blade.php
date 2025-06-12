@extends('layout.layout')

@php
    $title = 'Quarter Income';
    $subTitle = 'Quarterly Income';
    $script = '<script>
        $(document).on("click", ".remove-item-btn", function() {
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
    </div>

    <form method="GET" action="{{ route('reports.quaterly.income') }}" id="filterForm">
        <div
            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="navbar-search">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search"
                        value="{{ request()->search }}">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </div>

                <select name="status" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                    <option value="">Year </option>
                    <option value="">Current</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>

                </select>

            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a href="javascript:void(0);" id="filterLink"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                    <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                    Filter
                </a>

                <a href="javascript:void(0);" id="resetLink"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                    <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm overflow-x-auto">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th scope="col">Quarter</th>
                        <th scope="col">Tax Billed</th>
                        <th scope="col">Tax Collected</th>
                        <th scope="col">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quarterSummaries as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td>{{ $item['billed'] }}</td>
                            <td>{{ $item['collected'] }}</td>
                            <td>{{ $item['outstanding'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('filterLink')?.addEventListener('click', function() {
            document.getElementById('filterForm')?.submit();
        });

        document.getElementById('resetLink')?.addEventListener('click', function() {
            const formElements = document.getElementById('filterForm')?.elements;
            Array.from(formElements || []).forEach(element => {
                if (element.type === 'select-one' || element.type === 'text') {
                    element.value = '';
                }
            });
            document.getElementById('filterForm')?.submit();
        });
    </script>
@endsection
