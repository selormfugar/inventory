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