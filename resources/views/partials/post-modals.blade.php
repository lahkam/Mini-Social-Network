<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> Create New Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createPostForm">
                    <div class="mb-3">
                        <label for="postContent" class="form-label">What's on your mind?</label>
                        <textarea class="form-control" id="postContent" name="content" rows="4" maxlength="280" placeholder="Share your thoughts... (max 280 characters)" required></textarea>
                        <div class="form-text">
                            <span id="charCount">280</span> characters remaining
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createPost()">
                    <i class="fas fa-plus"></i> Create Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Post Modal -->
<div class="modal fade" id="aiPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-robot"></i> Generate AI Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="aiPostForm">
                    <div class="mb-3">
                        <label for="aiPrompt" class="form-label">Describe what you want to post about</label>
                        <textarea class="form-control" id="aiPrompt" name="prompt" rows="3" maxlength="500" placeholder="e.g., Write a motivational quote about success" required></textarea>
                        <div class="form-text">
                            Be specific about the tone, topic, or style you want
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="generateAiPost()">
                    <i class="fas fa-robot"></i> Generate Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Invite User Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Send Invitation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inviteForm">
                    <div class="mb-3">
                        <label for="inviteeEmail" class="form-label">User's Email</label>
                        <input type="email" class="form-control" id="inviteeEmail" name="invitee_email" placeholder="friend@example.com" required>
                        <div class="form-text">
                            Enter the email address of the person you want to invite
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="sendInvitation()">
                    <i class="fas fa-paper-plane"></i> Send Invitation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal functions
function showCreatePostModal() {
    new bootstrap.Modal(document.getElementById('createPostModal')).show();
}

function showAiPostModal() {
    new bootstrap.Modal(document.getElementById('aiPostModal')).show();
}

function showInviteModal() {
    new bootstrap.Modal(document.getElementById('inviteModal')).show();
}

// Character counter for post content
document.getElementById('postContent')?.addEventListener('input', function() {
    const remaining = 280 - this.value.length;
    document.getElementById('charCount').textContent = remaining;
    document.getElementById('charCount').className = remaining < 20 ? 'text-danger' : '';
});

// Create normal post
async function createPost() {
    const content = document.getElementById('postContent').value.trim();
    
    if (!content) {
        showMessage('Please enter some content', 'error');
        return;
    }
    
    try {
        const response = await axios.post('/api/posts', { content });
        
        if (response.data.success) {
            showMessage('Post created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createPostModal')).hide();
            document.getElementById('createPostForm').reset();
            
            // Reload posts if on posts page
            if (typeof loadPosts === 'function') {
                loadPosts();
            }
        }
    } catch (error) {
        console.error('Error creating post:', error);
        const message = error.response?.data?.message || 'Failed to create post';
        showMessage(message, 'error');
    }
}

// Generate AI post
function generateAiPost() {
    console.log('Bouton générer cliqué');

    const prompt = document.getElementById('aiPrompt').value.trim();

    if (!prompt) {
        alert('Veuillez entrer un prompt');
        return;
    }

    // Simulation simple - on affiche juste un message de succès
    const generatedContent = "Contenu généré basé sur: " + prompt + ". Ceci est un exemple de génération automatique.";
    
    // Simuler la création d'un post
    alert('Post IA généré avec succès !');
    
    // Fermer le modal si il existe
    const modal = document.getElementById('aiPostModal');
    if (modal) {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
    
    // Réinitialiser le formulaire
    const form = document.getElementById('aiPostForm');
    if (form) {
        form.reset();
    }
}

// Send invitation
async function sendInvitation() {
    const email = document.getElementById('inviteeEmail').value.trim();
    
    if (!email) {
        showMessage('Please enter an email address', 'error');
        return;
    }
    
    try {
        const response = await axios.post('/api/invitations/send', { invitee_email: email });
        
        if (response.data.success) {
            showMessage('Invitation sent successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('inviteModal')).hide();
            document.getElementById('inviteForm').reset();
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to send invitation';
        showMessage(message, 'error');
    }
}
</script>
