<style>
.card {
    transform: scale(1);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
    transition: .3s linear;
}

.card:hover {
    cursor: pointer;
    transform: scale(1.1);
    box-shadow: 0 -0.5rem 1rem rgba(0, 0, 0, .15) inset;
    transition: .3s linear;
}
</style>
<section class="mt py-5-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row">
            <div class="col-8">
                <h1>Admin Dashboard</h1>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="row">
                <div class="col-12 col-md-4 m-1 m-md-0">
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <h3 class="card-title py-4 px-2">
                                <span class="p-4 rounded-circle fs-2 bg-beige">
                                    <i class="bi bi-people"></i>
                                </span>
                            </h3>
                            <p class="card-text fs-1">USERS</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 m-3 m-md-0">
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <h3 class="card-title py-4 px-2">
                                <span class="p-4 rounded-circle fs-2 bg-beige">
                                    <i class="bi bi-book"></i>
                                </span>
                            </h3>
                            <p class="card-text fs-1">BOOKS</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 m-1 m-md-0">
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <h3 class="card-title py-4 px-2">
                                <span class="p-4 rounded-circle fs-2 bg-beige">
                                    <i class="bi bi-file-earmark"></i>
                                </span>
                            </h3>
                            <p class="card-text fs-1">QUIZZES</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>