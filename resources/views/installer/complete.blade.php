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
    <div class="step completed">
        <div class="step-number">✓</div>
        <div class="step-label">Installation</div>
    </div>
</div>

<div class="content">
    <div class="alert alert-success">
        <h3 style="margin-bottom: 10px; color: #065f46;">🎉 Installation Complete!</h3>
        <p style="margin: 0;">Your Social Media Management Platform has been successfully installed.</p>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-top: 20px;">
        <h4 style="margin-bottom: 15px; color: #333;">Next Steps:</h4>
        <ol style="margin-left: 20px; line-height: 1.8;">
            <li>Visit your homepage to get started</li>
            <li>Create your admin account</li>
            <li>Configure your social media connections</li>
            <li>Start managing your social media presence!</li>
        </ol>
    </div>

    <div style="background: #fef3c7; padding: 15px; border-radius: 5px; margin-top: 20px; border-left: 4px solid #f59e0b;">
        <strong>Important:</strong> For security reasons, please delete the <code>.installed</code> file if you need to run the installer again.
    </div>
</div>

<div class="actions">
    <div></div>
    <a href="/" class="btn btn-primary">Go to Homepage →</a>
</div>
@endsection
