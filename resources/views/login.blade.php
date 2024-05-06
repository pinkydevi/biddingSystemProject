<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-5" style="margin-top: 150px">
            <form action="#" class="form-group">
                <h1 class="display-3 text-center">Login</h1>
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email"/>
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" />
                <br />
                <button class="btn btn-success form-control" id="btn-login">
                    Login
                </button>
                <br />
                <div id="error-text">
                </div>
                <div class="text-center mt-4">
                    <a href={{ route('forgot-password') }} title="ForgotPass"><h5>Forgot Password?</h5></a>
                </div>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>
