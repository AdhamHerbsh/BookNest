<section class="mt-5">
    <div class="container">
        <div class="row">
            <div class="bg-light p-4 rounded-4 my-5">
                <form id="uploadBookForm" enctype="multipart/form-data">
                    <h3 class="mb-4 fw-bold">Upload Book</h3>

                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-lg-7">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" required />
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" id="author" name="author" placeholder="Author" required />
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <select class="form-select" id="language" name="language" required>
                                        <option value="" selected disabled>Language</option>
                                        <option value="English">English</option>
                                        <option value="Spanish">Spanish</option>
                                        <option value="French">French</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <div class="form-check form-switch d-flex justify-content-between h-100 ps-5">
                                        <label class="form-check-label fw-semibold" for="isActive">
                                            Is Active
                                        </label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="isActive" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Description" required></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <fieldset>
                                    <legend class="form-label fw-semibold">Age Group</legend>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="age_group" id="age1" value="4-6" required>
                                            <label class="form-check-label fw-semibold" for="age1">
                                                4 - 6
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="age_group" id="age2" value="7-9">
                                            <label class="form-check-label fw-semibold" for="age2">
                                                7 - 9
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="age_group" id="age3" value="10-12">
                                            <label class="form-check-label fw-semibold" for="age3">
                                                10 - 12
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="mb-3">
                                <div id="coverDropZone"
                                    class="border border-dashed border-2 border-dashed border-dark border-opacity-50 rounded-4 d-flex flex-column align-items-center justify-content-center p-4"
                                    style="cursor: pointer;">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem;"></i>
                                    <p class="fw-semibold mb-0 mt-2">Upload Cover Image</p>
                                    <input type="file" id="cover_image" name="cover_image" accept="image/*" class="d-none" required aria-label="Upload cover image file">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div id="previewContainer"
                                    class="border border-2 border-dark border-opacity-50 rounded-4 d-flex align-items-center justify-content-center p-4"
                                    style="min-height: 200px; overflow: hidden;">
                                    <p class="fw-semibold mb-0" id="previewText">Preview</p>
                                    <img id="coverPreview" src="" alt="Cover Preview" class="img-fluid d-none" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Button -->
                    <div class="text-center my-4">
                        <button type="button" class="btn btn-primary" id="uploadBookBtn">
                            <i class="bi bi-cloud-arrow-up me-2"></i>
                            <span id="bookFileName">Upload a book from your device</span>
                        </button>
                        <input type="file" id="book_file" name="book_file" accept=".pdf,.epub" class="d-none" required aria-label="Upload book file (PDF or EPUB)">
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" onclick="window.location.href='?page=books'">
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