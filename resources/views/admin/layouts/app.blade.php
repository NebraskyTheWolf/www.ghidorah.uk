<html lang="{{ config('app.locale') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>@yield('title') - KibbleLands</title>

    <link rel="icon" type="image/png" href="{{ url('/img/favicon.png') }}" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/vendors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/core/menu/menu-types/vertical-menu-modern.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/fonts/simple-line-icons/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/core/colors/palette-gradient.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ url('/admin-assets/css/admin.css') }}">
    @yield('style')
</head>
<body data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar">
    @include('admin.layouts.header')

    <div class="app-content content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- BEGIN VENDOR JS-->
    <script src="{{ url('/admin-assets/vendors/js/vendors.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ url('/admin-assets/vendors/js/charts/raphael-min.js') }}" type="text/javascript"></script>
    <script src="{{ url('/admin-assets/vendors/js/charts/morris.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('/admin-assets/vendors/js/extensions/unslider-min.js') }}" type="text/javascript"></script>
    <script src="{{ url('/admin-assets/vendors/js/timeline/horizontal-timeline.js') }}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN STACK JS-->
    <script src="{{ url('/admin-assets/js/core/app-menu.js') }}" type="text/javascript"></script>
    <script src="{{ url('/admin-assets/js/core/app.js') }}" type="text/javascript"></script>
    <script src="{{ url('/admin-assets/js/scripts/customizer.js') }}" type="text/javascript"></script>
    <!-- END STACK JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script type="text/javascript">
        var localization = {
            loading: "@lang('form.loading')",
            error: {
                title: "@lang('form.error.title')",
                internal: "@lang('form.error.internal')",
                notfound: "@lang('form.error.notfound')",
                forbidden: "@lang('form.error.forbidden')",
                badrequest: "@lang('form.error.badrequest')",
                methodnotallowed: "@lang('form.error.methodnotallowed')"
            },
            success: {
                title: "@lang('form.success.title')"
            },
            class: {
                error: 'alert bg-danger',
                success: 'alert bg-success'
            }
        }
    </script>
    <script type="text/javascript" src="{{ url('/js/app.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/form.js') }}"></script>
    @yield('script')
</body>