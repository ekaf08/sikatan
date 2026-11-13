<!DOCTYPE html>
<html lang="id">

    @include('layouts.style')

    <body>
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
