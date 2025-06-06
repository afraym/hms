<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16x16.png">
<link rel="manifest" href="/assets/img/site.webmanifest">
  <title>
     {{ __('routes.' . Route::currentRouteName()) }} | مستشفى الباطنة التخصصي 
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="/assets/fonts/inter.css" />
  <!-- Nucleo Icons -->
  <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="/assets/css//42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="/assets/css/Material+Icons+Round.css" rel="stylesheet">
  <!-- Material Symbols -->
  <link href="/assets/css/Material+Symbols+Rounded.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link rel="stylesheet" href="/assets/css/custom.css">
  <link id="pagestyle" href="/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="g-sidenav-show rtl bg-gray-200">
  <style>
    .sidenav .navbar-brand {
      padding:0;
    }
    .navbar-vertical .navbar-brand>img, .navbar-vertical .navbar-brand-img {
    max-height: 4.8rem;
    }
    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('/assets/img/background.jpg') no-repeat center center fixed;
        background-size: contain;
        opacity: 0.2; /* Adjust transparency */
        z-index: -1;
    }
    /* Style the first <td> in each row */
    table tbody tr td:first-child {
      padding: 0.5rem 1.5rem;

    }
  </style>