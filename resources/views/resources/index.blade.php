@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-success fw-semibold mb-1">MANAGE YOUR BUSINESS</p><h1 class="page-heading h2 mb-0">{{ $title }}</h1></div>
        <a href="{{ route($routeName.'.create') }}" class="btn btn-primary">＋ Add {{ rtrim($title, 's') }}</a>
    </div>
    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr>@foreach ($columns as $label)<th>{{ $label }}</th>@endforeach<th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse ($records as $record)
                    <tr>
                        @foreach ($columns as $key => $label)
                            @php($value = data_get($record, $key))
                            <td>
                                @if ($key === 'available')
                                    <span class="badge {{ $value ? 'badge-soft' : 'bg-secondary-subtle text-secondary' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                @elseif ($key === 'status')
                                    <span class="badge badge-soft text-capitalize">{{ str_replace('_', ' ', $value) }}</span>
                                @elseif ($key === 'total' || $key === 'price')
                                    ${{ number_format((float) $value, 2) }}
                                @elseif ($key === 'dispatched_at')
                                    {{ $value?->format('M j, Y') ?? '—' }}
                                @else
                                    {{ $value ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap">
                            <a href="{{ route($routeName.'.show', $record) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route($routeName.'.edit', $record) }}" class="btn btn-sm btn-outline-success">Edit</a>
                            <form action="{{ route($routeName.'.destroy', $record) }}" method="post" class="d-inline" data-confirm="Delete this record? This action cannot be undone.">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 1 }}" class="text-center text-secondary py-5">No records yet. Add your first one to get started.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($records->hasPages())<div class="card-footer bg-white">{{ $records->links() }}</div>@endif
    </div>
@endsection
