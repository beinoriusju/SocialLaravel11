document.addEventListener('reload', () => {
        window.location.reload();
    });

function changeLanguage(route) {
    window.location.href = route;  // Redirect to the given route
}

window.addEventListener('alert', event => {
    toastr[event.detail.type](event.detail.message, event.detail.title ?? '');
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
    };
});

function showForm(formType) {
    // Hide all forms
    document.getElementById('post-form-container').style.display = 'none';
    document.getElementById('blog-form-container').style.display = 'none';
    document.getElementById('event-form-container').style.display = 'none'; // Added event form container

    // Show the selected form
    if (formType === 'post') {
        document.getElementById('post-form-container').style.display = 'block';
    } else if (formType === 'blog') {
        document.getElementById('blog-form-container').style.display = 'block';
    } else if (formType === 'event') { // Added event form display logic
        document.getElementById('event-form-container').style.display = 'block';
    }
}

// Listen for events
Echo.private(`conversation.${conversationId}`)
    .listen('MessageSent', (e) => {
        Livewire.dispatch('refreshMessages');
        alert("hi");
    });
