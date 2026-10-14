@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="mb-4">
        <a href="{{ route($routeName.'.index') }}" class="text-success text-decoration-none small">← Back to {{ strtolower($title) }} list</a>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
            <h1 class="page-heading h2 mb-0">{{ $title }} details</h1>
            <a href="{{ route($routeName.'.edit', $record) }}" class="btn btn-primary">Edit record</a>
        </div>
    </div>
    <div class="card"><dl class="row g-0 mb-0 p-4 p-lg-5">
        @foreach ($details as $label => $value)
            <dt class="col-sm-4 py-3 border-bottom text-secondary">{{ $label }}</dt>
            <dd class="col-sm-8 py-3 border-bottom mb-0 fw-medium">{{ $value }}</dd>
        @endforeach
    </dl></div>
@endsection
