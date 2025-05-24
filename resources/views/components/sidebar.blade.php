<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    {{-- Logo --}}
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Admin Section --}}
            @if (Auth::user()->role === 'Admin')
                <li class="sidebar-menu-group-title">Admin Management</li>

                {{-- User Management --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('user.index') }}">Users List</a></li>
                        <li><a href="{{ route('user.create') }}">Add User</a></li>
                    </ul>
                </li>

                {{-- Landlord Management --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-cog" class="menu-icon"></iconify-icon>
                        <span>Landlord</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('lanlord.index') }}">Landlord List</a></li>
                        <li><a href="{{ route('lanlord.create') }}">Add Landlord</a></li>
                    </ul>
                </li>

                {{-- District & Branch --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:map-outline" class="menu-icon"></iconify-icon>
                        <span>Location</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('district.index') }}">District</a></li>
                        <li><a href="{{ route('branch.index') }}">Branch</a></li>
                    </ul>
                </li>
            @endif

            {{-- Property Section --}}
            <li class="sidebar-menu-group-title">Property Management</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-home-fill text-warning-main text-xl"></i>
                    <span>Property</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{ route('property.index') }}">Property List</a></li>

                    @if (Auth::user()->role === 'Admin')
                        <li><a href="{{ route('property.create') }}">Add Property</a></li>
                        <li><a href="{{ route('property.report') }}">Property Report</a></li>
                    @endif

                    @if (Auth::user()->role === 'lanlord')
                        <li><a href="{{ route('property.create.landlord') }}">Add Property</a></li>
                    @endif
                </ul>
            </li>

            {{-- Property Units --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-user-fill text-info-main text-xl"></i>
                        <span>Property Units</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('unit.index') }}">Unit List</a></li>
                        <li><a href="{{ route('unit.create') }}">Add Unit</a></li>
                    </ul>
                </li>
            @endif

            {{-- Rent --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-wallet-fill text-info-main text-xl"></i>
                        <span>Rent</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('rent.index') }}">Rent List</a></li>
                        <li><a href="{{ route('rent.create') }}">Add Rent</a></li>
                    </ul>
                </li>
            @endif

            {{-- Rent Payment for Landlords --}}
            @if (Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-money-dollar-circle-fill text-success-main text-xl"></i>
                        <span>Rent Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('payment.index') }}">Payment List</a></li>
                        <li><a href="{{ route('payment.create') }}">Add Payment</a></li>
                    </ul>
                </li>
            @endif

            {{-- Payment for Admin & Landlord --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-money-dollar-circle-fill text-success-main text-xl"></i>
                        <span>Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('payment.index.tax') }}">Payment List</a></li>
                        {{-- <li><a href="{{ route('payment.create.tax') }}">Add Payment</a></li> --}}
                    </ul>
                </li>
            @endif

            {{-- Monitoring --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="bi:people" class="menu-icon"></iconify-icon>
                        <span>Monitoring</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('monitor.index') }}">Monitoring Properties</a></li>
                    </ul>
                </li>
            @endif

            {{-- Invoice Management --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
                        <span>Invoice Managment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('invoiceList') }}">Pending </a></li>
                        <li><a href="{{ route('invoice.paid') }}">Paid </a></li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</aside>
