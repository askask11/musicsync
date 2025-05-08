@extends('template')

@section('title', 'Welcome to SheetSync')

@section('content')
    <div class="text-center mb-5">
        <h1 class="display-4">🎵 Welcome to SheetSync</h1>
        <p class="lead">Share, discover, and preview sheet music from fellow musicians around the world.</p>
        @guest
            <a href="{{route('login')}}" class="btn btn-primary btn-lg">Get Started</a>
        @else
            <a href="{{route('sheets.create')}}" class="btn btn-success btn-lg">Upload Your Sheet</a>
        @endguest
    </div>

    <h2 class="mb-4">🌍 Public Sheets</h2>

    @if ($publicSheets->isEmpty())
        <div class="alert alert-info">
            No public sheets have been shared yet. Be the first to <a href="{{route("sheets.create")}}">upload one</a>!
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($publicSheets as $sheet)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @if ($sheet->images->first())
                            <img src="https://us.file.jianqinggao.com/{{ $sheet->images->first()->image_path }}"
                                 class="card-img-top" alt="Sheet image">
                        @else
                            <!--<img src="" class="card-img-top" alt="No image">-->
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                 style="height: 200px;">
                                <span class="text-muted
                                        font-italic">No image available</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $sheet->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($sheet->description, 80) }}</p>

                            @if ($sheet->preview_audio_path)
                                <audio controls class="mt-2">
                                    <source src="https://us.file.jianqinggao.com/{{$sheet->preview_audio_path}}" type="audio/mpeg">
                                    Your browser does not support the audio tag.
                                </audio>
                            @endif

                            <div class="mt-auto">
                                <a href="{{ route('sheets.show', $sheet) }}"
                                   class="btn btn-sm btn-outline-primary w-100 mt-3">View Sheet</a>
                            </div>
                        </div>

                        <div class="card-footer text-muted text-center small">
                            Shared by {{ $sheet->user->name }} on {{ $sheet->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
