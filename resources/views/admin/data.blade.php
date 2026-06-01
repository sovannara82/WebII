@extends('layouts.admin')

@section('title', 'Admin Data - Infinity Figures')
@section('admin-title', str_replace('_', ' ', ucfirst($table)))
@section('admin-subtitle', 'Inspect records from the '.$table.' table.')

@section('content')
            @php
                $baseFormColumns = collect($columns)->reject(fn (string $column): bool => in_array($column, ['id', 'created_at', 'updated_at', 'user_id', 'used_count', 'remember_token', 'email_verified_at'], true));
                $createFormColumns = $baseFormColumns;
                $createFormColumns = $table === 'users'
                    ? $createFormColumns->reject(fn (string $column): bool => $column === 'role_id')
                    : $createFormColumns;
                $editFormColumns = $baseFormColumns->reject(fn (string $column): bool => $column === 'password');
                $imageUrl = function (?string $value): ?string {
                    if (blank($value)) {
                        return null;
                    }

                    if (Str::startsWith($value, ['http://', 'https://', '/'])) {
                        return $value;
                    }

                    if (Str::startsWith($value, 'storage/')) {
                        return asset($value);
                    }

                    return asset('storage/'.$value);
                };
            @endphp

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please check these fields:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (in_array($table, $creatableTables, true))
                <form class="admin-panel mb-4" action="{{ route('admin.data.store', $table) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="admin-create-grid">
                        @foreach ($createFormColumns as $column)
                            <div>
                                <label class="form-label" for="{{ $column }}">{{ str_replace('_', ' ', ucfirst($column)) }}</label>
                                @if (in_array($column, ['isActive', 'is_primary'], true))
                                    <select class="form-select" id="{{ $column }}" name="{{ $column }}">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                @elseif ($column === 'image')
                                    <input class="form-control" id="{{ $column }}" name="{{ $column }}" type="file" accept="image/*">
                                @elseif (in_array($column, ['start_date', 'end_date'], true))
                                    <input class="form-control" id="{{ $column }}" name="{{ $column }}" type="date">
                                @elseif ($column === 'password')
                                    <input class="form-control" id="{{ $column }}" name="{{ $column }}" type="password" autocomplete="new-password">
                                @elseif (str_contains($column, 'date'))
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
                                @php
                                    $recordKey = $table === 'product_tags'
                                        ? $record->product_id.'-'.$record->tag_id
                                        : ($record->id ?? null);
                                @endphp

                                <tr>
                                    @foreach ($columns as $column)
                                        <td>
                                            @if ($column === 'image' && filled($record->{$column}))
                                                <img class="admin-table-image" src="{{ $imageUrl($record->{$column}) }}" alt="{{ $table }} image">
                                            @else
                                                {{ Str::limit((string) $record->{$column}, 60) }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end">
                                        @if ($recordKey)
                                            <div class="admin-row-actions">
                                                @if (in_array($table, $editableTables, true))
                                                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="modal" data-bs-target="#update-{{ $table }}-{{ $recordKey }}">
                                                        Edit
                                                    </button>
                                                @endif

                                                @if (isset($record->id))
                                                    <form action="{{ route('admin.data.destroy', [$table, $record->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
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

            @if (in_array($table, $editableTables, true))
                @foreach ($records as $record)
                    @php
                        $recordKey = $table === 'product_tags'
                            ? $record->product_id.'-'.$record->tag_id
                            : ($record->id ?? null);
                    @endphp

                    @continue(! $recordKey)

                    <div class="modal fade" id="update-{{ $table }}-{{ $recordKey }}" tabindex="-1" aria-labelledby="update-{{ $table }}-{{ $recordKey }}-label" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <form class="modal-content admin-modal" action="{{ route('admin.data.update', [$table, $recordKey]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <div>
                                        <span>Edit record</span>
                                        <h2 class="modal-title" id="update-{{ $table }}-{{ $recordKey }}-label">
                                            {{ str_replace('_', ' ', ucfirst($table)) }} #{{ $recordKey }}
                                        </h2>
                                    </div>
                                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="admin-create-grid">
                                        @foreach ($editFormColumns as $column)
                                            @php
                                                $fieldValue = old($column, $record->{$column});

                                                if (in_array($column, ['start_date', 'end_date'], true) && filled($fieldValue)) {
                                                    $fieldValue = \Illuminate\Support\Carbon::parse($fieldValue)->format('Y-m-d');
                                                } elseif (str_contains($column, 'date') && filled($fieldValue)) {
                                                    $fieldValue = \Illuminate\Support\Carbon::parse($fieldValue)->format('Y-m-d\TH:i');
                                                }
                                            @endphp

                                            <div>
                                                <label class="form-label" for="update-{{ $table }}-{{ $recordKey }}-{{ $column }}">
                                                    {{ str_replace('_', ' ', ucfirst($column)) }}
                                                </label>
                                                @if (in_array($column, ['isActive', 'is_primary'], true))
                                                    <select class="form-select" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}">
                                                        <option value="1" @selected((bool) $fieldValue)>Yes</option>
                                                        <option value="0" @selected(! (bool) $fieldValue)>No</option>
                                                    </select>
                                                @elseif ($table === 'users' && $column === 'role_id')
                                                    <select class="form-select" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}" @disabled($record->role?->name === 'Admin')>
                                                        @foreach ($roles as $role)
                                                            @continue($role->name === 'Admin' && $record->role?->name !== 'Admin')
                                                            <option value="{{ $role->id }}" @selected((int) $fieldValue === $role->id)>{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif ($column === 'image')
                                                    <input class="form-control" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}" type="file" accept="image/*">
                                                    @if (filled($record->{$column}))
                                                        <img class="admin-form-image" src="{{ $imageUrl($record->{$column}) }}" alt="{{ $table }} image preview">
                                                    @endif
                                                @elseif (in_array($column, ['start_date', 'end_date'], true))
                                                    <input class="form-control" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}" type="date" value="{{ $fieldValue }}">
                                                @elseif (str_contains($column, 'date'))
                                                    <input class="form-control" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}" type="datetime-local" value="{{ $fieldValue }}">
                                                @elseif ($column === 'type')
                                                    <select class="form-select" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}">
                                                        <option value="fixed" @selected($fieldValue === 'fixed')>Fixed</option>
                                                        <option value="percent" @selected($fieldValue === 'percent')>Percent</option>
                                                    </select>
                                                @else
                                                    <input class="form-control" id="update-{{ $table }}-{{ $recordKey }}-{{ $column }}" name="{{ $column }}" value="{{ $fieldValue }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-outline-light" type="button" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-gold" type="submit">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
@endsection
