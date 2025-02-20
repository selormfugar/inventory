<?php
session_start();

// If user is already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/');
            break;
        case 'manager':
            header('Location: manager/');
            break;
        case 'staff':
            header('Location: staff/');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Stan Prestige Plus</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="stan-prestige-plus-logo.png" />
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                            <img src="stan-prestige-plus-logo.png" alt="logo" />
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="fw-light">Sign in to continue.</h6>
                            
                            <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                            
                            <form id="loginForm" class="pt-3">
                                <div class="form-group">
                                    <input type="text" 
                                           name="username" 
                                           class="form-control form-control-lg" 
                                           placeholder="Username" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <input type="password" 
                                           name="password" 
                                           class="form-control form-control-lg" 
                                           placeholder="Password" 
                                           required>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit" 
                                            class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                                        SIGN IN
                                    </button>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                                                        <!-- <a href="forgot-password.php" class="auth-link text-black">Forgot password?</a> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    
    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const errorDiv = document.getElementById('error-message');
        
        fetch('auth/login_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                errorDiv.style.display = 'block';
                errorDiv.textContent = data.message;
            }
        })
        .catch(error => {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'An error occurred. Please try again.';
            console.error('Error:', error);
        });
    });
    
// Disable browser back button
(function() {
    // Push a new state to history to prevent immediate back
    window.history.pushState(null, "", window.location.href);
    
    // Handle popstate event (triggered when back/forward buttons are clicked)
    window.addEventListener('popstate', function() {
        window.history.pushState(null, "", window.location.href);
    });
})();

// Additional security: Disable right-click context menu
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Disable keyboard shortcuts for back navigation
document.addEventListener('keydown', function(e) {
    // Disable Alt + Left Arrow
    if (e.altKey && e.keyCode === 37) {
        e.preventDefault();
        return false;
    }
    
    // Disable Backspace outside of input fields
    if (e.keyCode === 8 && !isInputField(e.target)) {
        e.preventDefault();
        return false;
    }
});

// Helper function to check if element is an input field
function isInputField(element) {
    const tagName = element.tagName.toLowerCase();
    const type = element.type ? element.type.toLowerCase() : '';
    
    return (
        tagName === 'input' && ['text', 'password', 'number', 'email', 'tel', 'url'].includes(type) ||
        tagName === 'textarea' ||
        element.isContentEditable
    );
}
</script>
</body>
</html>