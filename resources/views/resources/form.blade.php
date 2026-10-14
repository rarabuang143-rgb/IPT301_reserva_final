@extends('layouts.app')

@section('title', ($record->exists ? 'Edit ' : 'Add ').$title)

@section('content')
    <div class="mb-4">
        <a href="{{ route($routeName.'.index') }}" class="text-success text-decoration-none small">← Back to {{ strtolower($title) }} list</a>
        <h1 class="page-heading h2 mt-2 mb-0">{{ $record->exists ? 'Edit' : 'Add' }} {{ $title }}</h1>
    </div>
    <div class="card"><div class="card-body p-4 p-lg-5">
        <form action="{{ $record->exists ? route($routeName.'.update', $record) : route($routeName.'.store') }}" method="post">
            @csrf
            @if ($record->exists) @method('PUT') @endif
            <div class="row g-3">
                @foreach ($fields as $field)
                    @php
                        $value = old($field['name'], $record->getAttribute($field['name']));
                        if ($field['type'] === 'datetime-local' && $value) $value = \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i');
                        $required = $field['required'] ?? true;
                    @endphp
                    <div class="col-12 {{ $field['type'] === 'textarea' ? '' : 'col-md-6' }}">
                        @if ($field['type'] === 'checkbox')
                            <div class="form-check mt-4">
                                <input type="hidden" name="{{ $field['name'] }}" value="0">
                                <input class="form-check-input" type="checkbox" id="{{ $field['name'] }}" name="{{ $field['name'] }}" value="1" @checked((bool) $value)>
                                <label class="form-check-label" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                            </div>
                        @else
                            <label class="form-label fw-semibold" for="{{ $field['name'] }}">{{ $field['label'] }} @if ($required)<span class="text-danger">*</span>@endif</label>
                            @if ($field['type'] === 'textarea')
                                <textarea class="form-control @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}" rows="3" @required($required)>{{ $value }}</textarea>
                            @elseif ($field['type'] === 'select')
                                <select class="form-select @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}" @required($required)>
                                    <option value="">Choose {{ strtolower($field['label']) }}</option>
                                    @foreach ($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input class="form-control @error($field['name']) is-invalid @enderror" type="{{ $field['type'] }}" id="{{ $field['name'] }}" name="{{ $field['name'] }}" value="{{ $value }}" @if (isset($field['min'])) min="{{ $field['min'] }}" @endif @if (isset($field['max'])) max="{{ $field['max'] }}" @endif @if (isset($field['step'])) step="{{ $field['step'] }}" @endif @required($required)>
                            @endif
                            @error($field['name'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary">{{ $record->exists ? 'Save changes' : 'Create' }}</button>
                <a href="{{ route($routeName.'.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div></div>
@endsection
