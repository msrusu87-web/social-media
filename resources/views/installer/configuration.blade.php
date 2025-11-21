@extends('installer.layout')

@section('content')
<h1>Social Media Management Platform</h1>
<h2>Installation Wizard</h2>

<div class="progress-steps">
    <div class="step completed">
        <div class="step-number">✓</div>
        <div class="step-label">Requirements</div>
    </div>
    <div class="step active">
        <div class="step-number">2</div>
        <div class="step-label">Configuration</div>
    </div>
    <div class="step">
        <div class="step-number">3</div>
        <div class="step-label">Installation</div>
    </div>
</div>

<div class="content">
    <h3 style="margin-bottom: 20px; color: #333;">Configuration</h3>
    
    <form action="{{ route('install.save-configuration') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="website_name">Website Name</label>
            <input type="text" id="website_name" name="website_name" value="{{ old('website_name', 'Social Media Platform') }}" required>
        </div>

        <div class="form-group">
            <label for="db_host">Database Host</label>
            <input type="text" id="db_host" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required>
        </div>

        <div class="form-group">
            <label for="db_name">Database Name</label>
            <input type="text" id="db_name" name="db_name" value="{{ old('db_name') }}" required>
        </div>

        <div class="form-group">
            <label for="db_user">Database User</label>
            <input type="text" id="db_user" name="db_user" value="{{ old('db_user', 'root') }}" required>
        </div>

        <div class="form-group">
            <label for="db_password">Database Password</label>
            <input type="password" id="db_password" name="db_password" value="{{ old('db_password') }}">
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="actions">
            <a href="{{ route('install.requirements') }}" class="btn btn-secondary">← Back</a>
            <button type="submit" class="btn btn-primary">Continue →</button>
        </div>
    </form>
</div>
@endsection
