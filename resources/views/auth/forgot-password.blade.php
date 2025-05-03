@extends('layouts.main')
@section('title')
    Forgot Password
@endsection

@section('content')
    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <div class="d-flex justify-content-center py-4">
                                            <a href="index.html" class="logo d-flex align-items-center w-auto">
                                                <img src="{{ asset('assets') }}/img/logo-smkn1-slawi.png" alt="">
                                                <span class="d-none d-lg-block">Inventory</span>
                                            </a>
                                        </div>
                                        <p class="text-center small">Forgot Password</p>
                                    </div>

                                    @error('login_error')
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <strong>Ooops!</strong> {{ $message }}
                                            <button type="button" class="btn-close" data-bsdismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @enderror

                                    <form class="row g-3 needs-validation" novalidate method="POST"
                                        action="{{ route('forgot-password') }}">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="yourEmail"
                                                value="{{ old('email') }}" required>
                                            <div class="invalid-feedback">Please enter your email.</div>
                                            @error($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @enderror

                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Submit</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">I have an account! <a
                                                    href="/">Login here</a></p>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            <div class="credits" style="font-size: x-small;">
                                <!-- All the links in the footer should remain intact. -->
                                <!-- You can delete the links only if you purchased the pro version. -->
                                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main>
@endsection
