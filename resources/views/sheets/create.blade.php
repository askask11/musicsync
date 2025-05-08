@extends('template')

@section('title', isset($sheet) ? 'Edit Sheet' : 'Create Sheet')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4 text-center">
                {{ isset($sheet) ? 'Edit Music Sheet' : 'Create Music Sheet' }}
            </h1>

            <form id="sheet-form"
                  action="{{ isset($sheet) ? route('sheets.update') : route('sheets.create.submit') }}"
                  method="POST">
                @csrf
                @if(isset($sheet))
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $sheet->id }}">
                @endif

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
                    <input id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           name="title"
                           value="{{ old('title', $sheet->title ?? '') }}"
                           maxlength="255" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description"
                              class="form-control @error('description') is-invalid @enderror"
                              name="description"
                              rows="3">{{ old('description', $sheet->description ?? '') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Public / Private --}}
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_public" id="is_public"
                        {{ old('is_public', $sheet->is_public ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_public">Make sheet public</label>
                </div>

                {{-- Preview Audio --}}
                <div class="mb-4">
                    <label class="form-label" for="preview_audio_path">Preview Audio (optional, ≤3MB)</label>
                    <input type="file"
                           accept="audio/*"
                           id="preview_audio_path"
                           class="form-control @error('preview_audio_path') is-invalid @enderror">
                    <input type="hidden" name="preview_audio_path"
                           value="{{ old('preview_audio_path', $sheet->preview_audio_path ?? '') }}">
                    @error('preview_audio_path')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Image Dropzone --}}
                <h5 class="mb-2">Sheet Images <small class="text-muted">(drag & drop or click)</small></h5>
                <!-- This area, let's use Dropzone.js for images -->
                <div id="image-dropzone" class="dropzone border rounded p-4 mb-4 text-center">
                    <div class="dz-message">
                        Drop images here or click to upload<br>
                        <small class="text-muted">(Each ≤3MB, JPEG/PNG/GIF/WebP, multiple allowed)</small>
                    </div>
                </div>
                @error('image_paths')
                <div class="text-danger mb-3">{{ $message }}</div> @enderror

                {{-- Old images for edit
                @if(isset($sheet))
                    @foreach($sheet->images as $img)
                        <input type="hidden" name="image_paths[]" value="{{ $img->image_path }}">
                    @endforeach
                @endif--}}

                <button class="btn btn-primary w-100">
                    {{ isset($sheet) ? 'Save Changes' : 'Create Sheet' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Dropzone & SweetAlert --}}
    <link href="https://cdn.jsdelivr.net/npm/dropzone@6.0.0-beta.2/dist/dropzone.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/dropzone@6.0.0-beta.2/dist/dropzone-min.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!--Some styling-->
    <style>
        .dropzone {
            background: #f9f9f9;
            border: 2px dashed #ccc;
            border-radius: 1rem;
        }

        .dropzone .dz-message {
            font-size: 1rem;
            color: #666;
        }

        .dropzone .dz-preview .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .dropzone .dz-preview {
            margin: 10px;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    {{-- Dropzone & Audio Upload Logic --}}
    <script>
        Dropzone.autoDiscover = false;
        const csrf = document.querySelector('input[name="_token"]').value;//get the csrf token from the form

        const dz = new Dropzone('#image-dropzone', {
            url: '{{ route("upload") }}',
            paramName: 'file',
            maxFilesize: 3,
            acceptedFiles: 'image/*',
            headers: {'X-CSRF-TOKEN': csrf},
            addRemoveLinks: true,
            dictRemoveFile: 'Remove',
            init() {
                // Prepopulate with existing images if editing
                @if(isset($sheet))
                let mock;
                let input;
                @foreach($sheet->images as $img)
                // Create a mock file object
                // This is a mock file, not an actual file
                mock = {name: '{{ basename($img->image_path) }}', size: 123456};
                //add the mock file to the dropzone
                this.emit('addedfile', mock);
                this.emit('thumbnail', mock, 'https://us.file.jianqinggao.com/{{ $img->image_path }}');
                this.emit('complete', mock);
                mock._uploadedPath = '{{ $img->image_path }}';

                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'image_paths[]';
                input.value = '{{ $img->image_path }}';
                mock._hiddenInput = input;
                document.getElementById('sheet-form').appendChild(input);
                @endforeach
                @endif
            },
            success(file, response) {
                if (response.success) {
                    file._uploadedPath = response.file_id;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'image_paths[]';
                    input.value = response.file_id;
                    file._hiddenInput = input;
                    document.getElementById('sheet-form').appendChild(input);
                } else {
                    this.removeFile(file);
                    Swal.fire('Upload failed', response.message, 'error');
                }
            },
            error(file, msg) {
                Swal.fire('Upload failed', (msg.message || msg), 'error');
                this.removeFile(file);
            },
            removedfile(file) {
                if (file.previewElement) file.previewElement.remove();
                if (file._hiddenInput) file._hiddenInput.remove();
                else if (file._uploadedPath) {
                    const selector = `input[name="image_paths[]"][value="${file._uploadedPath}"]`;
                    document.querySelectorAll(selector).forEach(el => el.remove());//remove the hidden input so that it doesn't get submitted
                }
            }
        });

        // Full image preview on click
        document.addEventListener('click', function (e) {
            if (e.target && e.target.closest('.dz-image img')) {
                const img = e.target.closest('.dz-image img');
                Swal.fire({
                    title: 'Preview',
                    imageUrl: img.src,
                    imageAlt: 'Full preview',
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: 'auto',
                    padding: '1rem',
                });
            }
        });

        // Preview audio upload
        document.getElementById('preview_audio_path').addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 3 * 1024 * 1024) {
                Swal.fire('File too big', 'Max size is 3 MB', 'error');
                e.target.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            fetch('{{ route("upload") }}', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrf},
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('input[name="preview_audio_path"]').value = data.file_id;
                        Swal.fire('Uploaded', 'Audio preview uploaded successfully', 'success');
                    } else {
                        e.target.value = '';
                        Swal.fire('Upload failed', data.message, 'error');
                    }
                })
                .catch(() => {
                    e.target.value = '';
                    Swal.fire('Upload failed', 'Network error', 'error');
                });
        });
    </script>
@endsection


