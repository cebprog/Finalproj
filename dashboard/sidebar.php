<div class="card">
    <div class="card-body">
        <h5 class="card-title">Create Event</h5>
        <form action="feature/post_process.php" method="post">
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Title</label>
                <input type="text" class="form-control" id="formGroupExampleInput" name="title" placeholder="Title" required>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">City</label>
                <input type="text" class="form-control" id="formGroupExampleInput2" name="city" placeholder="City" required>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
