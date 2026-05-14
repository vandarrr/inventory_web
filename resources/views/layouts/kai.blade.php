<!DOCTYPE html>
<html lang="id">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- 🔥 TITLE DINAMIS -->
    <title>@yield('title', 'Inventory - PT Gema Bumi Arta')</title>

    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- 🔥 FAVICON (GANTI LOGO SENDIRI) -->
    <link rel="icon" href="{{ asset('images/elpigi.png') }}" type="image/png"/>

    <!-- Fonts -->
    <script src="{{ asset('template') }}/assets/js/plugin/webfont/webfont.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["{{ asset('template') }}/assets/css/fonts.min.css"],
        },
      });
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('template') }}/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('template') }}/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="{{ asset('template') }}/assets/css/kaiadmin.min.css" />

    <!-- 🔥 CUSTOM STYLE -->
    <style>
      body {
        background: #f4f6f9;
      }

      .page-header {
        margin-bottom: 20px;
      }

      .page-title {
        font-weight: 600;
        font-size: 20px;
      }

      .navbar-header {
        background: #ffffff !important;
      }

      .logo-header {
        background: #1e293b !important;
      }

      .footer {
        font-size: 13px;
        color: #666;
      }

      .card {
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      }
    </style>
  </head>

  <body>
    @include('sweetalert::alert')

    <div class="wrapper">

      <!-- 🔥 SIDEBAR -->
      <x-Sidebar />

      <div class="main-panel">

        <!-- 🔥 HEADER -->
        <div class="main-header">
          <div class="main-header-logo">

            <div class="logo-header" data-background-color="dark">
              <a href="/home" class="logo text-white fw-bold">
                Inventory LPG
              </a>

              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- 🔥 NAVBAR -->
          <nav class="navbar navbar-header navbar-expand-lg border-bottom">
            <div class="container-fluid">

              <ul class="navbar-nav ms-auto align-items-center">

                <!-- 🔥 USER -->
                <li class="nav-item dropdown">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown">
                    <div class="avatar-sm">
                      <img src="{{ asset('template') }}/assets/img/profile.jpg"
                        class="avatar-img rounded-circle"/>
                    </div>

                    <span class="ms-2 fw-semibold">
                      {{ auth()->user()->name }}
                    </span>
                  </a>

                  <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-2">
                      <strong>{{ auth()->user()->name }}</strong><br>
                      <small class="text-muted">{{ auth()->user()->email }}</small>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                      <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                         onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                        Logout
                      </a>

                      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                      </form>
                    </li>
                  </ul>
                </li>

              </ul>
            </div>
          </nav>
        </div>

        <!-- 🔥 CONTENT -->
        <div class="container">
          <div class="page-inner">

            <div class="page-header">
              <h4 class="page-title">
                @yield('page_title','Dashboard')
              </h4>
            </div>

            @yield('content')

          </div>
        </div>

        <!-- 🔥 FOOTER (SUDAH DI RAPIIKAN) -->
        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <div>
              © {{ date('Y') }} <strong>PT Gema Bumi Arta</strong>
            </div>

            <div>
              Inventory System PT Gema Bumi Arta Depok
            </div>
          </div>
        </footer>

      </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('template') }}/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/core/popper.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/core/bootstrap.min.js"></script>

    <script src="{{ asset('template') }}/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="{{ asset('template') }}/assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <script src="{{ asset('template') }}/assets/js/kaiadmin.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('script')

  </body>
</html>