@extends('layouts.master')

@section('content')
<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form id="form-default" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="form-group col-md-12">
                                    <label>Gambar</label>
                                    <input type="file" class="dropify" id="slider" name="slider"
                                        data-show-remove="false"
                                        data-max-file-size="2M"
                                        data-default-file="{{ isset($banner['GambarKonten']) && $banner['GambarKonten'] != '' ? asset('assets/konten/'.$banner['GambarKonten']) : '' }}"
                                        data-allowed-file-extensions="jpeg jpg png">

                                    <!-- Modal Cropper -->
                                    <div class="modal fade" id="modal_cropper_slider" role="dialog" aria-labelledby="modalLabel"
                                        aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                                                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body p-0" style="max-height: 60vh; overflow: auto;">
                                                    <img id="imageCropping_slider" src="">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary modal-hide" data-dismiss="modal">Close</button>
                                                    <button type="button" id="cropButton_slider" class="btn btn-primary">Crop & Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="text-right">
                                <input type="hidden" name="id" value="{{ isset($banner['IDKonten']) ? base64_encode($banner['IDKonten']) : '' }}">
                                <input type="hidden" name="old_Gambar" value="{{ $banner['GambarKonten'] ?? '' }}">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary mr-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div><!-- content-wrapper ends -->
</div><!-- main-panel ends -->

@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
@endpush

@endsection
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

<script>
$(document).ready(function() {
    $('.dropify').dropify();

    let cropper = null;
    let imageCropping = $('#imageCropping_slider')[0];
    let croppedImageName = '';
    let croppedImageFile = null;

    $('#slider').on('change', function(e) {
        let files = e.target.files;
        if (files && files.length > 0) {
            let file = files[0];
            croppedImageName = file.name;
            let reader = new FileReader();
            reader.onload = function(event) {
                imageCropping.src = event.target.result;

                imageCropping.onload = function() {
                    const width = this.width;
                    const height = this.height;
                    if (width > height) {
                        $('#modal_cropper_slider').modal('show');
                    }
                };
            };
            reader.readAsDataURL(file);
        }
    });

    $('#modal_cropper_slider').on('shown.bs.modal', function() {
        cropper = new Cropper(imageCropping, {
            viewMode: 1,
            aspectRatio: 18 / 9,
        });
    }).on('hidden.bs.modal', function() {
        if(cropper) cropper.destroy();
        cropper = null;
    });

    $("#cropButton_slider").click(function() {
        if (cropper) {
            cropper.getCroppedCanvas({
                width: 800,
                height: 450,
            }).toBlob(function(blob) {
                let croppedImageFile = new File([blob], croppedImageName, {
                    type: 'image/png'
                });

                let reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    let base64data = reader.result;

                    let dropifyElement = $('#slider').data('dropify');
                    dropifyElement.resetPreview();
                    dropifyElement.clearElement();
                    dropifyElement.settings.defaultFile = base64data;

                    dropifyElement.destroy();
                    $('#slider').dropify({
                        defaultFile: base64data
                    }).data('dropify').init();

                    let inputFile = document.getElementById('slider');
                    let dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedImageFile);
                    inputFile.files = dataTransfer.files;

                    resetPreview('slider', base64data, croppedImageName);

                    $('#modal_cropper_slider').modal('hide');
                };
            }, 'image/png');
        }
    });

    function resetPreview(name, src, fname = '') {
        let input = $('input[name="' + name + '"]');
        let wrapper = input.closest('.dropify-wrapper');
        let preview = wrapper.find('.dropify-preview');
        let filename = wrapper.find('.dropify-filename-inner');
        let render = wrapper.find('.dropify-render').html('');

        input.attr('title', fname);
        wrapper.removeClass('has-error').addClass('has-preview');
        filename.html(fname);
        render.append($('<img />').attr('src', src).css('max-height', input.data('height') || ''));
        preview.fadeIn();
    }

    function deleteImage(id) {
        $('#' + id).dropify().clearElement();
        $('input[name="slider"]').val('');
    }

    $('.modal-hide').click(function() {
        $('#slider').val('');
    });

    // AJAX submit form
    $("#form-default").submit(function(e) {
        e.preventDefault();
        const formData = new FormData($("#form-default")[0]);
        $.ajax({
            url: "{{ url('admin/Slider/simpan') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            processData: false,
            contentType: false,
            data: formData,
            success: function(res) {

                if (res.status) {
                    toast('success', res.message);
                    window.location.href = res.redirect;
                } else {
                    toast('error', res.message);
                }
            },
            error: function(xhr, status, error) {
                toast('error', 'Sistem Error');
            }
        });
    });
});
</script>
@endpush
