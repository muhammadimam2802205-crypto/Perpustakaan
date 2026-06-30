<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @php
                    $unpaidCount = App\Models\Loan::where('payment_status', 'belum_bayar')
                                               ->where('fine_amount', '>', 0)
                                               ->count();
                @endphp
                @if($unpaidCount > 0)
                    <span class="badge badge-danger navbar-badge">{{ $unpaidCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ $unpaidCount }} Notifikasi</span>
                <div class="dropdown-divider"></div>
                @if($unpaidCount > 0)
                    <a href="{{ route('denda.index') }}" class="dropdown-item">
                        <i class="fas fa-money-bill-wave mr-2"></i> Ada denda belum dibayar
                        <span class="float-right text-muted text-sm">Segera</span>
                    </a>
                @else
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-check-circle mr-2 text-success"></i> Tidak ada notifikasi
                    </a>
                @endif
            </div>
        </li>

        <!-- User Profile -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> 
                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.index') }}" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>