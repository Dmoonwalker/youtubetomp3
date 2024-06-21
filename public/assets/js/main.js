$(document).ready(function() {
    setupCSRFToken();
    initEventHandlers();
    loadComments();
    
});

function setupCSRFToken() {
    // Set CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

function loadComments() {
    $.ajax({
        url: '/comments',
        method: 'GET',
        success: function(response) {
            $('#commentsList').empty();
            response.comments.forEach(comment => {
                $('#commentsList').append(renderComment(comment));
            });
        }
    });
}

function renderComment(comment) {
    return `
        <div class="card comment-card">
            <div class="card-body">
                <div class="comment-header">
                    <img src="${comment.avatar}" alt="Avatar" class="comment-avatar">
                    <div class="comment-author">
                        <h5 class="comment-author-name">${comment.author}</h5>
                        <p class="comment-date">${comment.date}</p>
                    </div>
                </div>
                <p class="comment-text">${comment.text}</p>
            </div>
        </div>
    `;
}
