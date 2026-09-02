<script>
    function deleteSemuaItenerary() {
        const encodedIDPaket = "{{ strtr(base64_encode($paket->IDPaket), '+/=', '._-') }}";
        const encodedIDTravel = "{{ strtr(base64_encode($paket->IDTravel), '+/=', '._-') }}";

        Swal.fire({
            title: 'Hapus Semua Itinerary?',
            text: 'Apakah anda yakin akan menghapus semua data itinerary dalam paket ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.paket.hapusSemuaItenerary') }}",
                    type: 'POST',
                    data: {
                        idpaket: encodedIDPaket,
                        idtravel: encodedIDTravel,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan pada sistem.', 'error');
                    }
                });
            }
        });
    }

    const rowTemplate = $('#table-itenerary tbody tr:first-child')[0].outerHTML;

    function addRow() {
        $('#table-itenerary tbody').append(rowTemplate);
        orderNumber();
    }

    function orderNumber() {
        let no = 1;
        $('#table-itenerary tbody tr').each(function() {
            $(this).find('.no').text(no);
            no++;
        });
    }

    function removeRow(_this) {
        console.log($(_this).closest('tr'));
        if ($('#table-itenerary tbody tr').length < 1) {
            return;
        }
        $(_this).closest('tr').remove();
        orderNumber();
    }

    // Auto-load first day data on page load
    $(document).ready(function() {
        // Select the first day option
        const firstOption = $('#HariKe option[value="1"]');
        if (firstOption.length > 0) {
            $('#HariKe').val('1').trigger('change');
        }
    });

    $('#HariKe').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        let tanggal = selectedOption.data('tanggal');
        $('#Tanggal').val(tanggal);
        getItenerary(selectedOption.val());
    });

    $("#form-default").submit(function(e) {
        e.preventDefault();

        const encodedIDPaket = "{{ strtr(base64_encode($paket->IDPaket), '+/=', '._-') }}";
        const encodedIDTravel = "{{ strtr(base64_encode($paket->IDTravel), '+/=', '._-') }}";

        const url = `${baseURL}admin/Paket/simpan_itenerary/${encodedIDPaket}/${encodedIDTravel}`;
        const formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function(res) {
                res = typeof res === 'string' ? JSON.parse(res) : res;

                if (res.status) {
                    window.location.href = res.redirect;
                    toast('success', res.message);
                } else {
                    toast('error', res.message);
                }
            },
            error: function() {
                toast('error', 'Sistem Error');
            }
        });
    });

    $('#form-import-itenerary').submit(function(e) {
        e.preventDefault();

        const encodedIDPaket = "{{ strtr(base64_encode($paket->IDPaket), '+/=', '._-') }}";
        const encodedIDTravel = "{{ strtr(base64_encode($paket->IDTravel), '+/=', '._-') }}";
        const url = `${baseURL}admin/Paket/import_itenerary/${encodedIDPaket}/${encodedIDTravel}`;

        const formData = new FormData(this);
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.text();

        btn.prop('disabled', true).text('Mengimport...');
        $('#import-itenerary-result').addClass('d-none');
        $('#import-itenerary-failed-list').empty();
        $('#import-itenerary-failed-container').addClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(res) {
                res = typeof res === 'string' ? JSON.parse(res) : res;
                btn.prop('disabled', false).text(originalText);

                if (res.status) {
                    $('#import-itenerary-result').removeClass('d-none');
                    $('#import-itenerary-success-count').text(res.success_count || 0);

                    if (res.failed && res.failed.length > 0) {
                        $('#import-itenerary-failed-container').removeClass('d-none');
                        res.failed.forEach(item => {
                            $('#import-itenerary-failed-list').append(
                                `<li class="list-group-item text-danger">Baris ${item.row}: ${item.reason}</li>`
                            );
                        });
                    } else {
                        toast('success', res.message || 'Import berhasil');
                    }

                    setTimeout(() => {
                        location.reload();
                    }, 800);
                } else {
                    toast('error', res.message || 'Import gagal');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text(originalText);
                toast('error', xhr.responseJSON?.message || 'Terjadi kesalahan saat import');
            }
        });
    });

    function getItenerary(hariKe) {
        const encodedIDPaket = "{{ strtr(base64_encode($paket->IDPaket), '+/=', '._-') }}";
        const encodedIDTravel = "{{ strtr(base64_encode($paket->IDTravel), '+/=', '._-') }}";

        const url = `${baseURL}admin/Paket/get_itenerary_hari/${encodedIDPaket}/${encodedIDTravel}/${hariKe}`;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#table-itenerary tbody').html('');

                if (res.status && res.data && res.data.length > 0) {
                    res.data.forEach(el => {
                        let draw = `
                            <tr>
                                <td class="text-center"><span class="no">1</span></td>
                                <td>
                                    <input type="time" name="TglMulai[]" class="form-control" value="${el.TglMulai}" required>
                                </td>
                                <td>
                                    <input type="time" name="TglSelesai[]" class="form-control" value="${el.TglSelesai}" required>
                                </td>
                                <td>
                                    <input type="text" name="Agenda[]" class="form-control" value="${el.Agenda}" required>
                                </td>
                                <td>
                                    <input type="text" name="Lokasi[]" class="form-control" value="${el.Lokasi}" required>
                                </td>
                                <td style="width:10%">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#table-itenerary tbody').append(draw);
                    });
                    orderNumber();
                } else {
                    // Show message if no data exists
                    let noDataRow = `
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fa fa-info-circle"></i> 
                                    Belum ada data itenerary untuk Hari Ke ${hariKe}. 
                                    Silakan tambah data baru atau import dari Excel.
                                </td>
                            </tr>
                        `;
                    $('#table-itenerary tbody').append(noDataRow);
                    // Also add one empty row for new input
                    addRow();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading itenerary:', error);
                $('#table-itenerary tbody').html(`
                        <tr>
                            <td colspan="6" class="text-center text-danger py-3">
                                <i class="fa fa-exclamation-triangle"></i> 
                                Gagal memuat data. Silakan refresh halaman.
                            </td>
                        </tr>
                    `);
                addRow();
            }
        });
    }
</script>
