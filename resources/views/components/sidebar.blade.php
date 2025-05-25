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
                <li class="sidebar-menu-group-title">Administration</li>

                {{-- User Management --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="heroicons:user-group" class="menu-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('user.index') }}"><iconify-icon icon="heroicons:users" class="inline-icon"></iconify-icon>List</a></li>
                        <li><a href="{{ route('user.create') }}"><iconify-icon icon="heroicons:user-plus" class="inline-icon"></iconify-icon> Create </a></li>
                    </ul>
                </li>

                {{-- Landlord Management --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-key" class="menu-icon"></iconify-icon>
                        <span>Landlord</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('lanlord.index') }}"><iconify-icon icon="mdi:account-group" class="inline-icon"></iconify-icon>  List</a></li>
                        <li><a href="{{ route('lanlord.create') }}"><iconify-icon icon="mdi:account-plus" class="inline-icon"></iconify-icon> Create</a></li>
                    </ul>
                </li>

                {{-- District & Branch --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:map-marker" class="menu-icon"></iconify-icon>
                        <span>Location</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('district.index') }}"><iconify-icon icon="mdi:map" class="inline-icon"></iconify-icon> District</a></li>
                        <li><a href="{{ route('branch.index') }}"><iconify-icon icon="mdi:office-building" class="inline-icon"></iconify-icon> Branch</a></li>
                    </ul>
                </li>
            @endif

            {{-- Property Section --}}
            <li class="sidebar-menu-group-title">Property Management</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:home" class="menu-icon text-warning-main"></iconify-icon>
                    <span>Property</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{ route('property.index') }}"><iconify-icon icon="mdi:view-list" class="inline-icon"></iconify-icon>  List</a></li>

                    @if (Auth::user()->role === 'Admin')
                        <li><a href="{{ route('property.create') }}"><iconify-icon icon="mdi:home-plus" class="inline-icon"></iconify-icon>Create</a></li>
                        <li><a href="{{ route('property.report') }}"><iconify-icon icon="mdi:file-document" class="inline-icon"></iconify-icon>  Report</a></li>
                    @endif

                    @if (Auth::user()->role === 'lanlord')
                        <li><a href="{{ route('property.create.landlord') }}"><iconify-icon icon="mdi:home-plus" class="inline-icon"></iconify-icon> Add Property</a></li>
                    @endif
                </ul>
            </li>

            {{-- Property Units --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:office-building" class="menu-icon text-info-main"></iconify-icon>
                        <span>Property Units</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('unit.index') }}"><iconify-icon icon="mdi:view-list" class="inline-icon"></iconify-icon>  List</a></li>
                        <li><a href="{{ route('unit.create') }}"><iconify-icon icon="mdi:plus-circle" class="inline-icon"></iconify-icon>  Create</a></li>
                    </ul>
                </li>
            @endif

            {{-- Rent --}}
            @if (Auth::user()->role === 'Admin')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:cash" class="menu-icon text-info-main"></iconify-icon>
                        <span>Rent</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('rent.index') }}"><iconify-icon icon="mdi:view-list" class="inline-icon"></iconify-icon>  List</a></li>
                        <li><a href="{{ route('rent.create') }}"><iconify-icon icon="mdi:plus-circle" class="inline-icon"></iconify-icon>  Create</a></li>
                    </ul>
                </li>
            @endif

            {{-- Finance Section --}}
            <li class="sidebar-menu-group-title">Finance</li>

            {{-- Rent Payment for Landlords --}}
            @if (Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:cash-register" class="menu-icon text-success-main"></iconify-icon>
                        <span>Rent Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('payment.index') }}"><iconify-icon icon="mdi:view-list" class="inline-icon"></iconify-icon>  List</a></li>
                        <li><a href="{{ route('payment.create') }}"><iconify-icon icon="mdi:cash-plus" class="inline-icon"></iconify-icon>  Create</a></li>
                    </ul>
                </li>
            @endif

            {{-- Payment for Admin & Landlord --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:cash-multiple" class="menu-icon text-success-main"></iconify-icon>
                        <span>Tax Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('payment.index.tax') }}"><iconify-icon icon="mdi:receipt" class="inline-icon"></iconify-icon>  List</a></li>
                        {{-- <li><a href="{{ route('payment.create.tax') }}"><iconify-icon icon="mdi:cash-plus" class="inline-icon"></iconify-icon> Add Payment</a></li> --}}
                    </ul>
                </li>
            @endif

            <li class="sidebar-menu-group-title">Reports & Analytics</li>

            {{-- Monitoring --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:monitor-dashboard" class="menu-icon"></iconify-icon>
                        <span>Monitoring</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('monitor.index') }}"><iconify-icon icon="mdi:home-analytics" class="inline-icon"></iconify-icon>  Properties</a></li>
                    </ul>
                </li>
            @endif

            {{-- Invoice Management --}}
            @if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
                        <span>Invoice Management</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('invoiceList') }}"><iconify-icon icon="mdi:file-clock" class="inline-icon"></iconify-icon> Pending Invoices</a></li>
                        <li><a href="{{ route('invoice.paid') }}"><iconify-icon icon="mdi:file-check" class="inline-icon"></iconify-icon> Paid Invoices</a></li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</aside>
