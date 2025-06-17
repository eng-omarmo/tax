@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Login Activities</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('login.activities.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_id">Filter by User</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                                        <td>{{ $activity->logged_in_at ? $activity->logged_in_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No login activities found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $activities->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
