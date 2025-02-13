@extends('layouts.app')

@section('content')
    <div class="login-container">
        <form method="POST" action="{{ route('login') }}" class="form">
            @csrf
            <h2>Login</h2>

            @if ($errors->any())
                <p class="error-message">{{ $errors->first() }}</p>
            @endif

            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email"
                   class="form-input @error('email') form-error @enderror"
                   placeholder="Enter your email"
                   required value="{{ old('email') }}">



            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password"
                   class="form-input @error('email') form-error @enderror"
                   placeholder="Enter your password" required>

            <button type="submit" class="form-submit">Login</button>
            <!-- Enlace de registro -->
            <p class="mt-4 text-center">
                <span>Don't have an account yet?</span>
                <a href="{{ route('register') }}" style="color: #0039f5;" class="hover:text-blue-700">Click here</a>

            </p>
        </form>
    </div>
@endsection
