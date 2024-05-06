<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10">
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link btn active activeTab" id="btn-change-tab-my-auctions" data-tab="all">All Auctions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn" id="btn-change-tab-my-auctions" data-tab="active">Active Auctions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn" id="btn-change-tab-my-auctions" data-tab="closed">Closed Auctions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn" id="btn-change-tab-my-auctions" data-tab="won">Won Auctions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn" id="btn-change-tab-my-auctions" data-tab="bidded">Bidded Auctions</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item" style="float: right">
                        <a href={{ route('create-auction') }} class="btn btn-primary">Create Auction</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col"></div>
    </div>
    <div class="row mt-3">
        <div class="col"></div>
        <div class="col-8" id="list-card-my-auctions">
            <div class="jumbotron row mt-3">
				<h4 class="display-4 text-center">There are currently no auctions.</h4>
			</div>
            
            {{-- <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mt-2">
                        <strong>@{{ auction.name }}</strong>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">@{{ auction.description }}</p>
                    <img class="border border-gray border-rounded" style="float: right; position: relative" src="'upload\\' + auction.photo_url" alt="Auction image" width="200" height="150"/>
                    Start date: @{{ auction.start }}<br>
                    <span class="border border-danger rounded">End date: @{{ auction.end }}</span>
                    <br />
                    Minumum price: @{{ auction.min_price }}
                    <br />
                    <p>Last bid: @{{ auction.last_bid_price }}</p>

                    <button type="button" class="btn btn-danger">Close Auction</button>
                </div>
            </div> --}}
            
        </div>
        <div class="col"></div>
    </div>
</div>