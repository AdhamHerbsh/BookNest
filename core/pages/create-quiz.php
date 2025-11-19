<section>
    <div class="container py-5">
        <div class="bg-light p-4 rounded-4 my-5">
            <form>
                <h3 class="mb-4 ">Create Quiz</h3>

                <div class="row g-3">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Title" />
                        </div>

                        <div class="mb-3">
                            <select class="form-select">
                                <option selected>Choose Book</option>
                                <option value="1">Book 1</option>
                                <option value="2">Book 2</option>
                                <option value="3">Book 3</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" rows="7" placeholder="Description"></textarea>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <div class="d-flex gap-2 mb-3">
                            <input type="text" class="form-control" placeholder="Question" />
                            <button type="button" class="btn btn-success px-3">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>

                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-text bg-white">
                                    <input class="form-check-input mt-0" type="radio" name="correctAnswer" id="answer1">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer 1">
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-text bg-white">
                                    <input class="form-check-input mt-0" type="radio" name="correctAnswer" id="answer2">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer 2">
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-text bg-white">
                                    <input class="form-check-input mt-0" type="radio" name="correctAnswer" id="answer3">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer 3">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <div class="input-group-text bg-white">
                                    <input class="form-check-input mt-0" type="radio" name="correctAnswer" id="answer4">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer 4">
                            </div>
                        </div>

                        <!-- Questions Preview List -->
                        <div class="border rounded-3 p-3 bg-white">
                            <h6 class=" mb-3">Questions Preview</h6>
                            <div class="list-group">
                                <div
                                    class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                    <div class="flex-grow-1">
                                        <div class="">1. What is the capital of France?</div>
                                        <small class="text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Correct: Paris
                                        </small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                    <div class="flex-grow-1">
                                        <div class="">2. How many continents are there?</div>
                                        <small class="text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Correct: Seven
                                        </small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                    <div class="flex-grow-1">
                                        <div class="">3. What color is the sky?</div>
                                        <small class="text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Correct: Blue
                                        </small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>