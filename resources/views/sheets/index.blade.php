@extends('template')

@section('title', 'My Sheets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">My Sheets</h1>
        <a href="{{ route('sheets.create') }}" class="btn btn-sm btn-primary">Upload New Sheet</a>
    </div>

    @if ($sheets->isEmpty())
        <div class="alert alert-info">
            You haven't uploaded any sheets yet. <a href="{{ route('sheets.create') }}">Create one now</a>.
        </div>
    @else
        {{-- Search Box --}}
        <div class="mb-3">
            <input type="text" id="sheet-search" class="form-control" placeholder="Search sheets by title...">
        </div>

        {{-- Compact Table Layout --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle" id="sheets-table">
                <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Comments</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($sheets as $sheet)
                    <tr>
                        <td class="sheet-title">{{ $sheet->title }}</td>
                        <td>
                                <span class="badge bg-{{ $sheet->is_public ? 'success' : 'secondary' }}">
                                    {{ $sheet->is_public ? 'Public' : 'Private' }}
                                </span>
                        </td>
                        <td>{{ $sheet->comments->count() }}</td>
                        <td class="text-end">
                            <a href="{{ route('sheets.show',$sheet->id) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('sheets.create') }}?id={{$sheet->id}}"
                               class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('sheets.delete') }}?id={{$sheet->id}}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this sheet?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <script>
            // Search Functionality. do something after the page is loaded
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('sheet-search');
                const tableRows = document.querySelectorAll('#sheets-table tbody tr');

                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase();
                    tableRows.forEach(row => {
                        const title = row.querySelector('.sheet-title').textContent.toLowerCase();
                        row.style.display = title.includes(query) ? '' : 'none';
                    });
                });
            });
        </script>
    @endif
@endsection

