<!DOCTYPE html>
<html lang="id">

    @include('layouts.style')

    <body>
        <script>
            (function () {
                try {
                    if (window.localStorage.getItem('sikatan.sidebar.collapsed') === '1'
                        && window.matchMedia('(min-width: 992px)').matches) {
                        document.body.classList.add('sidebar-collapsed');
                    }
                    document.body.classList.add('sidebar-no-transition');
                    window.addEventListener('DOMContentLoaded', function () {
                        requestAnimationFrame(function () {
                            requestAnimationFrame(function () {
                                document.body.classList.remove('sidebar-no-transition');
                            });
                        });
                    });
                } catch (e) {}
            })();
        </script>
        @include('layouts.sidebar')

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="app-main">
            @include('layouts.navbar')

            <main class="app-content">
                @yield('content')
            </main>
        </div>
        @include('layouts.script')
        @stack('scripts')
    </body>

</html>
