<section class="mt-5 p-5">
    <div class="container-fluid">
        <div class="row my-5">
            <h1>Account Settings</h1>
            <div class="w-auto bg-primary-light rounded-4 m-3 p-4">
                <span class="bg-primary text-white rounded-circle p-3 fs-2"><i class="bi bi-person"></i></span>
                <h2 class="d-inline mx-3">Sophia Carter</h2>
                <p class="d-inline pb-5">Parent</p>
            </div>
        </div>
        <!-- Tabs -->
        <div class="underline-tabs mb-5">
            <ul class="nav nav-tabs mb-4" id="underlineTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                        type="button" role="tab">
                        Personal Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                        type="button" role="tab">
                        Child Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security"
                        type="button" role="tab">
                        Password & Security
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites"
                        type="button" role="tab">
                        Favorites
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="underlineTabsContent">
                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form class="form-container">
                            <h3 class="mb-4">Personal Information</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="First Name" />
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Last Name" />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email Address" />
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone Number" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <h4>Profile Content</h4>
                    <p>This is the profile tab content with underline style.</p>
                </div>
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <h4>Password & Security Content</h4>
                    <p>This is the security tab content with underline style.</p>
                </div>
                <div class="tab-pane fade" id="favorites" role="tabpanel">
                    <h4>Favorites Content</h4>
                    <p>This is the security tab content with underline style.</p>
                </div>
            </div>
        </div>
    </div>
</section>