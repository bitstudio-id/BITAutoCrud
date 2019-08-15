<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/ico" href="/img/favicon.ico" sizes="any" />
    <title>{{env('APP_NAME')}}</title>
    @include('bit.layout.css')
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
@include('bit.layout.header')
<div class="app-body">
    @include('bit.layout.sidebar')
    <main class="main">
        @include('bit.layout.breadcrumb')
        <div class="container-fluid">
            <div class="animated fadeIn">
                @yield('content')
            </div>
        </div>
    </main>
    @include('bit.layout.aside')
</div>
@include('bit.layout.footer')
@include('bit.layout.js')
</body>
</html>
