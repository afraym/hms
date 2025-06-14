<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
  <title>
     {{ __('routes.' . Route::currentRouteName()) }} | مستشفى الباطنة التخصصي 
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/inter.css') }}" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="{{ asset('assets/css/42d5adcbca.js') }}" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="{{ asset('assets/css/Material+Icons+Round.css') }}" rel="stylesheet">
  <!-- Material Symbols -->
  <link href="{{ asset('assets/css/Material+Symbols+Rounded.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />
{{-- <script src="{{ asset('assets/themes/fa5/theme.js')}}" type="text/javascript"></script> --}}
    {{-- <script src="{{ asset('assets/themes/explorer-fa5/theme.js')}}" type="text/javascript"></script> --}}
<!-- the fileinput plugin styling CSS file -->
<link href="{{ asset('assets/css/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />
 
<!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
<link rel="stylesheet" href="{{ asset('assets/css/fileinput-rtl.min.css')}}"> 
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="g-sidenav-show rtl bg-gray-200">
  <style>
    .sidenav .navbar-brand {
      padding: 0;
    }
    .navbar-vertical .navbar-brand>img, .navbar-vertical .navbar-brand-img {
      max-height: 4.8rem;
    }
    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('{{ asset('assets/img/background.jpg') }}') no-repeat center center fixed;
      background-size: contain;
      opacity: 0.2; /* Adjust transparency */
      z-index: -1;
    }
    /* Style the first <td> in each row */
    table tbody tr td:first-child {
      padding: 0.5rem 1.5rem;
    }

    .hover-image {
    display: none;
    position: absolute;
    top: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 14%;
    z-index: 10;
}

a:hover .hover-image {
    display: block;
}
  </style>