@extends("layouts.master")

@section("content")
    @push("css")
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css"
            integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs4.min.css"
            integrity="sha512-rDHV59PgRefDUbMm2lSjvf0ZhXZy3wgROFyao0JxZPGho3oOuWejq/ELx0FOZJpgaE5QovVtRN65Y3rrb7JhdQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush

    @php
        $editMode = isset($data) && !empty($data);
    @endphp

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ $editMode ? "Edit" : "Tambah" }} Artikel
                            </h4>

                            {{-- Alert --}}
                            <div id="alert-container" style="display:none">
                                <div id="alert-message" class="alert"></div>
                            </div>

                            <form id="form-artikel" enctype="multipart/form-data" method="POST">
                                @csrf

                                @if ($editMode)
                                    <input type="hidden" name="id" value="{{ $data->IDKonten }}">
                                    <input type="hidden" id="old_GambarKonten" name="old_GambarKonten"
                                        value="{{ $data->GambarKonten }}">
                                    <input type="hidden" name="is_remove" id="is_remove" value="0">
                                @endif

                                {{-- Tanggal --}}
                                <div class="form-group">
                                    <label for="tanggalkonten">Tanggal</label>
                                    <input type="text" id="tanggalkonten" name="TanggalKonten"
                                        class="form-control datepicker"
                                        value="{{ old("TanggalKonten", $editMode ? $data->TanggalKonten : now()->format("Y-m-d")) }}"
                                        required>
                                </div>

                                {{-- Judul --}}
                                <div class="form-group">
                                    <label for="judul">Judul Artikel</label>
                                    <input type="text" class="form-control" id="judul" name="JudulKonten"
                                        value="{{ old("JudulKonten", $editMode ? $data->JudulKonten : "") }}" required>
                                </div>

                                {{-- Author --}}
                                <div class="form-group">
                                    <label for="author">Author</label>
                                    <input type="text" class="form-control" id="author" name="Author"
                                        value="{{ old("Author", $editMode ? $data->Author : auth()->user()->name) }}"
                                        required>
                                </div>

                                {{-- No Urut --}}
                                <div class="form-group">
                                    <label for="nourut">No. Urut</label>
                                    <input type="number" class="form-control" id="nourut" name="NoUrut" min="0"
                                        value="{{ old("NoUrut", $editMode ? $data->NoUrut : "") }}" placeholder="Opsional">
                                </div>

                                {{-- Kategori --}}
                                <div class="form-group">
                                    <label for="IDKatKonten">Kategori Artikel</label>
                                    <select class="form-control" id="IDKatKonten" name="IDKatKonten" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategori_konten as $kat)
                                            <option value="{{ $kat->IDKatKonten }}"
                                                {{ old("IDKatKonten", $editMode ? $data->IDKatKonten : "") == $kat->IDKatKonten ? "selected" : "" }}>
                                                {{ $kat->NamaKategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Gambar --}}
                                <div class="form-group">
                                    <label for="gambar">Gambar Artikel</label>

                                    @if ($editMode && !empty($data->GambarKonten))
                                        <input type="file" class="dropify" id="gambar" name="GambarKonten"
                                            data-allowed-file-extensions="jpg jpeg png gif" data-max-file-size="2M"
                                            data-default-file="{{ asset("assets/konten/" . $data->GambarKonten) }}">
                                        {{-- <small class="form-text text-muted">
                                            Biarkan kosong jika tidak ingin mengubah gambar.
                                        </small> --}}
                                    @else
                                        <input type="file" class="dropify" id="gambar" name="GambarKonten"
                                            data-allowed-file-extensions="jpg jpeg png gif" data-max-file-size="2M"
                                            {{ $editMode ? "" : "required" }}>
                                    @endif

                                    <small class="form-text text-muted">
                                        Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                                    </small>
                                </div>

                                {{-- Isi --}}
                                <div class="form-group">
                                    <label for="isi">Isi Artikel</label>
                                    <textarea class="form-control summernote" id="isi" name="IsiKonten" rows="10" required>{{ old("IsiKonten", $editMode ? $data->IsiKonten : "") }}</textarea>
                                </div>

                                {{-- Button --}}
                                <div class="mt-4 text-right">
                                    <button type="submit" class="btn btn-primary" id="btn-submit">
                                        {{ $editMode ? "Update" : "Simpan" }}
                                    </button>

                                    <a href="{{ route("admin.artikel.index") }}" class="btn btn-light">
                                        Batal
                                    </a>

                                    <button class="btn btn-primary loading d-none" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm"></span>
                                        Loading...
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote.min.js"
        integrity="sha512-07bR+AJ2enmNU5RDrZkqMfVq06mQHgFxcmWN/hNSNY4E5SgYNOmTVqo/HCzrSxBhWU8mx3WB3ZJOixA9cRnCdA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                showRemove: false,
                messages: {
                    default: 'Drag dan drop gambar di sini atau klik',
                    replace: 'Drag dan drop atau klik untuk mengganti gambar',
                    remove: 'Hapus',
                    error: 'Oops, terjadi kesalahan.'
                }
            });

            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('.dropify-clear').on('click', function() {
                $('.dropify-preview').hide();
                $('.dropify-render').hide();
                $('.dropify-filename-inner').text('');
                $('#is_remove').val(1);
            });

            $('#form-artikel').submit(function(e) {
                e.preventDefault();
                $('#btn-submit').addClass('d-none');
                $('.loading').removeClass('d-none');
                var formData = new FormData(this);
                if (!formData.has('IsRekomendasi')) {
                    formData.append('IsRekomendasi', '0');
                }

                $.ajax({
                    url: `${baseURL}admin/artikel/simpan`,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("Form submission response:", response);

                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response.status) {
                                showAlert('success', response.message);
                                if (response.redirect) {
                                    setTimeout(function() {
                                        window.location.href = response.redirect;
                                    }, 1500);
                                }
                            } else {
                                showAlert('danger', response.message);
                                $('#btn-submit').removeClass('d-none');
                                $('.loading').addClass('d-none');
                            }
                        } catch (e) {
                            console.error("Error parsing response:", e);
                            showAlert('danger', 'Terjadi kesalahan pada sistem');
                            $('#btn-submit').removeClass('d-none');
                            $('.loading').addClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        showAlert('danger', 'Gagal mengirim data. Silakan coba lagi.');
                        $('#btn-submit').removeClass('d-none');
                        $('.loading').addClass('d-none');
                    }
                });
            });

            function showAlert(type, message) {
                $('#alert-message').removeClass().addClass('alert alert-' + type).html(message);
                $('#alert-container').show();
                $('html, body').animate({
                    scrollTop: $("#alert-container").offset().top - 100
                }, 200);
            }
        });
    </script>
@endpush
