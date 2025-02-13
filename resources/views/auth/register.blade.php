@extends('layouts.app')

@section('content')
    <div class="login-container">
        <form method="POST" action="{{ route('register') }}" class="form" id="register-form">
            @csrf
            <h2>Sign up</h2>

            @if ($errors->any())
                <p class="error-message">{{ $errors->first() }}</p>
            @endif

            <label for="name" class="form-label">Username</label>
            <input type="text" name="name" id="name"
                   class="form-input @error('name') form-error @enderror"
                   placeholder="Enter your username"
                   required value="{{ old('name') }}">


            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email"
                   class="form-input @error('email') form-error @enderror"
                   placeholder="Enter your email"
                   required value="{{ old('email') }}">


            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password"
                   class="form-input @error('password') form-error @enderror"
                   placeholder="Enter your password" required>


            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-input @error('password') form-error @enderror"
                   placeholder="Confirm your password" required>

            <button type="submit" class="form-submit">Register</button>
            <button type="reset" class="form-reset">Clear</button>
        </form>
    </div>
@endsection
