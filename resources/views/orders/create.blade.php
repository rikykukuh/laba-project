@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Add Order')

@section('layout_css')
    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css" />
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload.css') }}" />
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload-ui.css') }}" />
@endsection
@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('orders.index') }}" class="link_menu_page">
			<i class="fa fa-shopping-basket"></i> Orders
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12" id="alert-container"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="customer">Customer: <small class="text-danger">*</small></label>
                <select class="form-control" id="customer" name="customer" required></select>
            </div>
        </div>
        <div class="col-md-2">
            <!-- Trigger the modal with a button -->
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-customer" style="margin: 25px auto;border-left: 1px solid #ccc;">Add Customer</button>
            <!-- Modal -->
            <div id="modal-add-customer" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <form id="add-customer-form" action="{{ route('clients.store') }}" method="post" onsubmit="saveCustomer(event)">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Customer</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Name: <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone: <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <textarea class="form-control" id="address" name="address"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" class="btn btn-danger pull-left" onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset Form</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()" style="margin-right: 15px;">Close Form</button>
                                <button type="submit" class="btn btn-primary">Submit Form</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="site_id">Site: <small class="text-danger">*</small></label>
                <select class="form-control" id="site_id" name="site_id" required>
                    <option value="bekasi">Bekasi</option>
                    <option value="depok">Depok</option>
                    <option value="bogor">Bogor</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Client -->
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail User</h3>
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
                    <h3 class="box-title">Items:</h3>
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
                                <th>Type</th>
                                <th>Note</th>
                                <th>Photo</th>
                                <th>Expense</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Modal Edit Item -->
                    <div class="modal fade" id="modal-edit-item" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <form id="edit-item-form" action="" method="get" onsubmit="editItem(event)" enctype="multipart/form-data">
                                <input type="hidden" name="item_element" id="item_element" value="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Edit Item</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_edit">Select Payment Method: <small class="text-danger">*</small></label>
                                            <select id="type_edit" class="form-control" name="type_edit">
                                                @foreach($payment_methods as $payment_method)
                                                    <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan_edit">Keterangan: <small class="text-danger">*</small></label>
                                            <textarea class="form-control" id="keterangan_edit" name="keterangan_edit"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_edit">Biaya: <small class="text-danger">*</small></label>
                                            <input type="number" class="form-control" id="biaya_edit" name="biaya_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="gambar_edit">Gambar: <small class="text-danger">*</small></label>
                                            <input type="file" class="form-control-file" id="gambar_edit" name="gambar_edit" accept=".jpg,.jpeg,.png" multiple onchange="handleImageUpload(this)" required>
                                        </div>
                                        <hr>
                                        <table role="presentation" class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th width="300">#</th>
                                                <th width="300">Image</th>
                                            </tr>
                                            </thead>
                                            <tbody class="content-image"></tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-warning">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Show Image Item -->
                    <div class="modal fade" id="modal-show-image-item" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">All Image Item</h4>
                                </div>
                                <div class="modal-body">
                                    <table role="presentation" class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th width="300">#</th>
                                            <th width="300">Image</th>
                                        </tr>
                                        </thead>
                                        <tbody class="content-image"></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Close</button>
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
                        <span>Add item</span>
                    </span>
            <!-- Modal Add Item -->
            <div class="modal fade" id="modal-add-item" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="add-item-form" action="" method="get" onsubmit="saveItem(event)" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Item</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type">Select Payment Method: <small class="text-danger">*</small></label>
                                    <select id="type" class="form-control" name="type" required>
                                        @foreach($payment_methods as $payment_method)
                                            <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan: <small class="text-danger">*</small></label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="biaya">Biaya: <small class="text-danger">*</small></label>
                                    <input type="number" class="form-control" id="biaya" name="biaya" required>
                                </div>
                                <div class="form-group">
                                    <label for="gambar">Gambar: <small class="text-danger">*</small></label>
                                    <input type="file" class="form-control-file" id="gambar" name="gambar" accept=".jpg, .jpeg, .png" multiple onchange="handleImageUpload(this)" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
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
                    <h3 class="box-title">Total Value Items</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form id="fileupload" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="customer_id" id="customer_id" value="">
                        {{ csrf_field() }}
                        <p class="margin-b-2"><b>Total: </b><span id="total"></span></p>
                        <p class="margin-b-2"><b>Down payment: </b><input type="number" id="dp" name="dp" value="" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2"><b>Kekurangan: </b><span id="kekurangan">-</span></p>
                        <p class="margin-b-2"><b>Pembayaran: </b><input type="number" name="pembayaran" value="250000" readonly class="form-control" style="display: inline"></p>
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
            {{--                         <label for="type">Payment method: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type" name="type" required> --}}
            {{--                             @foreach($payment_methods as $payment_method) --}}
            {{--                                 <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type_merchant">Payment merchant: <small class="text-danger">*</small></label> --}}
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
            <button type="submit" class="btn bg-purple pull-left" style="margin-right: 15px;">
                <i class="fa fa-fw fa-save"></i>
                <span>Simpan <!-- (Di halaman create order) --></span>
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-default pull-right"><i class="fa fa-fw fa-close"></i> Cancel</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script>
        $('#items').hide();
        $('.total-items').hide();
        const payment_methods = @json($payment_methods);
        const items = [];
        let dataFile = [];

        function getPaymentMethodById(nameKey, myArray){
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

            console.log(files);
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
        }

        function showEditItemForm(index) {
            showImageAsTable(index);
            $('#item_element').val(index);

            const dataItem = items[index];

            $(`#type_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const biaya = $('#biaya_edit').val(dataItem.biaya);
        }

        function showImageAsTable(index) {
            const contentImage = $('.content-image');
            contentImage.empty();

            items[index].gambar.forEach(function(image, index) {
                const row = `
                    <tr>
                        <th>
                            ${index + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Item Image ${index + 1}" title="Item Image ${index + 1}" class="img-thumbnail" style="height:100px">
                        </td>
                    </tr>
                `;
                contentImage.append(row);
            });
        }

        function removeItem(e, el, index) {
            if (confirm("Are you sure you want to DELETE this item?") === true) {

                items.splice(index, 1);
                renderItems();

                if (items && !items.length) {
                    $('#items').hide();
                    $('.total-items').hide();
                }

                const message = 'Remove item successfully!';
                $('.top-right').notify({
                    message: { text: `Success! ${message}` }
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
                    const message = 'Customer data saved successfully!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Success!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');

                    // Reset form
                    $('#add-customer-form').trigger("reset");

                    $('#customer').val(user_id).trigger("change");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Customer data does not saved successfully!'
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

        function renderItems() {
            const tbody = $('#table-items tbody'); // Ganti '#itemTable' dengan ID dari elemen tabel Anda
            tbody.empty(); // Kosongkan isi tabel sebelum menambahkan item baru


            // Loop melalui setiap item dan tambahkan baris HTML untuk masing-masing item
            items.forEach(function(item, index) {
                let payment_method = getPaymentMethodById(item.type, payment_methods);
                const imageSource = ''; // Tentukan sumber gambar, misalnya dari properti gambar item
                const row = `
                    <tr>
                        <th>${index + 1}</th>
                        <td>${payment_method.name}</td>
                        <td>${item.keterangan}</td>
                        <td>
                            <!--
                            <img src="${imageSource}" alt="Item Image" class="img-thumbnail" style="height:50px">
                            -->
                            <span class="btn btn-info btn-xs" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-show-image-item" onclick="showImageAsTable(${index})">
                                <i class="fa fa-image margin-r-5"></i>
                                <span>Show images</span>
                            </span>
                        </td>
                        <td>
                            <b>${item.biaya}</b>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-xs margin-r-5" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm(${index})">Edit</button>
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeItem(event, this, ${index})">Remove</button>
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
            const biaya = $('#biaya').val();
            const gambar = dataFile;

            // Buat objek item
            const newItem = { type, keterangan, biaya, gambar };

            console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items.push(newItem); // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Add item successfully!';
            $('.top-right').notify({
                message: { text: `Success! ${message}` }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-add-item').modal('hide');

            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumTotalItem();

            dataFile = [];
        }

        function editItem(event) {
            $('#items').show();
            $('.total-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            // Ambil nilai dari form
            const type = $('#type').val();
            const keterangan = $('#keterangan').val();
            const biaya = $('#biaya').val();
            const gambar = dataFile;

            // Buat objek item
            const newItem = { type, keterangan, biaya, gambar };

            console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items.push(newItem); // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Add item successfully!';
            $('.top-right').notify({
                message: { text: `Success! ${message}` }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-add-item').modal('hide');

            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            dataFile = [];
        }

        function sumTotalItem() {
            let total = 0
            for(let i = 0; i < items.length; i++) {
                total += parseInt(items[i].biaya, 10);
            }
             $('#total').text(total);
        }


        $(function() {
            $('#modal-add-customer').on('shown.bs.modal', function () {
                $('#add-customer-form #name').focus();
            });
            $('#modal-show-image-item').on('hidden.bs.modal', function () {
                $('.content-image').empty();
            });
            $('#modal-edit-item').on('hidden.bs.modal', function () {
                $('#item_element').val('');
                // $(`#type_edit option[value='1']`).attr("selected");
                $('#keterangan_edit').val('');
                $('#biaya_edit').val('');
            });
            $('#customer').select2({
                placeholder: '-- Customer --',
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
                        <strong>Customer Name:</strong> ${customerName === null ? '-' : customerName}
                         <br>
                        <strong>Address:</strong> ${address === null ? '-' : address}
                        <br>
                        <strong>Phone:</strong> ${phone === null ? '-' : phone}
                    </p>
                `);
                // status-customer
                $('#status-customer').html(`
                    <p class="text-center margin-b-10"><b>NEW</b></p>
                    <p class="text-center margin-b-2"><b>Oleh: </b> <!-- ${customerName === null ? '-' : customerName} --></p>
                    <p class="text-center margin-b-2"><b>Pada: </b><!-- ${phone === null ? '-' : phone} --></p>
                `);

                $('#customer_id').val(customerId);
            });

            $('#dp').on('input', function (e) {
                const total_expense = parseInt($('#total').text(), 10);
                const dp = parseInt($(this).val(), 10);

                const total = total_expense - dp;

                $('#kekurangan').text(total);
            });

            // $('.select2').select2();
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
