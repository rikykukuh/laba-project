@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Tambah Pesanan')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('orders.index') }}" class="link_menu_page">
			<i class="fa fa-shopping-basket"></i> Pesanan
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12" id="alert-container"></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="customer">Pelanggan: <small class="text-danger">*</small></label>
                        <select class="form-control" id="customer" name="customer" required></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Trigger the modal with a button -->
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-customer" style="margin: 25px auto;border-left: 1px solid #ccc;">Tambah Pelanggan</button>
                    <!-- Modal -->
                    <div id="modal-add-customer" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-md">
                            <!-- Modal content-->
                            <form id="add-customer-form" action="{{ route('clients.store') }}" method="post" onsubmit="saveCustomer(event)" autocomplete="off">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Tambah Pelanggan</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Nama: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">No Telepon: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="phone" name="phone" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Alamat:</label>
                                            <textarea class="form-control" id="address" name="address"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="reset" class="btn btn-danger pull-left" onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()" style="margin-right: 15px;">Batalkan</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="site_id">Cabang: <small class="text-danger">*</small></label>
                <select class="form-control select2" id="site_id" name="site_id" required>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Client -->
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail Pelanggan</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" id="detail-user">
                </div>
            </div>
        </div>

        <!-- Status Client -->
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Status</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" id="status-customer">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" id="items">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Barang:</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive no-padding">
                        <table class="table table-hover" id="table-items">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Foto</th>
                                    <th>Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Foto</th>
                                    <th>Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Modal Edit Item -->
                    <div class="modal fade" id="modal-edit-item" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <form id="edit-item-form" action="" method="get" onsubmit="formEditItem(event)" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="item_element" id="item_element" value="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Edit Item</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_edit">Jenis Barang: <small class="text-danger">*</small></label>
                                            <select id="type_edit" class="form-control" name="type_edit">
                                                @foreach($item_types as $item_type)
                                                    <option value="{{ $item_type->id }}">{{ $item_type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan_edit">Keterangan: <small class="text-danger">*</small></label>
                                            <textarea class="form-control" id="keterangan_edit" name="keterangan_edit"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_edit">Biaya: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="biaya_edit" name="biaya_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="gambar_edit">Foto: <small class="text-danger">*</small></label>
                                            <input type="file" class="form-control-file" id="gambar_edit" name="gambar_edit" accept=".jpg,.jpeg,.png" multiple onchange="handleEditImageUpload(this)">
                                        </div>
                                        <hr>
                                        <table role="presentation" class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th width="300">#</th>
                                                <th width="300">Foto</th>
                                            </tr>
                                            </thead>
                                            <tbody class="content-image"></tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Batalkan</button>
                                        <button type="submit" class="btn btn-warning">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Show Image Item -->
                    <div class="modal fade" id="modal-show-image-item" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Item Foto</h4>
                                </div>
                                <div class="modal-body">
                                    <table role="presentation" class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th width="300">#</th>
                                            <th width="300">Foto</th>
                                        </tr>
                                        </thead>
                                        <tbody class="content-image"></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-bottom: 15px;">
            <span class="btn btn-success" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-add-item">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Tambah</span>
                    </span>
            <!-- Modal Add Item -->
            <div class="modal fade" id="modal-add-item" role="dialog" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="add-item-form" action="" method="get" onsubmit="saveItem(event)" enctype="multipart/form-data" autocomplete="off">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Tambah Barang</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type">Jenis Barang: <small class="text-danger">*</small></label>
                                    <select id="type" class="form-control" name="type" required>
                                        @foreach($item_types as $item_type)
                                            <option value="{{ $item_type->id }}">{{ $item_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan: <small class="text-danger">*</small></label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="biaya">Biaya: <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="biaya" name="biaya" required>
                                </div>
                                <div class="form-group">
                                    <label for="gambar">Foto: <small class="text-danger">*</small></label>
                                    <input type="file" class="form-control-file" id="gambar" name="gambar" accept=".jpg, .jpeg, .png" multiple onchange="handleImageUpload(this)" required>
                                </div>
                                <hr>
                                <table role="presentation" class="table table-striped table-bordered table-hover list-image">
                                    <thead>
                                    <tr>
                                        <th width="300">#</th>
                                        <th width="300">Foto</th>
                                    </tr>
                                    </thead>
                                    <tbody id="list-image"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 total-items">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Nilai</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" value="">
                        {{ csrf_field() }}
                        <p class="margin-b-2"><b>Total: </b><span id="total"></span></p>
                        <p class="margin-b-2"><b>Uang muka: </b><input type="text" id="dp" name="dp" value="" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2"><b>Kekurangan: </b><span id="kekurangan">-</span></p>
                        <input type="hidden" id="kekurangan-final" name="kekurangan" class="form-control" style="display: inline">
                        {{-- <p class="margin-b-2"><b>Pembayaran: </b><input type="text" id="pembayaran" name="pembayaran" value="0" readonly class="form-control" style="display: inline"></p> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 total-items">
            {{--  <button type="submit" class="btn bg-olive pull-left" data-toggle="modal" data-target="#modal-take" style="margin-right: 15px;"> --}}
            {{--      <i class="fa fa-fw fa-save"></i> --}}
            {{--      <span>Ambil</span> --}}
            {{--  </button> --}}
            {{-- <!-- Modal --> --}}
            {{-- <div id="modal-take" class="modal fade" role="dialog"> --}}
            {{--     <div class="modal-dialog modal-lg"> --}}
            {{--         <!-- Modal content--> --}}
            {{--         <form id="add-customer-form" action="{{ route('clients.store') }}" method="post" onsubmit="saveCustomer(event)"> --}}
            {{--             <div class="modal-content"> --}}
            {{--                 <div class="modal-header"> --}}
            {{--                     <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
            {{--                     <h4 class="modal-title">Ambil</h4> --}}
            {{--                 </div> --}}
            {{--                 <div class="modal-body"> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type">Metode Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type" name="type" required> --}}
            {{--                             @foreach($item_types as $item_type) --}}
            {{--                                 <option value="{{ $item_type->id }}">{{ $item_type->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type_merchant">Penyedia Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type_merchant" name="type_merchant" required> --}}
            {{--                             @foreach($payment_merchants as $payment_merchant) --}}
            {{--                                 <option value="{{ $payment_merchant->id }}">{{ $payment_merchant->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="kekurangan">Kekurangan: <small class="text-danger">*</small></label> --}}
            {{--                         <input type="text" class="form-control" id="kekurangan" name="kekurangan" required> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="diambil">Dimbail oleh: <small class="text-danger">*</small></label> --}}
            {{--                         <input type="text" class="form-control" id="diambil" name="diambil" required> --}}
            {{--                     </div> --}}
            {{--                 </div> --}}
            {{--                 <div class="modal-footer"> --}}
            {{--                      --}}{{-- <button type="reset" class="btn btn-danger pull-left" onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset Form</button> --}}
            {{--                      --}}{{-- <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()" style="margin-right: 15px;">Close Form</button> --}}
            {{--                      --}}{{-- <button type="submit" class="btn btn-primary">Submit Form</button> --}}
            {{--                     <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-right: 15px;">Close Form</button> --}}
            {{--                     <button type="button" class="btn btn-primary" data-dismiss="modal" style="margin-right: 15px;">Simpan</button> --}}
            {{--                 </div> --}}
            {{--             </div> --}}
            {{--         </form> --}}
            {{--     </div> --}}
            {{-- </div> --}}
            <button type="button" class="btn bg-purple pull-left" id="btn-order" style="margin-right: 15px;" onclick="createOrder()">
                <i class="fa fa-fw fa-save"></i>
                <span>Simpan</span>
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-default pull-left"><i class="fa fa-fw fa-close"></i> Batalkan</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script>
        $('#items').hide();
        $('.total-items').hide();
        $('.list-image').hide();
        const item_types = @json($item_types);
        const items = [];
        let dataFile = [];

        function getTypeById(nameKey, myArray){
            for (let i=0; i < myArray.length; i++) {
                if (myArray[i].id === parseFloat(nameKey, 10)) {
                    return myArray[i];
                }
            }
        }

        function uuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                let r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function handleImageUpload(element) {
            const files = $(element)[0].files;
            if(files.length === 0) return;

            // console.log(files);
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        // Mengakses data URL base64 dari file
                        const base64Data = e.target.result;
                        // Memasukkan data URL base64 ke dalam array
                        dataFile.push(base64Data);
                    };
                    // Membaca file sebagai data URL
                    reader.readAsDataURL(file);
                }
            }

            setTimeout(() => {
                renderListImage();
            }, 500);
        }

        function handleEditImageUpload(element) {
            const files = $(element)[0].files;
            if(files.length === 0) return;

            // console.log(files);
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        // Mengakses data URL base64 dari file
                        const base64Data = e.target.result;
                        // Memasukkan data URL base64 ke dalam array
                        dataFile.push(base64Data);
                    };
                    // Membaca file sebagai data URL
                    reader.readAsDataURL(file);
                }
            }

            setTimeout(() => {
                const contentImage = $('.content-image');
                contentImage.empty();

                dataFile.forEach(function(image, index) {
                    const row = `
                    <tr>
                        <th>
                            ${index + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${index + 1}" title="Foto Barang ${index + 1}" class="img-thumbnail" style="height:100px">
                        </td>
                    </tr>
                `;
                    contentImage.append(row);
                });
            }, 500);
        }

        function renderListImage() {
            // console.log(dataFile);
            const listImage = $('#list-image');
            $('.list-image').hide();
            $('.list-image').show();
            listImage.empty();
            for(let i = 0; i < dataFile.length; i++) {
                listImage.append(`
                    <tr>
                        <th>
                            ${i + 1}
                        </th>
                        <td class="text-center">
                            <img src="${dataFile[i]}" alt="Foto Barang ${i + 1}" title="Foto Barang ${i + 1}" class="img-thumbnail" style="height:100px">
                        </td>
                    </tr>
                `);
            }
        }

        function showEditItemForm(index) {
            showImageAsTable(index);
            $('#item_element').val(index);

            const dataItem = items[index];

            $(`#type_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const biaya = $('#biaya_edit').val(parseInt(dataItem.biaya, 10).toLocaleString('id-ID'));
        }

        function showImageAsTable(index) {
            const contentImage = $('.content-image');
            // console.log(items)
            contentImage.empty();

            items[index].gambar.forEach(function(image, index) {
                const row = `
                    <tr>
                        <th>
                            ${index + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${index + 1}" title="Foto Barang ${index + 1}" class="img-thumbnail" style="height:100px">
                        </td>
                    </tr>
                `;
                contentImage.append(row);
                dataFile.push(image);
            });
        }

        function removeItem(e, el, index) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS item ini?") === true) {

                items.splice(index, 1);
                renderItems();

                if (items && !items.length) {
                    $('#items').hide();
                    $('.total-items').hide();
                }

                const message = 'Item berhasil dihapus';
                $('.top-right').notify({
                    message: { text: `Sukses! ${message}` }
                }).show();

            }
        }

        function saveCustomer(event) {
            event.preventDefault();
            $.ajax({
                url: $('#add-customer-form').attr('action'),
                method: 'POST',
                data: $('#add-customer-form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    console.log(response);
                    const user_id = response.id;
                    const type = 'success';
                    const message = 'Pelanggan berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');

                    $('#customer').val(user_id).trigger('change');

                    resetFormAddItem();
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Pelanggan tidak bisa disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');
                }
            });
        }

        function resetFormAddItem() {
            const listImage = $('#list-image');
            listImage.empty();
            $('.list-image').hide();
            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            dataFile = [];
        }

        function renderItems() {
            const tbody = $('#table-items tbody'); // Ganti '#itemTable' dengan ID dari elemen tabel Anda
            tbody.empty(); // Kosongkan isi tabel sebelum menambahkan item baru


            // Loop melalui setiap item dan tambahkan baris HTML untuk masing-masing item
            items.forEach(function(item, index) {
                let type = getTypeById(item.type, item_types);
                const imageSource = ''; // Tentukan sumber gambar, misalnya dari properti gambar item
                const row = `
                    <tr>
                        <th>${index + 1}</th>
                        <td>${type.name}</td>
                        <td>${item.keterangan}</td>
                        <td>
                            <!--
                            <img src="${imageSource}" alt="Foto Barang" class="img-thumbnail" style="height:50px">
                            -->
                            <span class="btn btn-info btn-xs" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-show-image-item" onclick="showImageAsTable(${index})">
                                <i class="fa fa-image margin-r-5"></i>
                                <span>Tampilkan Foto</span>
                            </span>
                        </td>
                        <td>
                            <b>${parseInt(item.biaya, 10).toLocaleString('id-ID')}</b>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-xs margin-r-5" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm(${index})">Edit</button>
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeItem(event, this, ${index})">Hapus</button>
                        </td>
                    </tr>
                `;
                tbody.append(row); // Tambahkan baris ke tabel
            });
        }

        function saveItem(event) {
            $('#items').show();
            $('.total-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            // Ambil nilai dari form
            const type = $('#type').val();
            const keterangan = $('#keterangan').val();
            const biaya = $('#biaya').val().replace(/\./g, '');
            const gambar = dataFile;

            // Buat objek item
            const newItem = { type, keterangan, biaya, gambar };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items.push(newItem); // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil ditambahkan!';
            $('.top-right').notify({
                message: { text: `Sukses! ${message}` }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-add-item').modal('hide');

            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumTotalItem();

            dataFile = [];
        }

        function formEditItem(event) {
            $('#items').show();
            $('.total-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            const element = $('#item_element').val();

            // Ambil nilai dari form
            const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val();
            const biaya = $('#biaya_edit').val().replace(/\./g, '');

            // Buat objek item
            const newItem = { type, keterangan, biaya, gambar: dataFile };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items[element] = newItem; // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil diedit!';
            $('.top-right').notify({
                message: { text: `Sukses! ${message}` }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-edit-item').modal('hide');

            // Reset form
            $('#edit-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumTotalItem();

            dataFile = [];
        }

        function sumTotalItem() {
            const dp = $('#dp').val();

            let total = 0;

            for(let i = 0; i < items.length; i++) {
                total += parseInt(items[i].biaya, 10);
            }

            $('#total').text(total.toLocaleString('id-ID'));
            if (dp !== '') {
                $('#kekurangan').text(parseInt(total - dp.replace(/\./g, ''), 10).toLocaleString('id-ID'));
            } else {
                $('#kekurangan').text('-');
            }
        }

        function createOrder() {
            const customer_id = $('#customer_id').val();
            const site_id = $('#site_id option:selected').val();
            const dp = $('#dp').val().replace(/\./g, '');
            const total = $('#total').text().replace(/\./g, '');
            const kekurangan = $('#kekurangan-final').val();

            // console.log('FINAL RESULT');
            // console.table([{items}, {customer_id}, {site_id}, {dp}, {total}, {kekurangan}]);

            const url = "{{ route('orders.store') }}";
            $('.alert').alert('close');

            $.ajax({
                url,
                data: {
                    items, customer_id, site_id, dp, total, kekurangan
                },
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    console.log(response);
                    const type = 'success';
                    const message = 'Order berhasil dibuat'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                        window.location.reload();
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Order tidak berhasil disimpan!'
                    const statusCode = `${xhr.statusText} (${xhr.status})`;
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message} ${statusCode}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert
                }
            });

            $('html, body').animate({
                scrollTop: $("#alert-container").offset().top - 125
            }, 1000);
        }

        $(function() {
            $('#modal-add-customer').on('shown.bs.modal', function () {
                $('#add-customer-form #name').focus();
            });
            $('#modal-show-image-item').on('hidden.bs.modal', function () {
                $('.content-image').empty();
                dataFile = [];
            });
            $('#modal-add-item').on('hidden.bs.modal', function () {
                resetFormAddItem();
            });
            $('#modal-edit-item').on('hidden.bs.modal', function () {
                $('#item_element').val('');
                // $(`#type_edit option[value='1']`).attr("selected");
                $('#keterangan_edit').val('');
                $('#biaya_edit').val('');
                dataFile = [];
            });
            $('#customer').select2({
                placeholder: '-- Pelanggan --',
                ajax: {
                    url: '{{ route("client.search") }}', // Ganti dengan URL endpoint Anda
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        const options = [];
                        $.each(data, function(index, item) {
                            options.push({
                                id: item.id,
                                text: item.name,
                                data: {
                                    address: item.address,
                                    phone: item.phone_number
                                }
                            });
                        });
                        return {
                            results: options
                        };
                    },
                    cache: true
                }
            });

            $('#customer').on('select2:select', function(e) {
                const data = e.params.data;
                const dataAttribute = data.data;

                const customerId = data.id;
                const customerName = data.text;
                const address = dataAttribute.address;
                const phone = dataAttribute.phone;

                // detail-user
                $('#detail-user').html(`
                    <p class="text-muted well well-sm no-shadow margin-b-10">
                        <strong>Nama:</strong> ${customerName === null ? '-' : customerName}
                         <br>
                        <strong>Alamat:</strong> ${address === null ? '-' : address}
                        <br>
                        <strong>Telepon:</strong> ${phone === null ? '-' : phone}
                    </p>
                `);
                // status-customer
                $('#status-customer').html(`
                    <p class="text-center margin-b-10"><b>BARU</b></p>
                    <p class="text-center margin-b-2"><b>Oleh: </b> -<!-- ${customerName === null ? '-' : customerName} --></p>
                    <p class="text-center margin-b-2"><b>Pada: </b> -<!-- ${phone === null ? '-' : phone} --></p>
                `);

                $('#customer_id').val(customerId);
            });

            $('#dp').on('input', function (e) {
                let total = $('#total').text().replace(/\./g, '');
                const total_expense = parseInt(total, 10);
                const dp = $(this).val();

                // Hapus semua karakter selain digit
                let digitsOnly = dp.replace(/\D/g, '');

                total = total_expense - parseInt(digitsOnly, 10);

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);

                const kekurangan = !isNaN(total) ? total.toLocaleString('id-ID') : '-';
                $('#kekurangan').text(kekurangan);
                $('#kekurangan-final').val(kekurangan.replace(/\D/g, ''));
            });

            $('#biaya, #biaya_edit').on('input', function (e) {
                const value = $(this).val();

                // Hapus semua karakter selain digit
                let digitsOnly = value.replace(/\D/g, '');

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);
            });

            $('#site_id').select2();
            // $('.select2').select2({
            //     "language": {
            //         "noResults": function(){
            //             return "Nenhum registro encontrado.";
            //         }
            //     }
            // });

        });

    </script>

@endsection
