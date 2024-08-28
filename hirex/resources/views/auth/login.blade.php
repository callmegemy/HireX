<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary: #5289b5;
            --secondary: #afd8f2;
            --light: #ffffff; /* Set light to white for background */
            --dark: #1f3541;
            --hover-color: #417ba1;
        }

        body {
            
            background-color: var(--light); /* White background */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container{
           margin-top: 100px;
        }

        .card {
            background-color: var(--light); 
            border: none; 
            box-shadow: 0 0 15px rgba(31, 53, 65, 0.5); 
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            margin:auto;
        }

        .card-header {
            background-color: var(--primary);
            height: 50px;
            color: var(--light);
            border-radius: 15px 15px 0 0   !important ; /* Rounded corners for the top of the card */
            border: none; /* Remove header border */
            text-align: center;
            font-weight:bold; 
        
        }

        .form-control {
            background-color: var(--light);
            border: none; /* Remove input borders */
            border-radius: 25px; /* Rounded corners for inputs */
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 5px rgba(31, 53, 65, 0.5); /* Focus shadow with color #1f3541 */
            outline: none;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--light);
            border-radius: 25px; /* Rounded corners for the button */
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover-color); /* Darken button on hover */
        }

        .btn-link {
            color: var(--dark);
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: var(--primary);
        }

        .input-group-text {
            background-color: var(--primary);
            color: var(--light);
            border: none; /* Remove input group text border */
            border-radius: 25px 0 0 25px; /* Rounded corners for the left side of input group */
        }

    </style>
</head>
<body>
    <!-- Include Navbar -->
    @include('layouts.nav')

    <div class="container">
        <div class="card">
            <div class="card-header">
                {{ __('Login') }}
            </div>

            <div class="card-body" style="color: var(--dark);">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="col-form-label" style="color: var(--dark);">{{ __('Email Address') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="col-form-label" style="color: var(--dark);">{{ __('Password') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember" style="color: var(--dark);">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div class="mb-0 text-center">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>