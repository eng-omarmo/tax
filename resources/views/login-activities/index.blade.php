@extends('layout.layout')
<?php
$title = ' User Activities';
$subTitle = 'User Activities';
?>
@section('content')
    <div class="card h-100 p-0 radius-12">

    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Platform</th>
                        <th>Device Type</th>
                        <th>Login Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        @php
                            $agent = new Jenssegers\Agent\Agent();
                            $agent->setUserAgent($activity->user_agent);
                            $browser = $agent->browser() . ' ' . $agent->version($agent->browser());
                            $platform = $agent->platform() . ' ' . $agent->version($agent->platform());
                        @endphp
                        <tr>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ $activity->ip_address }}</td>
                            <td>{{ $browser }}</td>
                            <td>{{ $platform }}</td>
                            <td>{{ $activity->device }}</td>
                            <td>{{ $activity->logged_in_at ? $activity->logged_in_at : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No login activities found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center  bg-base px-24 py-12 justify-content-between flex-wrap gap-2 mt-24">
            <span>Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }}
                entries</span>
            <div class="pagination-container">
                <div class="card-body p-24">
                    <ul class="pagination d-flex flex-wrap bg-base align-items-center gap-2 justify-content-center">
                        @if ($activities->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">First</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $activities->url(1) }}">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $activities->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($activities->getUrlRange(1, $activities->lastPage()) as $page => $url)
                            <li class="page-item {{ $activities->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px {{ $activities->currentPage() == $page ? 'bg-primary-600 text-white' : '' }}"
                                    href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($activities->hasMorePages())
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $activities->nextPageUrl() }}">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="{{ $activities->url($activities->lastPage()) }}">Last</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px "
                                    href="javascript:void(0)">Next</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px"
                                    href="javascript:void(0)">Last</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
@endsection
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
        document.getElementById('filterForm').submit();
    });
</script>
