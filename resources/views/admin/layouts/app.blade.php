
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin') - bisaKerja</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f8f5;
            color: #263b30;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 230px;
            background: #ffffff;
            border-right: 1px solid #e8eee9;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
        }

        .sidebar-logo {
            height: 65px;
            display: flex;
            align-items: center;
            padding: 0 25px;
            border-bottom: 1px solid #edf1ee;
        }

        .sidebar-logo h2 {
            color: #347650;
            font-size: 21px;
        }

        .sidebar-menu {
            padding: 25px 15px;
            flex: 1;
        }

        .menu-label {
            font-size: 10px;
            color: #9aa99e;
            margin: 0 10px 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .menu-link {
            display: block;
            padding: 12px 13px;
            margin-bottom: 6px;
            border-radius: 7px;
            text-decoration: none;
            color: #66776c;
            font-size: 13px;
            transition: .2s;
        }

        .menu-link:hover,
        .menu-link.active {
            background: #e7f2e9;
            color: #347650;
            font-weight: bold;
        }

        .sidebar-bottom {
            padding: 15px;
            border-top: 1px solid #edf1ee;
        }

        .logout-button {
            width: 100%;
            border: none;
            background: transparent;
            color: #718477;
            text-align: left;
            padding: 10px;
            cursor: pointer;
            font-size: 13px;
        }

        .logout-button:hover {
            color: #d9534f;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 230px;
            width: calc(100% - 230px);
        }

        .topbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #e8eee9;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 35px;
        }

        .admin-profile {
            font-size: 12px;
            color: #347650;
            font-weight: bold;
        }

        .page-content {
            padding: 30px 35px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h1 {
            font-size: 22px;
            color: #263b30;
            margin-bottom: 7px;
        }

        .page-heading p {
            font-size: 12px;
            color: #849188;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                width: 190px;
            }

            .main-content {
                margin-left: 190px;
                width: calc(100% - 190px);
            }

            .page-content {
                padding: 20px 15px;
            }

            .topbar {
                padding: 0 15px;
            }
        }

        @media (max-width: 500px) {
            .sidebar {
                width: 65px;
            }

            .sidebar-logo {
                padding: 0;
                justify-content: center;
            }

            .sidebar-logo h2 {
                font-size: 12px;
            }

            .menu-label,
            .menu-link span,
            .logout-button span {
                display: none;
            }

            .menu-link,
            .logout-button {
                text-align: center;
                padding: 12px 3px;
                font-size: 11px;
            }

            .main-content {
                margin-left: 65px;
                width: calc(100% - 65px);
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="admin-wrapper">

    <aside class="sidebar">

        <div class="sidebar-logo">
            <h2>bisaKerja</h2>
        </div>

        <nav class="sidebar-menu">

            <p class="menu-label">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}"
               class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.verifikasi') }}"
            class="menu-link {{ request()->routeIs('admin.verifikasi') ? 'active' : '' }}">
                <span>Verifikasi Akun</span>
            </a>

            <a href="{{ route('admin.pengguna') }}"
                class="menu-link {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}">
                    <span>Manajemen Pengguna</span>
            </a>

            <a href="#"
               class="menu-link">
                <span>Laporan Pengguna</span>
            </a>

            <a href="#"
               class="menu-link">
                <span>Pengaturan</span>
            </a>

        </nav>

        <div class="sidebar-bottom">
            <button class="logout-button" onclick="logoutAdmin()">
                <span>Keluar dari akun</span>
            </button>
        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="admin-profile">
                Administrator
            </div>
        </header>

        <section class="page-content">
            @yield('content')
        </section>

    </main>

</div>

<script>
    async function logoutAdmin() {
        const token = sessionStorage.getItem('auth_token');

        try {
            if (token) {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            sessionStorage.removeItem('auth_token');
            sessionStorage.removeItem('user');
            window.location.href = '/login';
        }
    }
</script>

@stack('scripts')

</body>
</html>
