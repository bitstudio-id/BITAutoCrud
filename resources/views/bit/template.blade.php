<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/ico" href="/img/favicon.ico" sizes="any" />
    <title>{{env('APP_NAME')}}</title>
    @include('bit.layouts.css')
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
@include('bit.layouts.header')
<div class="app-body">
    @include('bit.layouts.sidebar')
    <main class="main">
        @include('bit.layouts.breadcrumb')
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div id="ui-view">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</div>
@include('bit.layouts.footer')
@include('bit.layouts.js')
<script id="__bs_script__">//<![CDATA[
    document.write("<script async src='http://HOST:3000/browser-sync/browser-sync-client.js?v=2.26.7'><\/script>".replace("HOST", location.hostname));
    //]]></script>
</body>
</html>
