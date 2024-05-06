<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-6">
            <div class="form-group align-midle" style="margin-top: 100px;">
                <h2 class="h2 text-center">Register</h2>
                <form action="#" class="form-group">
                    <label for="Email">Email</label>
                    <input class="form-control" type="email" id="email" required>
                    <label for="password">Password</label>
                    <input class="form-control" type="password" name="password" id="password" required>
                    <label for="passwordConfirmation">Confirm Password</label>
                    <input class="form-control" type="password" id="password_confirmation" required>
                    <button class="btn btn-success form-control mt-4" type="button" id="btn-register">Register</button>
                </form>
                <div class="text-center mt-4">
                    <a href={{ route('forgot-password') }} title="ForgotPass"><h5>Forgot Password?</h5></a>
                </div>
            </div>
        </div>
        <div class="col"></div>
    </div>
</div>
