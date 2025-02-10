

<!-- Header Start -->
<header class="app-header" style="background: linear-gradient(135deg, rgba(230, 151, 34, 0.74), rgb(174, 156, 203)); color: white; height: 100px;">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>

        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <div style="display: flex; align-items: center;">
                    <span style="margin-right: 1000px; font-family: 'Times New Roman', Times, serif; color: white; font-size: 37px; font-weight: bold;">
                        Harah Rubina Del Dios
                    </span>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/logos.png" alt="Profile Picture" width="60" height="60" class="rounded-circle">
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="profileDropdown">
                            <li>
                                <a href="#" class="d-flex align-items-center gap-2 dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                                    <i class="ti ti-user fs-6"></i>
                                    <p class="mb-0 fs-6">My Profile</p>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="index.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>
    </nav>
</header>
<!-- Header End -->

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6a11cb, #2575fc); color: white;">
                <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="profileForm">
                    <div class="text-center mb-3">
                        <img src="images/logos.png" alt="Profile Picture" width="100" height="100" class="rounded-circle">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="JohnDoe" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="capstone group" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password">
                    </div>

                    <div class="mb-3">
                        <label for="profileImage" class="form-label">Change Profile Picture</label>
                        <input type="file" class="form-control" id="profileImage">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="saveProfile">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Form Submission -->
<script>
document.getElementById('saveProfile').addEventListener('click', function() {
    alert('Profile updated successfully!');
});
</script>
