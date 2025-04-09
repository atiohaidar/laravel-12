@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">API Documentation</h2>
                </div>
                <div class="card-body">
                    <h3>Authentication</h3>
                    <p>All API endpoints require authentication using a Bearer token. Include your token in the Authorization header:</p>
                    <pre><code>Authorization: Bearer your-token-here</code></pre>

                    <hr>

                    <h3>Available Endpoints</h3>

                    <div class="endpoint mb-4">
                        <h4>Get User Profile</h4>
                        <p><code>GET /api/user</code></p>
                        <p>Returns the authenticated user's profile information.</p>
                        <p><strong>Response:</strong></p>
<pre><code>{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
}</code></pre>
                    </div>

                    <div class="endpoint mb-4">
                        <h4>Update User Profile</h4>
                        <p><code>PUT /api/user</code></p>
                        <p>Update the authenticated user's profile information.</p>
                        <p><strong>Parameters:</strong></p>
<pre><code>{
    "name": "string",      // required
    "email": "string",     // required, must be unique
    "password": "string"   // optional, minimum 8 characters
}</code></pre>
                    </div>

                    <div class="alert alert-info">
                        <h4>Need more help?</h4>
                        <p>For detailed API documentation and example requests, you can download our Postman collection:</p>
                        <a href="/Laravel_12_API.postman_collection.json" class="btn btn-primary">
                            Download Postman Collection
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
