@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Debug Page</h1>
            
            @auth
                <div class="alert alert-success">
                    <strong>Authenticated:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Test Create Post</h5>
                    </div>
                    <div class="card-body">
                        <form id="testPostForm">
                            @csrf
                            <div class="mb-3">
                                <label for="testContent" class="form-label">Test Content</label>
                                <textarea id="testContent" class="form-control" rows="3" placeholder="Enter test content..."></textarea>
                            </div>
                            <button type="button" onclick="testCreatePost()" class="btn btn-primary">
                                Test Create Post
                            </button>
                        </form>
                        <div id="testResult" class="mt-3"></div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Session Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Session ID:</strong> {{ session()->getId() }}</p>
                        <p><strong>CSRF Token:</strong> {{ csrf_token() }}</p>
                        <p><strong>User ID:</strong> {{ Auth::id() }}</p>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <strong>Not Authenticated</strong> - <a href="{{ route('login') }}">Please login</a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function testCreatePost() {
    const content = document.getElementById('testContent').value.trim();
    const resultDiv = document.getElementById('testResult');
    
    if (!content) {
        resultDiv.innerHTML = '<div class="alert alert-warning">Please enter some content</div>';
        return;
    }
    
    try {
        console.log('Testing post creation...');
        console.log('Content:', content);
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        resultDiv.innerHTML = '<div class="alert alert-info">Creating post...</div>';
        
        const response = await axios.post('/api/posts', { content });
        
        console.log('Response:', response);
        
        if (response.data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>Success!</strong> Post created successfully.
                    <pre>${JSON.stringify(response.data, null, 2)}</pre>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-warning">
                    <strong>Warning:</strong> ${response.data.message || 'Unknown error'}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        
        let errorMessage = 'Unknown error';
        let statusCode = 'Unknown';
        
        if (error.response) {
            errorMessage = error.response.data.message || error.response.statusText;
            statusCode = error.response.status;
        }
        
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <strong>Error ${statusCode}:</strong> ${errorMessage}
                <pre>${JSON.stringify(error.response?.data || error.message, null, 2)}</pre>
            </div>
        `;
    }
}
</script>
@endpush
