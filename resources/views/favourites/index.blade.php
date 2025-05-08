@extends('template')

@section('title', 'My Favorite Sheets')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4">❤️ My Favorite Sheets</h2>
            <a href="{{ route('sheets.create') }}" class="btn btn-sm btn-primary">Upload New Sheet</a>
        </div>

        @if ($favorites->isEmpty())
            <div class="alert alert-info">
                You haven’t added any sheets to your favorites yet.
            </div>
        @else
            {{-- Search bar --}}
            <div class="mb-3">
                <input type="text" id="favorite-search" class="form-control" placeholder="Search by title...">
            </div>

            {{-- Compact table view --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle" id="favorites-table">
                    <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Favorited On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($favorites as $sheet)
                        <tr>
                            <td class="sheet-title">
                                <a href="{{ route('sheets.show', $sheet) }}">{{ $sheet->title }}</a>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sheet->is_public ? 'success' : 'secondary' }}">
                                    {{ $sheet->is_public ? 'Public' : 'Private' }}
                                </span>
                            </td>
                            <td>{{ $sheet->pivot->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-grey" onclick="unfavourite({{ $sheet->id }})">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        function unfavourite(sheetId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unfavourite it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{route("sheets.favourite")}}?sheet_id=${sheetId}`;
                }
            });
        }

        // Filter functionality for the search bar
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('favorite-search');
            const rows = document.querySelectorAll('#favorites-table tbody tr');

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();

                rows.forEach(row => {
                    const title = row.querySelector('.sheet-title').textContent.toLowerCase();
                    row.style.display = title.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
@endsection

