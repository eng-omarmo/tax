<aside class="sidebar">

    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- <li class="sidebar-menu-group-title">Pages</li> --}}
            {{-- <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    {{-- <li>
                        <a href="{{ route('index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> AI</a>
                    </li> --}}
                    {{-- <li>
                        <a href="{{ route('index') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> CRM</a>
                    </li>
                    <li> --}}
                        {{-- <a href="{{ route('index3') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> eCommerce</a>
                    </li>
                    <li>
                    <a href="{{ route('index4') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Cryptocurrency</a>
                    </li>
                    <li>
                    <a href="{{ route('index5') }}"><i class="ri-circle-fill circle-icon text-success-main w-auto"></i> Investment</a>
                    </li>
                    <li>
                    <a href="{{ route('index6') }}"><i class="ri-circle-fill circle-icon text-purple w-auto"></i> LMS</a>
                    </li>
                    <li>
                    <a href="{{ route('index7') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> NFT & Gaming</a>
                    </li>
                    <li>
                    <a href="{{ route('index8') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Medical</a>
                    </li>
                    <li>
                    <a href="{{ route('index9') }}"><i class="ri-circle-fill circle-icon text-purple w-auto"></i> Analytics</a>
                    </li>
                    <li>
                    <a href="{{ route('index10') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> POS & Inventory </a>
                    </li> --}}
                {{-- </ul>
            </li> --}}
            <li class="sidebar-menu-group-title">Pages</li>
            {{-- <li>
                  <a href="{{ route('email') }}">
                    <iconify-icon icon="mage:email" class="menu-icon"></iconify-icon>
                    <span>User</span>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('chatMessage') }}">
                    <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
                    <span></span>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('calendar') }}">
                    <iconify-icon icon="solar:calendar-outline" class="menu-icon"></iconify-icon>
                    <span>Calendar</span>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('kanban') }}">
                    <iconify-icon icon="material-symbols:map-outline" class="menu-icon"></iconify-icon>
                    <span>Kanban</span>
                </a>
            </li> --}}

            @if (Auth::user()->role === 'Admin')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('user.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List</a>
                        </li>
                        {{-- <li>
                        <a  href="{{ route('usersGrid') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Users Grid</a>
                    </li> --}}
                        <li>
                            <a href="{{ route('user.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add User</a>
                        </li>
                        {{-- <li>
                        <a  href="{{ route('viewProfile') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> View Profile</a>
                    </li> --}}
                    </ul>

                </li>


                {{-- <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:briefcase-outline" class="menu-icon"></iconify-icon>

                        <span>Business</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('business.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                business List </a>
                        </li>


                        <li>
                            <a href="{{ route('business.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add Business

                            </a>
                        </li>

                    </ul>

                </li> --}}


                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-cog" class="menu-icon"></iconify-icon>


                        <span>Landlord</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('lanlord.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Landlord List </a>
                        </li>


                        <li>
                            <a href="{{ route('lanlord.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add Landlord

                            </a>
                        </li>

                    </ul>

                </li>


                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:map-outline" class="menu-icon"></iconify-icon>

                        <span>District</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('district.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                District </a>
                        </li>


                        <li>
                            <a href="{{ route('branch.index') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Branch

                            </a>
                        </li>

                    </ul>

                </li>
            @endif

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-home-fill text-warning-main text-xl"></i>
                    <span>Property</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('property.index') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                            Property List</a>
                    </li>
                    @if (Auth::user()->role === 'Admin')
                        <li>
                            <a href="{{ route('property.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Property</a>
                        </li>
                        <li>
                            <a href="{{ route('property.report') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Property
                                Report</a>
                        </li>
                    @endif
                    @if (Auth::user()->role === 'lanlord')
                        <li>
                            <a href="{{ route('property.create.landlord') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Property</a>
                        </li>
                    @endif
                    {{-- <li>
                        <a href="`"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Pending
                            Property</a>
                    </li> --}}



                </ul>

            </li>
            @if (Auth::user()->role == 'Landlord' || Auth::user()->role == 'Admin')
                {{-- <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-user-fill text-info-main text-xl"></i>
                        <span>Tenant</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('tenant.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Tenant List</a>
                        </li>

                        <li>
                            <a href="{{ route('tenant.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Tenant</a>
                        </li>

                    </ul>

                </li> --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-user-fill text-info-main text-xl"></i>
                        <span>Property Units</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('unit.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Unit List</a>
                        </li>

                        <li>
                            <a href="{{ route('unit.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Unit</a>
                        </li>

                    </ul>

                </li>

                {{-- <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-user-fill text-info-main text-xl"></i>
                        <span>Account Management</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('payment.method.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                             Payment Method</a>
                        </li>

                        <li>
                            <a href="{{ route('account.index') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Accounts
                                  </a>
                        </li>

                    </ul>

                </li> --}}


                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-wallet-fill text-info-main text-xl"></i>


                        <span>Rent</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('rent.index') }}"><i
                                    class="ri-circle-fill circle-icon text-info-600 w-auto"></i>
                                Rent List</a>
                        </li>

                        <li>
                            <a href="{{ route('rent.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Rent</a>
                        </li>

                    </ul>
                </li>
            @endif
            {{-- <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-wallet-fill text-info-main text-xl"></i>


                    <span>Taxes</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('tax.index') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i>
                            Tax List</a>
                    </li>
                    @if (auth()->user()->role == 'Admin')
                        <li>
                            <a href="{{ route('tax.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Tax</a>
                        </li>
                    @endif

                </ul>
            </li> --}}
            @if (auth()->user()->role == 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-money-dollar-circle-fill text-success-main text-xl"></i>

                        <span>Rent Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('payment.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Payment List</a>
                        </li>

                        <li>
                            <a href="{{ route('payment.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Payment</a>
                        </li>

                    </ul>

                </li>
            @endif
            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Landlord')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <i class="ri-money-dollar-circle-fill text-success-main text-xl"></i>

                        <span>Payment</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('payment.index.tax') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Payment List</a>
                        </li>

                        {{-- <li>
                            <a href="{{ route('payment.create.tax') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add
                                Payment</a>
                        </li> --}}

                    </ul>

                </li>
            @endif
            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="bi:people" class="menu-icon"></iconify-icon>
                        <span>Monitoring</span>

                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('monitor.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Monitoring Properties </a>
                        </li>

                        {{-- <li>
                            <a href="#"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Track
                                Appointment
                            </a>
                        </li> --}}

                    </ul>

                </li>
            @endif





            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>

                        <span>Invoice Management</span>
                    </a>
                    <ul class="sidebar-submenu">

                        <li>
                            <a href="{{ route('invoiceList') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Invoice  </a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('invoice.create') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Generate Invoice </a>
                        </li> --}}


                    </ul>

                </li>
            @endif



    </div>
</aside>
