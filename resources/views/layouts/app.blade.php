<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Social Media Platform' }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        .post-card {
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .ai-post {
            border-left-color: #28a745;
        }
        .ai-badge {
            background-color: #28a745;
            color: white;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
        }
        .invitation-card {
            border-left: 4px solid #ffc107;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .main-content {
            min-height: calc(100vh - 56px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-share-alt"></i> SocialConnect
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.index') }}">
                                <i class="fas fa-edit"></i> Posts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invitations.index') }}">
                                <i class="fas fa-user-plus"></i> Invitations
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="logout()">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            @auth
                <!-- Sidebar -->
                <div class="col-md-3 sidebar p-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Quick Actions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" onclick="showCreatePostModal()">
                                    <i class="fas fa-plus"></i> New Post
                                </button>
                                <button class="btn btn-success btn-sm" onclick="showAiPostModal()">
                                    <i class="fas fa-robot"></i> AI Post
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="showInviteModal()">
                                    <i class="fas fa-user-plus"></i> Send Invite
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-md-9 main-content p-3">
                    @yield('content')
                </div>
            @else
                <!-- Full Width for Guests -->
                <div class="col-12 main-content p-3">
                    @yield('content')
                </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios for API calls -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // Function to refresh CSRF token
        function refreshCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            return token;
        }
        
        // Initialize CSRF token
        refreshCsrfToken();
        
        // Add response interceptor to handle authentication and CSRF errors
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response) {
                    if (error.response.status === 401) {
                        showMessage('Session expired. Please login again.', 'error');
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 2000);
                    } else if (error.response.status === 419) {
                        showMessage('Page expired. Refreshing...', 'error');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
                return Promise.reject(error);
            }
        );

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Show success/error messages
        function showMessage(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alert = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.querySelector('.main-content');
            container.insertAdjacentHTML('afterbegin', alert);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alertElement = container.querySelector('.alert');
                if (alertElement) {
                    alertElement.remove();
                }
            }, 5000);
        }
    </script>

    @stack('scripts')
</body>
</html>
