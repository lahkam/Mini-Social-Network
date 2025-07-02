@extends('layouts.app')

@section('content')
@if(config('app.debug'))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <strong>Debug Info:</strong><br>
                User: {{ Auth::user()->name }} ({{ Auth::user()->email }})<br>
                Session ID: {{ session()->getId() }}<br>
                Auth Check: {{ Auth::check() ? 'true' : 'false' }}<br>
                Guard: {{ config('auth.defaults.guard') }}<br>
                Time: {{ now() }}
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </h5>
                <p class="card-text">Welcome to your private social media platform!</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-edit"></i> Recent Posts
                </h6>
            </div>
            <div class="card-body">
                <div id="recentPosts">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('posts.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye"></i> View All Posts
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-envelope"></i> Pending Invitations
                </h6>
            </div>
            <div class="card-body">
                <div id="pendingInvitations">
                    <div class="text-center">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('invitations.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-user-plus"></i> Manage Invitations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('partials.post-modals')
@endsection

@push('scripts')
<script>
// Charger les données du tableau de bord
async function loadDashboardData() {
    try {
        // Charger les publications récentes
        const postsResponse = await axios.get('/api/posts?limit=3');
        displayRecentPosts(postsResponse.data.data.data);
        
        // Charger les invitations en attente
        const invitationsResponse = await axios.get('/api/invitations/pending');
        displayPendingInvitations(invitationsResponse.data.data);
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function displayRecentPosts(posts) {
    const container = document.getElementById('recentPosts');
    
    if (posts.length === 0) {
        container.innerHTML = '<p class="text-muted">No posts yet. Create your first post!</p>';
        return;
    }
    
    container.innerHTML = posts.map(post => `
        <div class="post-card card mb-2 ${post.type === 'ai_generated' ? 'ai-post' : ''}">
            <div class="card-body p-3">
                ${post.type === 'ai_generated' ? '<span class="ai-badge">AI Generated</span>' : ''}
                <p class="card-text small">${post.content}</p>
                <small class="text-muted">
                    <i class="fas fa-clock"></i> ${new Date(post.created_at).toLocaleDateString()}
                </small>
            </div>
        </div>
    `).join('');
}

function displayPendingInvitations(invitations) {
    const container = document.getElementById('pendingInvitations');
    
    if (invitations.length === 0) {
        container.innerHTML = '<p class="text-muted">No pending invitations.</p>';
        return;
    }
    
    container.innerHTML = invitations.map(invitation => `
        <div class="invitation-card card mb-2">
            <div class="card-body p-3">
                <p class="card-text small">
                    <strong>${invitation.inviter.name}</strong> wants to share their posts with you
                </p>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-success" onclick="respondToInvitation(${invitation.id}, 'accept')">
                        <i class="fas fa-check"></i> Accept
                    </button>
                    <button class="btn btn-danger" onclick="respondToInvitation(${invitation.id}, 'decline')">
                        <i class="fas fa-times"></i> Decline
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

async function respondToInvitation(invitationId, action) {
    try {
        const response = await axios.post(`/api/invitations/${invitationId}/${action}`);
        if (response.data.success) {
            showMessage(response.data.message, 'success');
            loadDashboardData(); // Recharger les données
        }
    } catch (error) {
        showMessage('Failed to respond to invitation', 'error');
    }
}

// Charger les données au chargement de la page
document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endpush
