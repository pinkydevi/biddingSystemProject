$(document).ready(function () {
    const base_url = window.location.origin;

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "1000",
        preventDuplicates: true,
        extendedTimeOut: "1000",
        maxOpened: 3,
        maxVisible: 3,
    };

    if (window.location.pathname == "/auctions/view") {
        loadMyAuctions();
    }

    if (window.location.pathname == "/") {
        loadAuctions();
    }

    function convertPrice(price) {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function loadAuctions(orderBy = "start_time desc", keyword = "") {
        $.ajax({
            url: base_url + "/api/auctions",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                orderBy: orderBy,
                keyword: keyword,
            },
            success: function (data) {
                $("#auctions").html("");
                if (data.data.length == 0) {
                    $("#show-no-active-auctions").removeClass("d-none");
                    return;
                } else {
                    $("#show-no-active-auctions").addClass("d-none");
                }
                data.data.forEach(function (auction) {
                    const html = `
                    <div class="col-md-4" data-auction-id="${
                        auction.id
                    }" id="card-auction">
                        <div class="card mt-5 ${
                            auction.belongToMe ? "owner" : ""
                        } ${
                        auction.winning ? "winning" : ""
                    }" style="border-radius: 10px;">
                            <img class="card-img-top" width="300" height="300" src="${
                                base_url + "/" + auction.image
                            }" alt="Auction image" style="border-top-left-radius: 10px;border-top-right-radius: 10px;" />
                            <div class="card-body">
                                <h5 class="card-title custom-text-truncate">${
                                    auction.title
                                }</h5>
                                <p class="card-text custom-text-truncate">${
                                    auction.description
                                }</p>
                                <p>Start date: ${auction.start_time}</p>
                                <p>Minimum price: $${convertPrice(
                                    auction.min_price
                                )}</p>
                                <p id="current_price">Last bid price: $${convertPrice(
                                    auction.current_price
                                )}</p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Bid price: $ ${
                                        auction.current_price
                                    }"/>
                                    <div class="input-group-append form-control btn btn-outline-secondary" style="display: flex;justify-content: center;align-items: center;border-color: #a0a4a9;" id="btn-bidding" data-auction-id="${
                                        auction.id
                                    }">Bid</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    $("#auctions").append(html);
                });
            },
        });
    }

    $(document).on("click", "#btn-order-by-auctions", function (event) {
        event.preventDefault();

        const orderBy = $(this).data("order-by");
        const keyword = $("#keyword").val();
        const text = $(this).text();
        $("#dropdownMenuButton").text("Order By : " + text);

        loadAuctions(orderBy, keyword);
    });

    $(document).on("keyup", "#keyword", function (event) {
        event.preventDefault();

        const orderBy = $("#btn-order-by-auctions").data("order-by");
        const keyword = $(this).val();

        loadAuctions(orderBy, keyword);
    });

    $(document).on("click", "#btn-login", function (event) {
        event.preventDefault();

        const email = $("#email").val();
        const password = $("#password").val();

        toastr.info("Loggin in...");

        $.ajax({
            url: base_url + "/api/login",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                email: email,
                password: password,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    $("#error-text").html("");
                    setTimeout(function () {
                        window.location.href = base_url;
                    }, 1000);
                } else {
                    toastr.error(data.message);
                    $("#error-text").html(`
                <div class="mt-3 alert alert-danger">
                    Error: ${data.message}
                </div>
                `);
                }
            },
            error: function (data) {
                // do something
            },
        });
    });

    $(document).on("click", "#btn-bidding", function (event) {
        event.preventDefault();

        const auctionId = $(this).data("auction-id");
        const bidPrice = $(this).parent().find("input").val();

        if (bidPrice == "") {
            return;
        }

        $(this).parent().find("input").val("");

        toastr.info("Bidding...");
        $.ajax({
            url: base_url + "/api/auctions/bidding",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                auction_id: auctionId,
                bid: bidPrice,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    $(
                        `#card-auction[data-auction-id="${auctionId}"]>.card`
                    ).addClass("winning");
                    $(`#card-auction[data-auction-id="${auctionId}"]`)
                        .find("#current_price")
                        .html("Last bid price: $" + bidPrice);
                    changeMoney(
                        data.data.balance_money,
                        data.data.reserved_money
                    );
                } else {
                    toastr.error(data.message);
                }
            },
        });
    });

    $(document).on("click", "#btn-deposit", function (event) {
        event.preventDefault();

        const money = parseInt($(this).parent().find("input").val());

        if (money == "" || money <= 0 || isNaN(money)) {
            return;
        }

        $(this).parent().find("input").val("");

        toastr.info("Depositing...");
        $.ajax({
            url: base_url + "/api/transaction",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                money: money,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    changeMoney(
                        data.data.balance_money,
                        data.data.reserved_money
                    );
                } else {
                    toastr.error(data.message);
                }
            },
        });
    });

    $(document).on("click", "#btn-withdraw", function (event) {
        event.preventDefault();

        const money = parseInt($(this).parent().find("input").val());

        if (money == "" || money <= 0 || isNaN(money)) {
            return;
        }

        $(this).parent().find("input").val("");

        toastr.info("Withdraw...");
        $.ajax({
            url: base_url + "/api/transaction",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                money: -1 * money,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    changeMoney(
                        data.data.balance_money,
                        data.data.reserved_money
                    );
                } else {
                    toastr.error(data.message);
                }
            },
        });
    });

    function changeMoney(balance, reserved = 0) {
        $("#balance").html(convertPrice(balance));
        $("#text-balance-money").html(convertPrice(balance));
        $("#text-reserved-money").html(convertPrice(reserved));
    }

    function loadMyAuctions(tab = "all") {
        $.ajax({
            url: base_url + "/api/auctions/my-auctions",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                tab: tab,
            },
            success: function (data) {
                $("#list-card-my-auctions").html("");
                if (data.data.length == 0) {
                    $("#list-card-my-auctions").html(`
                    <div class="jumbotron row mt-3">
                        <h4 class="display-4 text-center">There are currently no auctions.</h4>
                    </div>
                    `);
                    return;
                }
                data.data.forEach(function (auction) {
                    const html = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mt-2">
                                <strong class="custom-text-truncate">${
                                    auction.title
                                }</strong>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text custom-text-truncate">${
                                auction.description
                            }</p>
                            <img class="border border-gray border-rounded" style="float: right; position: relative" src="${
                                base_url + "/" + auction.image
                            }" alt="Auction image" width="200" height="150"/>
                            Start date: ${auction.start_time}<br>
                            ${
                                auction.status == "ended"
                                    ? `<span class="border border-danger rounded">End date: ${auction.updated}</span><br />`
                                    : ""
                            }
                            Minimum price: $${auction.min_price}
                            <br />
                            <p>Last bid: $${auction.current_price}</p>
                            ${
                                auction.status == "active" && auction.belongToMe
                                    ? '<button type="button" class="btn btn-danger" id="btn-close-auction" data-auction-id="' +
                                      auction.id +
                                      '">Close Auction</button>'
                                    : ""
                            }
                        </div>
                    </div>
                    `;
                    $("#list-card-my-auctions").append(html);
                });
            },
        });
    }

    $(document).on("click", "#btn-change-tab-my-auctions", function (event) {
        event.preventDefault();

        const tab = $(this).data("tab");
        loadMyAuctions(tab);

        $("a#btn-change-tab-my-auctions").removeClass("active");
        $("a#btn-change-tab-my-auctions").removeClass("activeTab");

        $(this).addClass("active");
        $(this).addClass("activeTab");
    });

    $(document).on("click", "#btn-close-auction", function (event) {
        event.preventDefault();

        var auctionId = $(this).data("auction-id");
        console.log(auctionId);

        swal({
            title: "Confirm",
            text: "Are you sure to close this auction?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {
                toastr.info("Closing auction...");
                $.ajax({
                    url: base_url + "/api/auctions/close",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                    },
                    data: {
                        auction_id: auctionId,
                    },
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data.message);

                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                });
            }
        });
    });

    $(document).on("change", "#customFile", function (event) {
        event.preventDefault();

        const file = $(this).prop("files")[0];

        if (file == undefined) {
            $("label.custom-file-label").html("Choose item photo");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            filename = file.name;
            $("label.custom-file-label").html(filename);
        };
        reader.readAsDataURL(file);
    });

    $(document).on("click", "#btn-create-auction", function (event) {
        event.preventDefault();

        const title = $("#title").val();
        const description = $("#description").val();
        const minPrice = $("#min_price").val();
        const image = $("#customFile").prop("files")[0];

        if (
            title == "" ||
            description == "" ||
            minPrice == "" ||
            image == undefined
        ) {
            toastr.error("Please fill all fields");
            return;
        }

        var formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);
        formData.append("min_price", minPrice);
        formData.append("image", image);

        toastr.info("Creating auction...");
        $.ajax({
            url: base_url + "/api/auctions/create",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);

                    setTimeout(function () {
                        window.location.href = base_url + "/auctions/view";
                    }, 1000);
                    return;
                }
                toastr.error(data.message);
            },
            error: function (data) {
                // do something
            },
        });
    });

    $(document).on("click", "#btn-register", function (event) {
        event.preventDefault();

        const email = $("#email").val();
        const password = $("#password").val();
        const password_confirmation = $("#password_confirmation").val();

        if (email == "" || password == "" || password_confirmation == "") {
            toastr.error("Please fill all fields");
            return;
        }

        if (password != password_confirmation) {
            toastr.error("Password and password confirmation do not match");
            return;
        }

        toastr.info("Registering...");

        $.ajax({
            url: base_url + "/api/register",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                email: email,
                password: password,
                password_confirmation: password_confirmation,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    setTimeout(function () {
                        window.location.href = base_url;
                    }, 1000);
                    return;
                }
                toastr.error(data.message);
            },
            error: function (data) {
                // do something
            },
        });
    });

    $(document).on("click", "#btn-change-password", function (event) {
        event.preventDefault();

        const oldPassword = $("#old_password").val();
        const newPassword = $("#new_password").val();
        const newPassword_confirmation = $("#newPassword_confirmation").val();

        if (
            oldPassword == "" ||
            newPassword == "" ||
            newPassword_confirmation == ""
        ) {
            toastr.error("Please fill all fields");
            return;
        }

        if (newPassword != newPassword_confirmation) {
            toastr.error(
                "New password and new password confirmation do not match"
            );
            return;
        }

        toastr.info("Changing password...");
        $.ajax({
            url: base_url + "/api/change-password",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                oldPassword: oldPassword,
                newPassword: newPassword,
                newPassword_confirmation: newPassword_confirmation,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    $("#old_password").val("");
                    $("#new_password").val("");
                    $("#newPassword_confirmation").val("");
                    return;
                }
                toastr.error(data.message);
            },
            error: function (data) {
                // do something
            },
        });
    });

    $(document).on("click", "#btn-send-code", function (event) {
        event.preventDefault();

        const email = $("#email").val();

        if (email == "") {
            toastr.error("Please enter email");
            return;
        }

        toastr.info("Sending code...");

        $.ajax({
            url: base_url + "/api/forgot-password/send-code",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                email: email,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    return;
                }
                toastr.error(data.message);
            },
            error: function (data) {
                // do something
            },
        });
    });

    $(document).on("click", "#btn-reset-password", function (event) {
        event.preventDefault();

        const email = $("#email").val();
        const code = $("#code").val();

        if (email == "") {
            toastr.error("Please enter email");
            return;
        }

        if (code == "") {
            toastr.error("Please enter code");
            return;
        }

        toastr.info("Sending code...");

        $.ajax({
            url: base_url + "/api/forgot-password/reset",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            data: {
                email: email,
                code: code,
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    return;
                }
                toastr.error(data.message);
            },
            error: function (data) {
                // do something
            },
        });
    });
});
