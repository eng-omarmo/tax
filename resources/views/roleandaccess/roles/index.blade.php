@extends('layout.layout')
@php
    $title='Roles Management';
    $subTitle = 'Roles List';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <form class="navbar-search">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
                <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" name="status">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div>
                @can('create roles')
                <a href="{{ route('roles.create') }}" class="btn btn-primary radius-8 py-8 px-20">
                    <iconify-icon icon="mdi:plus" class="text-lg me-6"></iconify-icon>
                    Add New Role
                </a>
                @endcan
            </div>
        </div>

        <div class="card-body p-24">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">S.L</th>
                            <th scope="col">Create Date</th>
                            <th scope="col">Role</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $role->created_at->format('d M Y') }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $role->status == 'Active' ? 'bg-success' : 'bg-danger' }} radius-4 py-4 px-12">
                                        {{ $role->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @can('edit roles')
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary radius-4 py-4 px-12">
                                            <iconify-icon icon="mdi:pencil" class="text-lg"></iconify-icon>
                                        </a>
                                        @endcan

                                        @if($role->name !== 'Admin' && auth()->user()->can('delete roles'))
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger radius-4 py-4 px-12">
                                                <iconify-icon icon="mdi:delete" class="text-lg"></iconify-icon>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
