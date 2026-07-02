{{--
    Dashboard Wrapper — Medivest VENTRILOC Design System
    
    Lightweight wrapper used by all dashboard pages.
    The sidebar and layout chrome are in layouts/app.blade.php.
    Each dashboard page extends this wrapper.
--}}

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @yield('dashboard-content')
@endsection
