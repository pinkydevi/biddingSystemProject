<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }}</title>
    <link rel="shortcut icon" href={{ asset('favicon.svg') }} type="image/x-icon">

    {{-- bootstrap cdn --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    {{-- fontawesome cdn --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">

    {{-- bootstrap js cdn --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    {{-- SweetAlert CDN --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    {{-- Toastr CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- Custom --}}
    <link rel="stylesheet" href={{ asset('css/style.css') }}>
    <script src={{ asset('js/app.js') }}></script>
</head>

<body>
    @csrf
    <header>
        <div class="navbar navbar-light bg-light">
            <a class="navbar-brand" href={{ route('home') }}>{{ $app_name }}</a>
            <div>
                @auth
                    <a href={{ route('transactions') }} class="navbar-brand btn btn-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-wallet2" viewBox="0 0 16 16">
                            <path
                                d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z" />
                        </svg>
                        My Wallet: $<span id="text-balance-money">{{ number_format(auth()->user()->balance_money) }}</span>
                        <span class="opac">
                            $<span id="text-reserved-money">{{ number_format(auth()->user()->reserved_money) }}</span>
                        </span>
                        <a href={{ route('my-auctions') }} class="navbar-brand btn btn-light">My Auctions</a>
                        <a href={{ route('change-password') }} class="navbar-brand btn btn-light">Change Password</a>
                        <a href={{ route('logout') }} class="navbar-brand btn btn-light">Logout</a>
                    </a>
                @endauth
                @guest
                    <a href={{ route('login') }} class="navbar-brand btn btn-light">Login</a>
                    <a href={{ route('register') }} class="navbar-brand btn btn-light">Register</a>
                @endguest
            </div>
        </div>
    </header>
    {!! $page !!}
</body>

</html>
