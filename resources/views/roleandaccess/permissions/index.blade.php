@extends('layout.layout')
@php
    $title='Permissions Management';
    $subTitle = 'Permissions List';
@endphp

@section('content')
    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <form class="navbar-search">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div>
                @can('assign permissions')
                <a href="{{ route('permissions.create') }}" class="btn btn-primary radius-8 py-8 px-20">
                    <iconify-icon icon="mdi:plus" class="text-lg me-6"></iconify-icon>
                    Add New Permission
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
                            <th scope="col">Permission Name</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $index => $permission)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @can('assign permissions')
                                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary radius-4 py-4 px-12">
                                            <iconify-icon icon="mdi:pencil" class="text-lg"></iconify-icon>
                                        </a>

                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger radius-4 py-4 px-12">
                                                <iconify-icon icon="mdi:delete" class="text-lg"></iconify-icon>
                                            </button>
                                        </form>
                                        @endcan
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
