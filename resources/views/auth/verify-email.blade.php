@extends('layouts.layout')
@include('includes.navbar')
@section('title', 'Verify Your Email')
<!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
@section('content')
    <div class="container">
        <div style="margin-top: 80px; margin-bottom: 30px;">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">Verify Your Email Address</div>
                        <div class="card-body">
                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success">
                                    A new verification link has been sent to your email address.
                                </div>
                            @endif
                            <p>
                                Before proceeding, please check your email for a verification link.
                                If you did not receive the email,
                            </p>
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Click here to request another</button>.
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection