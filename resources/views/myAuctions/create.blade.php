<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-6">
            <form class="form-group" style="padding-top: 50px" enctype="multipart/form-data">
                <h2 class="display-4 text-center">Create an Auction</h2>
                <label for="name">Name</label>
                <input type="text" class="form-control" id="title" required />
                <label for="description">Description</label>
                <textarea type="text" class="form-control" id="description" rows="4" cols="40" required></textarea>
                <label for="min_price">Minimum Price</label>
                <input type="number" id="min_price" class="form-control" required />
                <div class="custom-file mt-3 mb-3">
                    <input type="file" class="custom-file-input" id="customFile" />
                    <label class="custom-file-label" for="customFile">
                        Choose item photo
                    </label>
                </div>
                <button type="button" class="btn btn-success form-control" id="btn-create-auction">
                    Submit
                </button>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>
