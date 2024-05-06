<div class="container jumbotron mt-lg-5">
    <h3>Current balance: $<span id="balance">{{ number_format(auth()->user()->balance_money) }}</span></h3>
    <h5>Reserved balance: ${{ number_format(auth()->user()->reserved_money) }}</h5>

    <div class="mt-5">
        <form action="#">
            <div class="form-group">
                <label for="deposit">Deposit ammount</label>
                <div style="width: 50%" class="input-group">
                    <input class="form-control" id="deposit_input" type="number" placeholder="0">
                    <button style="width:30%" class="btn btn-success ml-3" type="button" id="btn-deposit">Deposit</button>
                </div>
            </div>
        </form>
        <form action="#">
            <div class="form-group">
                <label for="withdraw">Withdraw ammount</label>
                <div style="width:50%" class="input-group">
                    <input class="form-control" id="withdraw_input" type="number" placeholder="0">
                    <button style="width:30%" class="btn btn-success ml-3" type="button" id="btn-withdraw">Withdraw</button>
                </div>
            </div>
        </form>
    </div>
</div>
