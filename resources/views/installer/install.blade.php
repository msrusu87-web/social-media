@extends('installer.layout')

@section('content')
<h1>Social Media Management Platform</h1>
<h2>Installation Wizard</h2>

<div class="progress-steps">
    <div class="step completed">
        <div class="step-number">✓</div>
        <div class="step-label">Requirements</div>
    </div>
    <div class="step completed">
        <div class="step-number">✓</div>
        <div class="step-label">Configuration</div>
    </div>
    <div class="step active">
        <div class="step-number">3</div>
        <div class="step-label">Installation</div>
    </div>
</div>

<div class="content">
    <h3 style="margin-bottom: 20px; color: #333;">Running Installation</h3>
    
    <div id="install-output"></div>
    <div id="spinner" class="spinner"></div>
    
    <div id="complete-message" style="display: none;">
        <div class="alert alert-success">
            ✓ Installation completed successfully!
        </div>
    </div>

    <div id="error-message" style="display: none;">
        <div class="alert alert-error">
            ✗ <span id="error-text"></span>
        </div>
    </div>
</div>

<div class="actions">
    <a href="{{ route('install.configuration') }}" class="btn btn-secondary" id="back-btn">← Back</a>
    <a href="{{ route('install.complete') }}" class="btn btn-primary" id="complete-btn" style="display: none;">Complete →</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const output = document.getElementById('install-output');
    const spinner = document.getElementById('spinner');
    const completeMessage = document.getElementById('complete-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const backBtn = document.getElementById('back-btn');
    const completeBtn = document.getElementById('complete-btn');

    function log(message) {
        output.innerHTML += message + '<br>';
        output.scrollTop = output.scrollHeight;
    }

    log('Starting installation...');
    log('Running database migrations...');

    // Disable back button during installation
    backBtn.style.display = 'none';

    fetch('{{ route('install.run') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        spinner.style.display = 'none';
        
        if (data.success) {
            log('✓ Migrations completed');
            log('✓ Database seeded');
            log('✓ Installation completed successfully!');
            completeMessage.style.display = 'block';
            completeBtn.style.display = 'inline-block';
        } else {
            log('✗ Installation failed');
            errorText.textContent = data.message;
            errorMessage.style.display = 'block';
            backBtn.style.display = 'inline-block';
        }
    })
    .catch(error => {
        spinner.style.display = 'none';
        log('✗ Installation failed');
        errorText.textContent = 'An unexpected error occurred: ' + error.message;
        errorMessage.style.display = 'block';
        backBtn.style.display = 'inline-block';
    });
});
</script>
@endsection
