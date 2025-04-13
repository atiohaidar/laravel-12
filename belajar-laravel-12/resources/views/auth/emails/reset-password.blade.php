@extends('layouts.app')

@section('title', 'Email Notification')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Reset Password Notification</div>

                <div class="card-body">
                    <p>You are receiving this email because we received a password reset request for your account.</p>
                    
                    <div class="d-grid gap-2 col-6 mx-auto my-4">
                        <a href="{{ $resetUrl }}" class="btn btn-primary">
                            Reset Password
                        </a>
                    </div>

                    <p>This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.</p>
                    
                    <p>If you did not request a password reset, no further action is required.</p>
                    
                    <hr>
                    
                    <p class="text-secondary small">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                    <p class="text-secondary small">{{ $resetUrl }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection