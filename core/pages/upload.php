<section class="mt-5">
    <div class="container">
        <div class="row">
            <div class="bg-light p-4 rounded-4 my-5">
                <form>
                    <h3 class="mb-4 fw-bold">Upload Book</h3>

                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-lg-7">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Title" />
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Author" />
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <select class="form-select">
                                        <option selected>Language</option>
                                        <option value="1">English</option>
                                        <option value="2">Spanish</option>
                                        <option value="3">French</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <div class="form-check form-switch d-flex justify-content-between h-100 ps-5">
                                        <label class="form-check-label fw-semibold" for="isActive">
                                            Is Active
                                        </label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="isActive">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Description"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <label class="form-labelfw-semibold">Age Group</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="age1" checked>
                                        <label class="form-check-label fw-semibold" for="age1">
                                            4 - 6
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="age2">
                                        <label class="form-check-label fw-semibold" for="age2">
                                            7 - 9
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="age3">
                                        <label class="form-check-label fw-semibold" for="age3">
                                            10 - 12
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div
                                    class="border border-2 border-dark border-opacity-50 rounded-4 d-flex flex-column align-items-center justify-content-center p-4">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem;"></i>
                                    <p class="fw-semibold mb-0 mt-2">Upload Cover Image</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div
                                    class="border border-2 border-dark border-opacity-50 rounded-4 d-flex align-items-center justify-content-center p-4">
                                    <p class="fw-semibold mb-0">Preview</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Button -->
                    <div class="text-center my-4">
                        <button type="button" class="btn btn-primary">
                            <i class="bi bi-cloud-arrow-up me-2"></i>
                            Upload Book From Your Device
                        </button>
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
    </div>

</section>