@extends('layouts.admin')

@section('title', 'Admin Data - KRUY')
@section('admin-title', str_replace('_', ' ', ucfirst($table)))
@section('admin-subtitle', 'Inspect records from the '.$table.' table.')

@section('content')
    <div class="admin-table-tabs mb-4">
        @foreach ($tables as $name)
            <a class="{{ $table === $name ? 'active' : '' }}" href="{{ route('admin.data', ['table' => $name]) }}">
                {{ str_replace('_', ' ', ucfirst($name)) }}
            </a>
        @endforeach
    </div>

            @if (in_array($table, $creatableTables, true))
                <form class="admin-panel mb-4" action="{{ route('admin.data.store', $table) }}" method="POST">
                    @csrf
                    <div class="admin-create-grid">
                        @foreach ($columns as $column)
                            @continue(in_array($column, ['id', 'created_at', 'updated_at', 'user_id', 'used_count'], true))
                            <div>
                                <label class="form-label" for="{{ $column }}">{{ str_replace('_', ' ', ucfirst($column)) }}</label>
                                @if (str_contains($column, 'date'))
                                    <input class="form-control" id="{{ $column }}" name="{{ $column }}" type="datetime-local">
                                @elseif ($column === 'type')
                                    <select class="form-select" id="{{ $column }}" name="{{ $column }}">
                                        <option value="fixed">Fixed</option>
                                        <option value="percent">Percent</option>
                                    </select>
                                @else
                                    <input class="form-control" id="{{ $column }}" name="{{ $column }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-gold mt-3" type="submit">Create</button>
                </form>
            @endif

            <div class="admin-panel">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr>
                                    @foreach ($columns as $column)
                                        <td>{{ Str::limit((string) $record->{$column}, 60) }}</td>
                                    @endforeach
                                    <td class="text-end">
                                        @if (isset($record->id))
                                            <form action="{{ route('admin.data.destroy', [$table, $record->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                            </form>
                                        @else
                                            <span class="admin-muted">Pivot</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($columns) + 1 }}" class="admin-muted">No records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $records->links() }}
            </div>
@endsection
