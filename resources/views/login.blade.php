@extends('layouts.app')

@section('title', 'Login | Dayflow')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">

    <div class="card-custom shadow-sm"
         style="width: 100%; max-width: 430px; padding: 38px;">

        <div class="text-center mb-4">
            <a href="/" class="brand">
                Day<span>flow</span>
            </a>

            <div class="mt-4">
                <h3 class="fw-bold">Welcome to Dayflow</h3>
                <p class="page-subtitle">
                    Let's make today productive.
                </p>
            </div>
        </div>

        @include('partials.flash')

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold"
                       style="font-size: 13px;">
                    Username
                </label>

                <input type="text"
                       name="username"
                       class="form-control"
                       placeholder="Enter your username"
                       value="{{ old('username') }}"
                       maxlength="30"
                       required>
            </div>

            <button type="submit"
                    class="btn-primary-custom w-100 mt-2">
                Get Started
                <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>

        <p class="text-center text-secondary mt-4 mb-0"
           style="font-size: 11px;">
            Your personal space to organize every day.
        </p>

    </div>

</div>

@endsection
