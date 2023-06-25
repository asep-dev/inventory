<div class="layout-page">
    <?php $this->load->view('layouts/admin/topbar'); ?>
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Breadcrumb -->
        <div class="container-breadcrumb my-2">
            <h3 class="fw-bold mb-1">Users</h3>
            <nav aria-label="breadcrumb mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/dashboard') ?>">Beranda</a>
                    </li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <!-- End Breadcrumb -->

        <div class="row">
            <div class="col-12 mt-4">

                <div class="card p-2 pt-3 p-md-3">
                    <div class="card-header p-0 pb-3 mb-3 d-flex align-items-center justify-content-between border-bottom">
                        <h5 class="mb-0 ms-2">Users</h5>
                        <button id="action-add" type="button" class="btn btn-sm btn-primary">
                            <span class="tf-icons bx bx-plus"></span>
                            Users
                        </button>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table id="table" class="table dataTable table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>type</th>
                                    <th>Email</th>
                                    <th>No. Kontak</th>
                                    <th>Izin login?</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true" style="z-index:99999;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="form" class="modal-content">
            <div class="modal-header">
                <div class="head-title w-75">
                    <h5 id="title" class="mb-0"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notif" style="display:none;"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="ex. Asep supian tsaori">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. tlp / wa</label>
                        <input type="text" class="form-control" onkeyup="this.value.replace(/^[0-9]*$/, '');" name="contact" placeholder="ex. 0812345678910">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="type">
                            <option value="" hidden>Choose..</option>
                            <option value="administrator">Administrator</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Izin Login ? <span class="text-danger">*</span></label>
                        <select class="form-select" name="is_login">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="is_active">
                            <option value="0">Tidak</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="target">
                <button type="button" class="btn btn-soft-dark" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" id="btn-save">Tambah</button>
            </div>
        </form>
    </div>
</div>

<script async src="<?= base_url() ?>public/back/assets/vendor/libs/sweetalert/sweetalert2.js"></script>
<script src="<?= base_url() ?>public/back/assets/vendor/libs/datatables/dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        const csrf = $('meta[name="csrf_token"')
        const table = $('#table')
        const form = $('#form')
        const modalTitle = $('#title')
        const btnSave = $('#btn-save')
        const notif = $('#notif')
        let saveAction;

        const modal = document.getElementById('modal')
        const myModal = new bootstrap.Modal(modal)

        table.DataTable({
            dom: '<"row"<"col-sm-6 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            drawCallback: function() {
                $('#example_paginate').addClass('pagination align-items-center justify-content-end');
                $('select').addClass('form-select')
                $('select').css('padding', '0.3rem 1.6rem 0.3rem 0.875rem')
                $('input[type="search"]').addClass('form-control')
            },
            language: {
                search: "",
                searchPlaceholder: "Search",
                paginate: {
                    previous: '←',
                    next: '→'
                },
                processing: `<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`
            },
            responsive: true,
            scrollX: true,
            processing: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: '<?= base_url() ?>admin/users',
                type: 'POST',
                data: function(e) {
                    e.csrf_token = csrf.attr('content');
                },
                dataSrc: function(e) {
                    csrf.attr('content', e.csrf_hash)
                    return e.data
                }
            },
            columnDefs: [{
                target: [6],
                orderable: false,
            }]
        });

        $(document).on('click', '#action-add', function(e) {
            formModal('add', 'Tambah user', 'Tambah')
        })

        $(document).on('click', '.edit', function(e) {
            formModal('edit', 'Edit user', 'Simpan')
            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>admin/users/find/" + $(this).data('id'),
                data: "csrf_token=" + csrf.attr('content'),
                dataType: "JSON",
                success: function(res) {
                    csrf.attr('content', res.csrf_hash)
                    console.log(res.data)
                    if (res.success == "true") {
                        $('input[name="target"]').val(res.data.id)
                        $('input[name="name"]').val(res.data.name)
                        $('input[name="email"]').val(res.data.email)
                        $('input[name="contact"]').val(res.data.contact)
                        $('select[name="type"]').val(res.data.role).change()
                        $('select[name="is_login"]').val(res.data.login).change()
                        $('select[name="is_active"]').val(res.data.active).change()
                    }

                },
                error: function(xhr, status) {
                    console.log(xhr.responseText)
                }
            });
        })

        $(document).on('click', '.delete', function(e) {
            Swal.fire({
                html: `<span class="swalfire bg-soft-danger my-3">
                        <div class="swalfire-icon">
                            <i class='bx bx-trash text-danger'></i>
                        </div>
                    </span>
                    <div>
                        <h5 class="text-dark">Hapus</h5>
                        <p class="fs-6 mt-2">Anda yakin ingin menghapus pengguna ini?</p>
                    </div>`,
                customClass: {
                    content: 'p-3 text-center',
                    actions: 'justify-content-end mt-1 p-0',
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-soft-dark me-2'
                },
                width: 300,
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                buttonsStyling: false
            }).then((e) => {
                if (e.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url() ?>admin/users/action/delete",
                        data: "target=" + $(this).data('id') + "&csrf_token=" + csrf.attr('content'),
                        dataType: "JSON",
                        success: function(res) {
                            notifyAjax(res)
                        }
                    });
                }
            })
        })

        btnSave.click(function(e) {
            e.preventDefault();

            if (saveAction == "add") {
                var url = "<?= base_url() ?>admin/users/action/add"
            } else if (saveAction == "edit") {
                var url = "<?= base_url() ?>admin/users/action/edit"
            }

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize() + "&csrf_token=" + csrf.attr('content'),
                dataType: "JSON",
                success: function(res) {
                    notifyAjax(res)
                }
            });

        });

        function reloadTable() {
            table.DataTable().ajax.reload();
        }

        function formModal(action, title, btnText) {
            saveAction = action
            modalTitle.text(title)
            btnSave.text(btnText)
            notif.html('')
            myModal.show()
            form[0].reset()
        }

        function notifyAjax(res) {
            if (res.errors) {
                $('#notif').html(res.message).show()
            }
            if (res.error) {
                show_toast('Mohon maaf', res.message)
            }
            if (res.success) {
                show_toast('Berhasil', res.message)
                setTimeout(() => {
                    myModal.hide()
                }, 1000);

                setTimeout(() => {
                    reloadTable()
                }, 1300);
            }
            csrf.attr('content', res.csrf_hash)
        }
    });
</script>