<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dayflow')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #9182BD;
            --primary-dark: #7666A5;
            --primary-light: #EAE6F5;
            --background: #F8F7FC;
            --text: #373247;
            --muted: #9691A5;
            --border: #ECEAF2;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--background);
            color: var(--text);
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding: 32px;
            display: flex;
            flex-direction: column;
        }

        .page-footer {
            border-top: 1px solid var(--border);
        }

        .mobile-nav-header {
            display: none;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 28px 20px;
            z-index: 1000;
        }

        .sidebar-brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .desktop-nav-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #fff;
            color: var(--primary-dark);
            cursor: pointer;
        }

        .desktop-nav-toggle:hover {
            background: var(--primary-light);
        }

        .desktop-nav-toggle:focus-visible {
            outline: 3px solid rgba(145, 130, 189, .35);
            outline-offset: 2px;
        }

        .sidebar-brand-mark {
            display: none;
        }

        body.sidebar-collapsed .sidebar {
            width: 78px;
            padding-inline: 12px;
        }

        body.sidebar-collapsed .sidebar-brand-row {
            flex-direction: column;
        }

        body.sidebar-collapsed .sidebar-brand-label,
        body.sidebar-collapsed .sidebar > p,
        body.sidebar-collapsed .sidebar .nav-label,
        body.sidebar-collapsed .sidebar .user-details,
        body.sidebar-collapsed .sidebar .logout-label {display: none;}

        body.sidebar-collapsed .sidebar-brand-mark {
            display: inline;
            font-size: 23px;
        }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding-inline: 10px;
        }

        body.sidebar-collapsed .sidebar .mt-5 {margin-top: 1.5rem !important;}

        body.sidebar-collapsed .main-content {margin-left: 78px;}

        .brand {
            font-size: 23px;
            font-weight: 800;
            color: var(--text);
            text-decoration: none;
        }

        .brand span {color: var(--primary);}

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            padding: 12px 14px;
            margin-bottom: 7px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-dark);
            background: var(--primary-light);
        }

        .nav-link i {font-size: 18px;}

        .page-title {
            font-size: 25px;
            font-weight: 700;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 14px;
        }

        .card-custom {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            height: 100%;
        }

        .stat-number {
            font-size: 27px;
            font-weight: 700;
        }

        .stat-icon {
            width: 43px;
            height: 43px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 20px;
        }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 18px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: white;
        }

        .form-control,
        .form-select {
            border-color: var(--border);
            border-radius: 10px;
            padding: 11px 13px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(145, 130, 189, .12);
        }

        .table {vertical-align: middle;}

        .table th {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            border-bottom-color: var(--border);
            padding: 14px;
        }

        .table td {
            padding: 16px 14px;
            font-size: 13px;
            border-bottom-color: var(--border);
        }

        .badge-soft {
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-high {
            background: #FCE8E8;
            color: #C65D5D;
        }

        .badge-medium {
            background: #FFF1D8;
            color: #B9842F;
        }

        .badge-low {
            background: #E8F2E8;
            color: #5D9665;
        }

        .badge-status {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .progress {
            height: 9px;
            background: #EEEAF5;
            border-radius: 20px;
        }

        .progress-bar {
            background: var(--primary);
            border-radius: 20px;
        }

        @media (max-width: 991px) {
            .mobile-nav-header {
                position: sticky;
                top: 0;
                z-index: 1001;
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-height: 64px;
                padding: 10px 20px;
                background: #fff;
                border-bottom: 1px solid var(--border);
            }

            .mobile-nav-header .brand {
                font-size: 21px;
            }

            .mobile-nav-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border: 1px solid var(--border);
                border-radius: 10px;
                background: #fff;
                color: var(--primary-dark);
                font-size: 23px;
            }

            .mobile-nav-toggle:focus-visible {
                outline: 3px solid rgba(145, 130, 189, .35);
                outline-offset: 2px;
            }

            .desktop-nav-toggle {display: none;}

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: none;
                padding: 20px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .sidebar.is-open {display: block;}

            body.sidebar-collapsed .sidebar {
                width: 100%;
                padding: 20px;
            }

            body.sidebar-collapsed .sidebar-brand-row {flex-direction: row;}
            body.sidebar-collapsed .sidebar-brand-label,
            body.sidebar-collapsed .sidebar > p,
            body.sidebar-collapsed .sidebar .nav-label,
            body.sidebar-collapsed .sidebar .user-details,
            body.sidebar-collapsed .sidebar .logout-label {display: initial;}
            body.sidebar-collapsed .sidebar-brand-mark {display: none;}
            body.sidebar-collapsed .sidebar .nav-link {
                justify-content: flex-start;
                padding: 12px 14px;
            }

            .sidebar > .brand {display: none;}
            .sidebar-brand-row {display: none;}
            .sidebar-nav {
                display: flex;
                flex-direction: column;
                gap: 4px;
                margin-top: 0;
            }

            .nav-link {
                white-space: nowrap;
                margin-bottom: 0;
            }

            .sidebar > p {margin-top: 0 !important;}

            .main-content {
                margin-left: 0;
                padding: 22px 16px;
            }

            body.sidebar-collapsed .main-content {margin-left: 0;}
        }

        @media (max-width: 576px) {
            .mobile-nav-header {
                padding-inline: 16px;
            }
            .page-title {font-size: 21px;}
            .card-custom {padding: 17px;}
        }
    </style>
    @stack('styles')
</head>

<body>

    @include('partials.flash')
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
