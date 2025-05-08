@extends('template')

@section('title', $sheet->title)

@section('content')
    <style>
        .commentop {
            display: inline-block;
        }
    </style>
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">{{ $sheet->title }}</h2>
            <span class="badge bg-{{ $sheet->is_public ? 'success' : 'secondary' }}">
            {{ $sheet->is_public ? 'Public' : 'Private' }}
        </span>
        </div>

        {{-- Description --}}
        @if($sheet->description)
            <p class="text-muted">{{ $sheet->description }}</p>
        @endif

        {{-- Audio Preview --}}
        @if($sheet->preview_audio_path)
            <div class="mb-4">
                <label class="form-label fw-semibold">Preview Audio</label><br>
                <audio controls>
                    <source src="https://us.file.jianqinggao.com/{{ $sheet->preview_audio_path }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        @endif

        {{-- Fullscreen Mode Button --}}
        <div class="d-flex justify-content-end mb-2">
            <button id="fullscreen-btn" class="btn btn-outline-dark btn-sm">
                🔍 Enter Full Screen
            </button>
            <!--Favourite it if user is logged in-->
            @auth
                <form action="{{ route('sheets.favourite') }}" method="GET" class="ms-2">
                    <input type="hidden" name="sheet_id" value="{{ $sheet->id }}">
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        {{ $sheet->isFavoritedBy(auth()->id()) ? '💔 Unfavourite' : '❤️ Favourite' }}
                    </button>
                </form>

                <!--if the user owns the sheet show edit-->
                @if ($sheet->user_id === auth()->id())
                    <a href="{{ route('sheets.create') }}?id={{$sheet->id}}"
                       class="btn btn-outline-secondary btn-sm ms-2">✏️ Edit</a>
                @endif
            @endauth


        </div>


        {{-- Sheet Images --}}
        <div id="sheet-container" class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            @foreach ($sheet->images as $img)
                <img src="https://us.file.jianqinggao.com/{{ $img->image_path }}"
                     class="img-fluid border rounded"
                     style="max-height: 500px;"
                     alt="Sheet image">
            @endforeach
        </div>

        {{-- Comments --}}
        <h4 class="mb-3">💬 Comments ({{ $sheet->comments->count() }})</h4>

        @forelse ($sheet->comments->sortByDesc('created_at') as $comment)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong>{{ $comment->user->name }}</strong>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0" id="comment-body-{{$comment->id}}">{{ $comment->body }}</p>

                @if ($comment->user_id === auth()->id())
                    <!-- Edit Comment Form. When clicked, pop a Swal to edit -->
                    <button class="btn btn-sm btn-outline-secondary" onclick="editComment({{ $comment->id }})">
                        Edit
                    </button>
                    <form id="comment-form-{{$comment->id}}"
                          action="{{ route('comments.update',['sheet'=>$sheet,'comment'=>$comment]) }}" method="POST"
                          class="d-none commentop">
                        @csrf
                        @method('PUT')
                        <textarea name="body" class="d-none"></textarea>
                        <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                    </form>

                    <form action="{{ route('comments.destroy',['sheet'=>$sheet,'comment'=>$comment]) }}" method="POST"
                          class="mt-2 commentop" onsubmit="
                    return confirm('Are you sure you want to delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                @endif

            </div>
        @empty
            <p class="text-muted">No comments yet. Be the first to comment!</p>
        @endforelse

        {{-- Comment Form --}}
        @auth
            <form method="POST" action="{{ route('comments.store', $sheet) }}">
                @csrf
                <div class="mb-3">
                    <label for="body" class="form-label fw-semibold">Leave a Comment</label>
                    <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror"
                              rows="3" required>{{ old('body') }}</textarea>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        @else
            <p class="text-muted">Please <a href="{{ route('login') }}">log in</a> to post a comment.</p>
        @endauth
    </div>

    {{-- Fullscreen Script --}}
    <script>
        function editComment(commentId) {
            const commentBody = document.getElementById(`comment-body-${commentId}`);
            const commentForm = document.getElementById(`comment-form-${commentId}`);
            const commentText = commentBody.innerText;

            Swal.fire({
                title: 'Edit Comment',
                input: 'textarea',
                inputValue: commentText,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage('Please enter a comment');
                    }
                    return value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    commentForm.querySelector('textarea').value = result.value;
                    commentForm.submit();
                }
            });
        }

        document.getElementById('fullscreen-btn').addEventListener('click', () => {
            const el = document.getElementById('sheet-container');
            //not implemented yet
            Swal.fire({
                icon: 'info',
                title: 'Coming Soon!',
                text: 'Fullscreen mode is not implemented yet.',
            });
        });
    </script>
@endsection
