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

                @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Tax officer')
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:percent" class="menu-icon"></iconify-icon>

                        <span>Tax Rates</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('tax.rate.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Properties Tax Rate</a>
                        </li>

                        <li>
                            <a href="{{ route('tax.rate.create') }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                                Add Tax Rate
                            </a>
                        </li>

                    </ul>

                </li>
            @endif

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

                <li class="dropdown">
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

                </li>


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

            {{-- <li class="dropdown">
                <a  href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:invoice-03" class="menu-icon"></iconify-icon>
                    <span>Invoice</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                    <a href="{{ route('invoiceList') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> List</a>
                    </li>
                    <li>
                    <a href="{{ route('invoicePreview') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Preview</a>
                    </li>
                    <li>
                    <a href="{{ route('invoiceAdd') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add new</a>
                    </li>
                    <li>
                    <a href="{{ route('invoiceEdit') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Edit</a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li class="dropdown">
                <a  href="javascript:void(0)">
                    <i class="ri-robot-2-line text-xl me-6 d-flex w-auto"></i>
                    <span>Ai Application</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('textGenerator') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Text Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('codeGenerator') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Code Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('imageGenerator') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Image Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('voiceGenerator') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Voice Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('videoGenerator') }}"><i class="ri-circle-fill circle-icon text-success-main w-auto"></i> Video Generator</a>
                    </li>
                </ul>
            </li> --}}

            {{-- <li class="dropdown">
                <a  href="javascript:void(0)">
                    <i class="ri-btc-line text-xl me-6 d-flex w-auto"></i>
                    <span>Crypto Currency</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('wallet') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Wallet</a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Marketplace</a>
                    </li>
                    <li>
                        <a href="{{ route('marketplaceDetails') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Marketplace Details</a>
                    </li>
                    <li>
                    <a  href="{{ route('portfolio') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Portfolios</a>
                    </li>
                </ul>
            </li> --}}

            {{-- <li class="sidebar-menu-group-title">UI Elements</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Components</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('typography') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Typography</a>
                    </li>
                    <li>
                        <a href="{{ route('colors') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Colors</a>
                    </li>
                    <li>
                        <a href="{{ route('button') }}"><i
                                class="ri-circle-fill circle-icon text-success-main w-auto"></i> Button</a>
                    </li>
                    <li>
                        <a href="{{ route('dropdown') }}"><i
                                class="ri-circle-fill circle-icon text-lilac-600 w-auto"></i> Dropdown</a>
                    </li>
                    <li>
                        <a href="{{ route('alert') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Alerts</a>
                    </li>
                    <li>
                        <a href="{{ route('card') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Card</a>
                    </li>
                    <li>
                        <a href="{{ route('carousel') }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i> Carousel</a>
                    </li>
                    <li>
                        <a href="{{ route('avatar') }}"><i
                                class="ri-circle-fill circle-icon text-success-main w-auto"></i> Avatars</a>
                    </li>
                    <li>
                        <a href="{{ route('progress') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Progress bar</a>
                    </li>
                    <li>
                        <a href="{{ route('tabs') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Tab & Accordion</a>
                    </li>
                    <li>
                        <a href="{{ route('pagination') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Pagination</a>
                    </li>
                    <li>
                        <a href="{{ route('badges') }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i> Badges</a>
                    </li>
                    <li>
                        <a href="{{ route('tooltip') }}"><i
                                class="ri-circle-fill circle-icon text-lilac-600 w-auto"></i> Tooltip & Popover</a>
                    </li>
                    <li>
                        <a href="{{ route('videos') }}"><i class="ri-circle-fill circle-icon text-cyan w-auto"></i>
                            Videos</a>
                    </li>
                    <li>
                        <a href="{{ route('starRating') }}"><i
                                class="ri-circle-fill circle-icon text-indigo w-auto"></i> Star Ratings</a>
                    </li>
                    <li>
                        <a href="{{ route('tags') }}"><i class="ri-circle-fill circle-icon text-purple w-auto"></i>
                            Tags</a>
                    </li>
                    <li>
                        <a href="{{ route('list') }}"><i class="ri-circle-fill circle-icon text-red w-auto"></i>
                            List</a>
                    </li>
                    <li>
                        <a href="{{ route('calendar') }}"><i class="ri-circle-fill circle-icon text-yellow w-auto"></i>
                            Calendar</a>
                    </li>
                    <li>
                        <a href="{{ route('radio') }}"><i class="ri-circle-fill circle-icon text-orange w-auto"></i>
                            Radio</a>
                    </li>
                    <li>
                        <a href="{{ route('switch') }}"><i class="ri-circle-fill circle-icon text-pink w-auto"></i>
                            Switch</a>
                    </li>
                    <li>
                        <a href="{{ route('imageUpload') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Upload</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="heroicons:document" class="menu-icon"></iconify-icon>
                    <span>Forms</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('form') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Input Forms</a>
                    </li>
                    <li>
                        <a href="{{ route('formLayout') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Input Layout</a>
                    </li>
                    <li>
                        <a href="{{ route('formValidation') }}"><i
                                class="ri-circle-fill circle-icon text-success-main w-auto"></i> Form Validation</a>
                    </li>
                    <li>
                        <a href="{{ route('wizard') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Form Wizard</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mingcute:storage-line" class="menu-icon"></iconify-icon>
                    <span>Table</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('tableBasic') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Basic Table</a>
                    </li>
                    <li>
                        <a href="{{ route('tableData') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Data Table</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:pie-chart-outline" class="menu-icon"></iconify-icon>
                    <span>Chart</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('lineChart') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Line Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('columnChart') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Column Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('pieChart') }}"><i
                                class="ri-circle-fill circle-icon text-success-main w-auto"></i> Pie Chart</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('widgets') }}">
                    <iconify-icon icon="fe:vector" class="menu-icon"></iconify-icon>
                    <span>Widgets</span>
                </a>
            </li>


            {{-- <li class="dropdown">
                <a  href="javascript:void(0)">
                    <i class="ri-user-settings-line text-xl me-6 d-flex w-auto"></i>
                    <span>Role & Access</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a  href="{{ route('roleAaccess') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Role & Access</a>
                    </li>
                    <li>
                        <a  href="{{ route('assignRole') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Assign Role</a>
                    </li>
                </ul>
            </li> --}}

            {{-- <li class="sidebar-menu-group-title">Application</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="simple-line-icons:vector" class="menu-icon"></iconify-icon>
                    <span>Authentication</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('signin') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sign In</a>
                    </li>
                    <li>
                        <a href="{{ route('signup') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Sign Up</a>
                    </li>
                    <li>
                        <a href="{{ route('forgotPassword') }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i> Forgot Password</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('gallery') }}">
                    <iconify-icon icon="solar:gallery-wide-linear" class="menu-icon"></iconify-icon>
                    <span>Gallery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pricing') }}">
                    <iconify-icon icon="hugeicons:money-send-square" class="menu-icon"></iconify-icon>
                    <span>Pricing</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <i class="ri-news-line text-xl me-6 d-flex w-auto"></i>
                    <span>Blog</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('blog') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Blog</a>
                    </li>
                    <li>
                        <a href="{{ route('blogDetails') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Blog Details</a>
                    </li>
                    <li>
                        <a href="{{ route('addBlog') }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i> Add Blog</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('testimonials') }}">
                    <i class="ri-star-line text-xl me-6 d-flex w-auto"></i>
                    <span>Testimonial</span>
                </a>
            </li>
            <li>
                <a href="{{ route('faq') }}">
                    <iconify-icon icon="mage:message-question-mark-round" class="menu-icon"></iconify-icon>
                    <span>FAQs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('error') }}">
                    <iconify-icon icon="streamline:straight-face" class="menu-icon"></iconify-icon>
                    <span>404</span>
                </a>
            </li>
            <li>
                <a href="{{ route('termsCondition') }}">
                    <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                    <span>Terms & Conditions</span>
                </a>
            </li>
            <li>
                <a href="{{ route('comingSoon') }}">
                    <i class="ri-rocket-line text-xl me-6 d-flex w-auto"></i>
                    <span>Coming Soon</span>
                </a>
            </li>
            <li>
                <a href="{{ route('maintenance') }}">
                    <i class="ri-hammer-line text-xl me-6 d-flex w-auto"></i>
                    <span>Maintenance</span>
                </a>
            </li>
            <li>
                <a href="{{ route('blankPage') }}">
                    <i class="ri-checkbox-multiple-blank-line text-xl me-6 d-flex w-auto"></i>
                    <span>Blank Page</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                    <span>Settings</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('company') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Company</a>
                    </li>
                    <li>
                        <a href="{{ route('notification') }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Notification</a>
                    </li>
                    <li>
                        <a href="{{ route('notificationAlert') }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i> Notification Alert</a>
                    </li>
                    <li>
                        <a href="{{ route('theme') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Theme</a>
                    </li>
                    <li>
                        <a href="{{ route('currencies') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Currencies</a>
                    </li>
                    <li>
                        <a href="{{ route('language') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Languages</a>
                    </li>
                    <li>
                        <a href="{{ route('paymentGateway') }}"><i
                                class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Payment Gateway</a>
                    </li>
                </ul>
            </li>
        </ul> --}}
    </div>
</aside>
