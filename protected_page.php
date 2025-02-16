<?php


function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Store the requested URL for redirect after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: /login.php');
        exit();
    }
}

function logout() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: /login.php');
    exit();
}

// Prevent session fixation
function regenerateSession() {
    if (!isset($_SESSION['last_regeneration'])) {
        regenerateSessionId();
    } else {
        $regeneration_time = 30 * 60; // 30 minutes
        if (time() - $_SESSION['last_regeneration'] >= $regeneration_time) {
            regenerateSessionId();
        }
    }
}

function regenerateSessionId() {
    // Save old session data
    $old_session_data = $_SESSION;
    
    // Generate new session ID
    session_regenerate_id(true);
    
    // Restore session data
    $_SESSION = $old_session_data;
    $_SESSION['last_regeneration'] = time();
}

// Check for session timeout
function checkSessionTimeout() {
    $timeout_duration = 30 * 60; // 30 minutes
    
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > $timeout_duration)) {
        logout();
    }
    $_SESSION['last_activity'] = time();
}

?>
<script>
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