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
                    <iconify-icon icon="material-symbols:dashboard" class="menu-icon text-primary-light"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Admin Section --}}
            @can('view users')
                <li class="sidebar-menu-group-title">Administration</li>

                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-group" class="menu-icon text-secondary"></iconify-icon>
                        <span>Users</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('user.index') }}"><iconify-icon icon="heroicons:users"
                                    class="inline-icon"></iconify-icon>List</a></li>
                        @can('create users')
                        <li><a href="{{ route('user.create') }}"><iconify-icon icon="heroicons:user-plus"
                                    class="inline-icon"></iconify-icon>Create</a></li>
                        @endcan
                        @can('view login activities')
                        <li><a href="{{ route('login.activities.index') }}"><iconify-icon icon="heroicons:user-plus"
                                    class="inline-icon"></iconify-icon>Activities</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view landlords')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-tie" class="menu-icon text-secondary"></iconify-icon>
                        <span>Landlords</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('lanlord.index') }}"><iconify-icon icon="mdi:account-group"
                                    class="inline-icon"></iconify-icon>List</a></li>
                        @can('create landlords')
                        <li><a href="{{ route('lanlord.create') }}"><iconify-icon icon="mdi:account-plus"
                                    class="inline-icon"></iconify-icon>Create</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view locations')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:map-marker-radius" class="menu-icon text-secondary"></iconify-icon>
                        <span>Location</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('district.index') }}"><iconify-icon icon="mdi:map"
                                    class="inline-icon"></iconify-icon>District</a></li>
                        <li><a href="{{ route('branch.index') }}"><iconify-icon icon="mdi:office-building"
                                    class="inline-icon"></iconify-icon>Branch</a></li>
                    </ul>
                </li>
            @endcan

            @canany(['view roles', 'view permissions', 'assign roles'])
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:shield-account" class="menu-icon text-warning"></iconify-icon>
                    <span>Role Management</span>
                </a>
                <ul class="sidebar-submenu">
                    @can('view roles')
                        <li><a href="{{ route('roles.index') }}"><iconify-icon icon="mdi:shield-key"
                                    class="inline-icon"></iconify-icon>Roles</a></li>
                    @endcan

                    @can('view permissions')
                        <li><a href="{{ route('permissions.index') }}"><iconify-icon icon="mdi:key"
                                    class="inline-icon"></iconify-icon>Permissions</a></li>
                    @endcan

                    @can('assign roles')
                        <li><a href="{{ route('roles.assign') }}"><iconify-icon icon="mdi:account-key"
                                    class="inline-icon"></iconify-icon>Assign Roles</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- Property Section --}}
            @canany(['view properties', 'create properties'])
            <li class="sidebar-menu-group-title">Property Management</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:building" class="menu-icon text-warning-main"></iconify-icon>
                    <span>Properties</span>
                </a>
                <ul class="sidebar-submenu">
                    @can('view properties')
                        <li><a href="{{ route('property.index') }}"><iconify-icon icon="mdi:view-list"
                                    class="inline-icon"></iconify-icon>List</a></li>
                        <li><a href="{{ route('property.report') }}"><iconify-icon icon="mdi:file-chart"
                                    class="inline-icon"></iconify-icon>Report</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('view units')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:door" class="menu-icon text-info-main"></iconify-icon>
                        <span>Units</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('unit.index') }}"><iconify-icon icon="mdi:view-list"
                                    class="inline-icon"></iconify-icon>List</a></li>
                    </ul>
                </li>
            @endcan

            {{-- Rent Management --}}
            @can('view rents')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:cash-register" class="menu-icon text-info-main"></iconify-icon>
                        <span>Rent</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('rent.index') }}"><iconify-icon icon="mdi:view-list"
                                    class="inline-icon"></iconify-icon>List</a></li>
                    </ul>
                </li>
            @endcan

            {{-- Finance Section --}}
            @can('view finances')
            <li class="sidebar-menu-group-title">Finance</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:chart-areaspline" class="menu-icon text-blue-500"></iconify-icon>
                    <span>Tax Payment</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{ route('payment.index.tax') }}"><iconify-icon icon="mdi:receipt-text"
                                class="inline-icon"></iconify-icon>List</a></li>
                </ul>
            </li>
            @endcan

            @canany(['view monitoring', 'view invoices', 'view notifications'])
            <li class="sidebar-menu-group-title"> Analytics</li>

            @can('view monitoring')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:chart-areaspline" class="menu-icon text-blue-500"></iconify-icon>
                        <span>Monitoring</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('monitor.index') }}"><iconify-icon icon="mdi:home-analytics"
                                    class="inline-icon"></iconify-icon>Properties</a></li>
                    </ul>
                </li>
            @endcan

            @can('view invoices')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:file-document-multiple"
                            class="menu-icon text-yellow-500"></iconify-icon>
                        <span>Invoices</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="{{ route('invoice.paid') }}"><iconify-icon icon="mdi:file-check"
                                    class="inline-icon"></iconify-icon>Paid</a></li>
                        <li><a href="{{ route('invoiceList') }}"><iconify-icon icon="mdi:file-clock"
                                    class="inline-icon"></iconify-icon>Pending</a></li>
                    </ul>
                </li>
            @endcan

            @can('view notifications')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:bell-badge" class="menu-icon text-red-500"></iconify-icon>
                        <span>Notifications</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <a href="{{ route('notifications.index') }}">
                                <iconify-icon icon="mdi:bell-alert"
                                    class="inline-icon text-primary"></iconify-icon>Notified Properties
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            @endcanany

            @can('view reports')
            <li class="sidebar-menu-group-title"> Reports</li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:file-chart" class="menu-icon text-blue-500"></iconify-icon>
                    <span>Reports</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('reports.income') }}">
                            <iconify-icon icon="mdi:account-tie" class="inline-icon"></iconify-icon> Income Report
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.quaterly.income') }}">
                            <iconify-icon icon="mdi:home-city" class="inline-icon"></iconify-icon>
                            Quater Income
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.today_report') }}">
                            <iconify-icon icon="mdi:home-city" class="inline-icon"></iconify-icon> Today's Activities
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.district.income') }}">
                            <iconify-icon icon="mdi:map-marker-radius-outline" class="inline-icon"></iconify-icon>
                            District Income
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
        </ul>
    </div>

    </div>

</aside>
