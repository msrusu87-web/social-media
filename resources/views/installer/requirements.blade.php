@extends('installer.layout')

@section('content')
<h1>Social Media Management Platform</h1>
<h2>Installation Wizard</h2>

<div class="progress-steps">
    <div class="step active">
        <div class="step-number">1</div>
        <div class="step-label">Requirements</div>
    </div>
    <div class="step">
        <div class="step-number">2</div>
        <div class="step-label">Configuration</div>
    </div>
    <div class="step">
        <div class="step-number">3</div>
        <div class="step-label">Installation</div>
    </div>
</div>

<div class="content">
    <h3 style="margin-bottom: 20px; color: #333;">Server Requirements</h3>
    
    @if($allMet)
        <div class="alert alert-success">
            ✓ All requirements are met! You can proceed with the installation.
        </div>
    @else
        <div class="alert alert-warning">
            ⚠ Some requirements are not met. Please install missing PHP extensions before continuing.
        </div>
    @endif

    <ul class="requirement-list">
        @foreach($requirements as $name => $met)
        <li class="requirement-item {{ $met ? 'met' : 'not-met' }}">
            <span class="requirement-icon">{{ $met ? '✓' : '✗' }}</span>
            <span class="requirement-name">{{ strtoupper($name) }}</span>
            <span>{{ $met ? 'Found' : 'Not Found' }}</span>
        </li>
        @endforeach
    </ul>
</div>

<div class="actions">
    <div></div>
    <a href="{{ route('install.configuration') }}" class="btn btn-primary {{ !$allMet ? 'disabled' : '' }}" {{ !$allMet ? 'onclick="return false;"' : '' }}>
        Continue →
    </a>
</div>
@endsection
