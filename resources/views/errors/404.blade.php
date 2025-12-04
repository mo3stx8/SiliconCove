{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.layout') {{-- Or use layout you want --}}
<!-- website icon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
@section('title', 'Page Not Found')

@section('content')
<div class="text-center py-5">
    <h1 class="display-1">404</h1>
    <h3 class="mb-3">Oops! Page Not Found</h3>
    <p class="text-muted">The page you're looking for doesn't exist or has been moved.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go Home</a>
</div>
@endsection
