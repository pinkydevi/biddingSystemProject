<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-8">
            {{-- <h2 class="display-2 text-center">{{ $app_name }}</h2> --}}
            <div class="input-group">
                <input class="form-control" type="text" id="keyword" name="searchAuction" placeholder="Search..." />
                <div class="input-group-append">
                    <div class="dropdown">
                        <button class="btn btn-light border border-gray dropdown-toggle" type="button"
                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="border-radius: 0px">
                            Order By : Earliest
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="start_time desc">
                                Earliest
                            </a>
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="start_time asc">
                                Oldest
                            </a>
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="title asc">
                                Name Asc
                            </a>
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="title desc">
                                Name Desc
                            </a>
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="current_price asc">
                                Price (Lowest to Highest)
                            </a>
                            <a class="dropdown-item" id="btn-order-by-auctions" style="padding-right: 80px"
                                href="#" data-order-by="current_price desc">
                                Price (Highest to Lowest)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col"></div>
    </div>
    <div class="container" id="show-no-active-auctions">
        <div class="row">
            <div class="col"></div>
            <div class="col-8">
                <div class="jumbotron text-center" style="margin-top: 50px">
                    <h1>It appears there are no active auctions...</h1>
                    @guest
                        <h4>Login or register to start one!</h4>
                    @endguest
                    @auth
                        <div class="text-center">
                            <h3>Create one yourself!</h3>
                            <br />
                            <a href="#" class="btn btn-primary">
                                Create Auction
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <div class="row" id="auctions">
        {{-- <div class="col-md-4">
            <div class="card mt-5">
                <img class="card-img-top" width="300" height="300" src="./upload/1619878105_bike.jpeg"
                    alt="Auction image" style="border-radius-top: 10px" />
                <div class="card-body">
                    <h5 class="card-title">Name</h5>
                    <p class="card-text">Desc</p>
                    <p>Start date: Time</p>
                    <p>Minimum price: $ 100.000</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mt-5">
                <img class="card-img-top" width="300" height="300" src="./upload/1619878105_bike.jpeg"
                    alt="Auction image" style="border-radius-top: 10px" />
                <div class="card-body">
                    <h5 class="card-title">Name</h5>
                    <p class="card-text">Desc</p>
                    <p>Start date: Time</p>
                    <p>Minimum price: $ 100.000</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mt-5">
                <img class="card-img-top" width="300" height="300" src="./upload/1619878105_bike.jpeg"
                    alt="Auction image" style="border-radius-top: 10px" />
                <div class="card-body">
                    <h5 class="card-title">Name</h5>
                    <p class="card-text">Desc</p>
                    <p>Start date: Time</p>
                    <p>Minimum price: $ 100.000</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mt-5">
                <img class="card-img-top" width="300" height="300" src="./upload/1619878105_bike.jpeg"
                    alt="Auction image" style="border-radius-top: 10px" />
                <div class="card-body">
                    <h5 class="card-title">Name</h5>
                    <p class="card-text">Desc</p>
                    <p>Start date: Time</p>
                    <p>Minimum price: $ 100.000</p>
                </div>
            </div>
        </div> --}}


    </div>
    <div class="container mb-5"></div>
</div>
