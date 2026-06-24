<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">📚 Perpustakaan</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="{{ route('profile.index') }}" class="d-block text-white">
                    {{ Auth::user()->name }}
                </a>
                <small class="text-muted">
                    @if(Auth::user()->isAdmin())
                        <span class="badge badge-danger">Admin</span>
                    @else
                        <span class="badge badge-info">Member</span>
                    @endif
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Books - All users -->
                <li class="nav-item">
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Buku</p>
                    </a>
                </li>

                <!-- Admin Only -->
                @if(Auth::user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Member</p>
                    </a>
                </li>
                @endif

                <!-- Loans - All users -->
                <li class="nav-item">
                    <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>Peminjaman</p>
                        @php
                            $pendingLoans = App\Models\Loan::where('status', 'dipinjam')->count();
                        @endphp
                        @if($pendingLoans > 0 && Auth::user()->isAdmin())
                            <span class="badge badge-warning ml-2">{{ $pendingLoans }}</span>
                        @endif
                    </a>
                </li>

                <!-- Denda - All users -->
                <li class="nav-item">
                    <a href="{{ route('denda.index') }}" class="nav-link {{ request()->routeIs('denda.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Denda</p>
                        @php
                            $unpaidCount = App\Models\Loan::where('payment_status', 'belum_bayar')
                                                       ->where('fine_amount', '>', 0)
                                                       ->count();
                        @endphp
                        @if($unpaidCount > 0)
                            <span class="badge badge-danger ml-2">{{ $unpaidCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-item">
                    <hr class="my-2" style="border-color: rgba(255,255,255,0.1);">
                </li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>