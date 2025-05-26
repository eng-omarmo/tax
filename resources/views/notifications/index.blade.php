@extends('layout.layout')

@php
    $title = 'Notified Properties';
    $subTitle = 'View and Manage Property Notifications';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session()->get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="GET" action="{{ route('notifications.index') }}" id="filterForm">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="navbar-search">
                        <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search Property or Landlord" value="{{ request()->search }}">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </div>

                    <select name="quarter" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">All Quarters</option>
                        @foreach ($quarters as $quarter)
                            <option value="{{ $quarter }}" {{ request()->quarter == $quarter ? 'selected' : '' }}>
                                {{ $quarter }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">All Years</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request()->year == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <select name="is_notified" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option value="">Notification Status</option>
                        <option value="1" {{ request()->is_notified == '1' ? 'selected' : '' }}>Notified</option>
                        <option value="0" {{ request()->is_notified == '0' ? 'selected' : '' }}>Pending</option>
                    </select>

                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt" class="icon text-xl line-height-1"></iconify-icon>
                        Filter
                    </button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary text-sm btn-sm px-12 py-12 radius-4 d-flex align-items-center">
                        <iconify-icon icon="ic:baseline-filter-alt-off" class="icon text-xl line-height-1"></iconify-icon>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm overflow-x-auto">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>S.L</th>
                        <th>Property Name</th>
                        <th>Address / House Code</th>
                        <th>Landlord</th>
                        <th>District / Branch</th>
                        <th>Notification Date</th>
                        <th>Quarter</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notifications as $key => $notification)
                        <tr>
                            <td>{{ $notifications->firstItem() + $key }}</td>
                            <td>{{ $notification->property->property_name ?? 'N/A' }}</td>
                            <td>{{ $notification->property->address ?? $notification->property->house_code ?? 'N/A' }}</td>
                            <td>{{ $notification->property->landlord->user->name ?? 'N/A' }}</td>
                            <td>
                                {{ $notification->property->district->name ?? 'N/A' }} /
                                {{ $notification->property->branch->name ?? 'N/A' }}
                            </td>
                            <td>{{ $notification->updated_at->format('Y-m-d H:i A') }}</td>
                            <td>{{ $notification->quarter }}</td>
                            <td>{{ $notification->year }}</td>
                            <td>
                                @if ($notification->is_notified)
                                    <span class="badge bg-success-subtle text-success">Notified</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <form action="{{ route('notifications.renotify', $notification->property_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to re-notify this property?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info-light" title="Re-Notify">
                                            <iconify-icon icon="solar:notification-lines-bold-duotone"></iconify-icon>
                                        </button>
                                    </form>
                                    @if(false) {{-- Placeholder for optional history link --}}
                                    <a href="{{ route('notifications.history', $notification->property_id) }}" class="btn btn-sm btn-secondary-light" title="View History">
                                        <iconify-icon icon="solar:history-bold-duotone"></iconify-icon>
                                    </a>
                                    @endif
                                    <a href="{{ route('monitor.show', $notification->property_id) }}" class="btn btn-sm btn-primary-light" title="View Property">
                                        <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No notified properties found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-20">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    </div>

    {{-- Optional: History View --}}
    {{-- Create resources/views/notifications/history.blade.php if you implement the history feature --}}

@endsection

@push('scripts')
<script>
    // Add any specific JS for this page if needed
</script>
@endpush
