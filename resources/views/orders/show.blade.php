@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Pesanan')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('orders.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Pesanan
        </a>
    </li>

@endsection


{{--@section('content')--}}

{{--    <div class="col-md-6 col-md-offset-3">--}}

{{--        <div class="box box-primary">--}}
{{--            <div class="box-header with-border">--}}
{{--                <h3 class="box-title">Detail Order</h3>--}}
{{--            </div>--}}

{{--            <div class="box-body">--}}
{{--                <strong><i class="fa fa-book margin-r-5"></i> Name</strong>--}}
{{--                <p>{{ $order->name }}</p>--}}
{{--                <hr>--}}
{{--                <strong><i class="fa fa-user-circle-o margin-r-5"></i> Client Name</strong>--}}
{{--                <p>{{ $order->client->name }}</p>--}}
{{--                <hr>--}}
{{--                <strong><i class="fa fa-file-text-o margin-r-5"></i> Status</strong>--}}
{{--                <p>{{ $statuses[$order->status] }}</p>--}}
{{--                <hr>--}}
{{--                <strong><i class="fa fa-calendar-o margin-r-5"></i> Status</strong>--}}
{{--                <p>{{ $order->due_date }}</p>--}}
{{--                <hr>--}}
{{--                <img src="{{ $order }}" alt="">--}}
{{--            </div>--}}

{{--            <div class="box-footer with-border">--}}
{{--                <a href="{{ route('orders.index') }}" class="btn btn-default pull-left" style="margin-right: 10px;">Back to Orders</a>--}}
{{--                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning pull-left" style="margin-right: 10px;">Edit Order</a>--}}
{{--                <form onsubmit="return confirm('Apakah Anda benar-benar ingin MENGHAPUS?');" action="{{ route('orders.destroy', $order->id) }}" method="post" style="display: inline-block">--}}
{{--                    @csrf--}}
{{--                    @method('DELETE')--}}
{{--                    <button type="submit" class="btn btn-danger pull-left" style="margin-right: 10px;">Delete Order</button>--}}
{{--                </form>--}}
{{--            </div>--}}

{{--        </div>--}}

{{--    </div>--}}

{{--@endsection--}}

@section('layout_css')
    <link href="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.css') }}" rel="stylesheet">
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
                    <button type="button" class="btn btn-info" id="btn-add-customer" data-toggle="modal" data-target="#modal-add-customer" style="margin: 25px auto;border-left: 1px solid #ccc;">Tambah Pelanggan</button>
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
                <select class="form-control" id="site_id" name="site_id" required>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ $order->site_id === $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
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
                    <p class="text-muted well well-sm no-shadow margin-b-10">
                        <strong>Nama:</strong> {{ $order->client->name ?? '-' }}
                        <br>
                        <strong>Alamat:</strong> {{ $order->client->address ?? '-' }}
                        <br>
                        <strong>Telepon:</strong> {{ $order->client->phone_number ?? '-' }}
                    </p>
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
                    <p class="text-center margin-b-10"><b id="status">{{ $order->status }}</b></p>
                    <p class="text-center margin-b-2"><b>Oleh: </b> <span id="oleh">{{ $order->picked_by ?? '-' }}</span></p>
                    <p class="text-center margin-b-2"><b>Pada: </b> <span id="pada">{{ $order->picked_at ?? '-' }}</span></p>
                    <p class="text-center margin-b-2"><b>Tanggal Transaksi: </b> <span id="oleh">{{ $order->created_at ?? '-' }}</span></p>
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
                            <tbody>
                                @foreach($order->orderItems as $orderItem)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $item_types->find($orderItem->item_type_id)->name }}</td>
                                        <td>{{ $orderItem->note }}</td>
                                        <td>
                                            <!--
                                            <img src="${imageSource}" alt="Foto Barang" class="img-thumbnail" style="height:50px">
                                            -->
                                            <span class="btn btn-info btn-xs" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-show-image-item" onclick="showImageAsTable({{ $loop->iteration - 1 }})">
                                                <i class="fa fa-image margin-r-5"></i>
                                                <span>Tampilkan Foto</span>
                                            </span>
                                        </td>
                                        <td>
                                            <b>{{ number_format($orderItem->total,null,",",".") }}</b>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-xs margin-r-5 btn-edit" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm({{ $loop->iteration - 1 }})">Edit</button>
                                            {{-- <button type="button" class="btn btn-danger btn-xs margin-r-5 btn-remove" onclick="removeItem(event, this, {{ $loop->iteration - 1 }})">Remove</button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
                            <form id="edit-item-form" action="" method="get" onsubmit="editItem(event)" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="item_element" id="item_element" value="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Edit Barang</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_edit">Jenis Barang: <small class="text-danger">*</small></label>
                                            <select id="type_edit" class="form-control" name="type_edit" required>
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
                                        <table role="presentation" class="table table-striped table-bordered table-hover list-image">
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
                                    <table role="presentation" class="table table-striped table-bordered table-hover list-image">
                                        <thead>
                                        <tr>
                                            <th width="300">#</th>
                                            <th width="300">Foto</th>
                                            <th width="300">Aksi</th>
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
            <button type="button" class="btn btn-success" id="btn-add-item" style="margin-right: 15px;display: none;" data-toggle="modal" data-target="#modal-add-item">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Add item</span>
            </button>
            <!-- Modal Add Item -->
            <div class="modal fade" id="modal-add-item" role="dialog" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="add-item-form" action="" method="get" onsubmit="saveItem(event)" enctype="multipart/form-data" autocomplete="off">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add Item</h4>
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
                                        <th width="300">Image</th>
                                    </tr>
                                    </thead>
                                    <tbody id="list-image"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Tutup</button>
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
                    <h3 class="box-title">Nilai</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form id="fileupload" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ $order->client->id }}">
                        {{ csrf_field() }}
                        <p class="margin-b-2"><b>Total: </b><span id="total"></span></p>
                        <p class="margin-b-2"><b>Uang muka: </b><input type="text" id="dp" name="dp" readonly value="{{ number_format($order->uang_muka, null, ',', '.') }}" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2"><b>Kekurangan: </b><span id="kekurangan">{{ number_format($order->orderItems->sum('total') - $order->uang_muka, null, ',', '.') }}</span></p>
                        <p class="margin-b-2"><b>Pembayaran: </b><input type="text" id="pembayaran" name="pembayaran" readonly class="form-control" value="{{ $order->status == 'DIAMBIL' ? number_format($order->sisa_pembayaran, null, ',', '.') : null }}" style="display: inline"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 total-items">
              <button type="button" class="btn bg-purple pull-left" id="btn-pickup" data-toggle="modal" data-target="#modal-pickup-item" style="margin-right: 15px;">
                  <i class="fa fa-fw fa-save"></i>
                  <span>Ambil</span>
              </button>
             <!-- Modal -->
             <div id="modal-pickup-item" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
                 <div class="modal-dialog modal-sm">
                     <!-- Modal content-->
                     <form id="pickup-item-form" action="{{ route('orders.update', $order->id) }}" method="post" onsubmit="pickUpItemForm(event)" autocomplete="off">
                         <div class="modal-content">
                             <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal">&times;</button>
                                 <h4 class="modal-title">Form Ambil Barang</h4>
                             </div>
                             <div class="modal-body">
                                 <div class="form-group">
                                     <label for="payment_method">Metode Pembayaran: <small class="text-danger">*</small></label>
                                     <select class="form-control" id="payment_method" name="payment_method" required>
                                         @foreach($payment_methods as $payment_method)
                                             <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="payment_merchant">Penyedia Pembayaran: <small class="text-danger">*</small></label>
                                     <select class="form-control" id="payment_merchant" name="payment_merchant" required>
                                         @foreach($payment_merchants as $payment_merchant)
                                             <option value="{{ $payment_merchant->id }}">{{ $payment_merchant->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div class="form-group">
                                     <label for="kekurangan">Kekurangan: <small class="text-danger">*</small></label>
                                     <input type="text" class="form-control" id="kekurangan-sisa" name="kekurangan" value="{{ number_format($order->orderItems->sum('total') - $order->uang_muka, null, ',', '.') }}" readonly required>
                                 </div>
                                 <div class="form-group">
                                     <label for="diambil">Diambil oleh: <small class="text-danger">*</small></label>
                                     <input type="text" class="form-control" id="diambil" name="diambil" required>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                   {{-- <button type="reset" class="btn btn-danger pull-left" onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset Form</button> --}}
                                   {{-- <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()" style="margin-right: 15px;">Simpan Form</button> --}}
                                   {{-- <button type="submit" class="btn btn-primary">Submit Form</button> --}}
                                 <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Batalkan</button>
                                 <button type="submit" class="btn bg-purple" id="btn-ambil">Ambil</button>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>
            {{-- <button type="submit" class="btn bg-purple pull-left" style="margin-right: 15px;"> --}}
            {{--     <i class="fa fa-fw fa-save"></i> --}}
            {{--     <span>Simpan</span> --}}
            {{-- </button> --}}
            <button type="button" class="btn bg-olive pull-left" id="btn-simpan" style="margin-right: 15px;" onclick="pickUpItemForm(event)">
                <i class="glyphicon glyphicon-file"></i>
                <span>Simpan</span>
            </button>
            <div class="vl" style="display:inline-block; border-left: 1px solid black; height: 30px; margin: auto 15px;"></div>
            &nbsp;
            <a href="{{ route('orders.print', $order->id) }}" target="_blank" class="btn bg-navy" style="margin-left:15px;margin-right: 15px;margin-top: -20px;">
                <i class="fa fa-fw fa-print"></i>
                <span>Cetak</span>
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-default pull-right"><i class="fa fa-fw fa-arrow-left"></i> Back to Page Order</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script type="text/javascript" src="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.js') }}"></script>
    <script>
        $('#items').show();
        $('.total-items').show();
        $('.list-image').hide();
        $('.list-image').show();
        $('#btn-ambil').attr('disabled', true);
        const item_types = @json($item_types);
        let dataFile = [];
        let items = [];
        let editItemSelected = null;
        let allItem = { id: null, type: '', keterangan: '', biaya: 0, gambar: [] };

        const orderItems = <?= $order->orderItems->toJson(); ?>;

        for (let i=0; i < orderItems.length; i++) {
            const orderItemPhotos = orderItems[i].order_item_photos;

            allItem.id = orderItems[i].id;
            allItem.type = orderItems[i].item_type_id;
            allItem.keterangan = orderItems[i].note;
            allItem.biaya = orderItems[i].total.toLocaleString('id-ID');
            for (let j=0; j < orderItemPhotos.length; j++) {
                allItem.gambar.push(`${window.location.origin}${window.location.protocol !== 'https:' ? '' : '/public' }/storage/${orderItemPhotos[j].thumbnail_url}`);
            }

            items.push(allItem);

            allItem = {id: null, type: '', keterangan: '', biaya: 0, gambar: [] };
        }

        console.log('items', items);

        getPaymentMerchant();

        sumTotalItem();

        const status ="{{ $order->status }}";
        if(status.toLowerCase() === 'diambil') {
            $('#btn-add-customer, #btn-add-item, #customer, #site_id, #btn-pickup, #btn-simpan, .btn-edit, .btn-remove').attr("disabled", true);
        }

        function getPaymentMethodById(nameKey, myArray){
            for (let i=0; i < myArray.length; i++) {
                if (myArray[i].id === parseFloat(nameKey, 10)) {
                    return myArray[i];
                }
            }
        }

        function getTypeById(nameKey, myArray){
            for (let i=0; i < myArray.length; i++) {
                if (myArray[i].id === parseFloat(nameKey, 10)) {
                    return myArray[i];
                }
            }
        }

        $('#payment_method').on('change', function() {
            getPaymentMerchant();
        });

        function getPaymentMerchant() {
            $.ajax({
                url: '{{ route("order.merchant_by_payment") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    payment_method: $('#payment_method').val()
                },
                success: function(response) {
                    $('#payment_merchant').empty();
                    $.each(response, function(index, paymentMerchant) {
                        $('#payment_merchant').append('<option value="' + paymentMerchant.id + '">' + paymentMerchant.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
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
                            <img src="${image}" alt="Foto Barang ${index + 1}" title="Foto Barang ${index + 1}" class="img-thumbnail" style="height:100px" data-magnify="gallery" data-caption="Foto Barang ${index + 1}" data-src="${image}" style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${editItemSelected}, ${index}, '${image}')">Hapus foto</button>
                        </td>
                    </tr>
                `;
                    contentImage.append(row);
                });
            }, 500);
        }

        function renderListImage() {
            console.log(dataFile);
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
                            <img src="${dataFile[i]}" alt="Foto Barang ${i + 1}" title="Foto Barang ${i + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;" data-magnify="gallery" data-caption="Foto Barang ${i + 1}" data-src="${image}">
                        </td>
                    </tr>
                `);
            }
        }

        function removeImage(itemIndex, imageIndex, imageUrl=null) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS foto ini?") === true) {

                dataFile.splice(imageIndex, 1);
                items[itemIndex]['gambar'].splice(imageIndex, 1);

                setTimeout(() => {
                    // renderListImage(itemIndex);

                    const contentImage = $('.content-image');
                    contentImage.empty();

                    dataFile.forEach(function(image, imageIndex) {
                        const row = `
                            <tr>
                                <th>
                                    ${imageIndex + 1}
                                </th>
                                <td class="text-center">
                                    <img src="${image}" alt="Foto Barang ${imageIndex + 1}" title="Foto Barang ${imageIndex + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;" data-magnify="gallery" data-caption="Foto Barang ${imageIndex + 1}" data-src="${image}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${itemIndex}, ${imageIndex}, '${image}')">Hapus foto</button>
                                </td>
                            </tr>
                        `;
                        contentImage.append(row);
                    });

                    if (dataFile.length === 0) {
                        $('.list-image').hide();
                        $('#gambar').val(null);
                    }

                    if (imageUrl !== null) {
                        if (isValidURL(imageUrl)) {
                            const url = new URL(imageUrl);
                            const pathname = url.pathname;
                            const thumbnailPath = pathname.replace('/storage/', '');

                            console.log(thumbnailPath);

                            deleteItemPhoto(thumbnailPath);
                        }
                    }
                }, 500);
            }
        }

        function isValidURL(urlString) {
            const url = new URL(urlString);
            const pathname = url.pathname;
            return pathname.includes('/storage/');
            // try {
            //     new URL(urlString);
            //     return true;
            // } catch (error) {
            //     return false;
            // }
        }

        function removeEditImage(e, el, itemIndex, imageIndex, imageId=null) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS foto ini?") === true) {
                items[itemIndex]['gambar'].splice(imageIndex, 1);

                const message = 'Foto berhasil dihapus';
                $('.top-right').notify({
                    message: {
                        text: `Sukses! ${message}`
                    }
                }).show();

                if (imageId !== null) {
                    deleteItemPhoto(imageId);
                }

                setTimeout(() => {
                    showImageAsTable(itemIndex);
                    if (dataFile.length === 0) {
                        $('.list-image').hide();
                        $('#gambar_edit').val(null);
                    }
                }, 500);
            }
        }

        function deleteItemPhoto(thumbnail_url) {
            $.ajax({
                url: "{{ route('orders.index') }}/item/photo/",
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    thumbnail_url
                },
                success: function(result) {
                    // Tindakan setelah request berhasil
                    console.log(result);
                    const message = 'Foto Pesanan berhasil dihapus!';
                    $('.top-right').notify({
                        message: { text: `Sukses! ${message}` }
                    }).show();
                },
                error: function(xhr, status, error) {
                    // Tindakan jika terjadi kesalahan
                    const message = 'Foto Pesanan tidak berhasil dihapus!';
                    $('.top-right').notify({
                        message: { text: `Sukses! ${message}` },
                        type:'danger'
                    }).show();
                    console.log(error);
                }
            });
        }

        function showEditItemForm(index) {
            showImageAsTable(index);
            $('#item_element').val(index);

            const dataItem = items[index];
            editItemSelected = index;

            dataFile = dataItem.gambar;

            $(`#type_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const biaya = $('#biaya_edit').val(parseInt(dataItem.biaya, 10).toLocaleString('id-ID'));
        }

        function showImageAsTable(itemIndex) {
            const contentImage = $('.content-image');
            contentImage.empty();

            items[itemIndex].gambar.forEach(function(image, imageIndex) {
                const row = `
                    <tr>
                        <th>
                            ${imageIndex + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${imageIndex + 1}" title="Foto Barang ${imageIndex + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;" data-magnify="gallery" data-caption="Foto Barang ${imageIndex + 1}" data-src="${image}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${itemIndex}, ${imageIndex}, '${image}')">Hapus foto</button>
                        </td>
                    </tr>
                `;
                contentImage.append(row);
                dataFile.push(image);
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

                const message = 'Barang berhasil dihapus!';
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

                    // Reset form
                    $('#add-customer-form').trigger("reset");

                    $('#customer').val(user_id).trigger("change");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Pelanggan tidak berhasil disimpan!'
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

        function pickUpItemForm(event) {
            console.log('all item', items);
            event.preventDefault();
            const customer_id = $('#customer_id').val();
            const site_id = $('#site_id').val();
            const payment_method = $('#payment_method').val();
            const payment_merchant = $('#payment_merchant').val();
            const picked_by = $('#diambil').val();
            const picked_at = dateInYyyyMmDdHhMmSs(new Date());
            const total = $('#total').text().replace(/\./g, '');
            const dp = $('#dp').val().replace(/\./g, '');
            const kekurangan = $('#kekurangan').text().replace(/\./g, '');
            const kekuranganSisa = picked_by.length > 0 ? $('#kekurangan-sisa').val().replace(/\./g, '') : 0;
            const pembayaran = parseInt(kekuranganSisa, 10);
            const status = picked_by.length > 0 ? 'DIAMBIL' : 'DIPROSES';
            $('#pembayaran').val(picked_by.length > 0 ? pembayaran.toLocaleString('id-ID') : '');
            $('#kekurangan').text(picked_by.length > 0 ? parseInt(kekurangan, 10) - parseInt(kekuranganSisa, 10) : $('#kekurangan').text());
            $('#status').text(status);
            $('#oleh').text(picked_by);
            $('#pada').text(picked_at);
            let data = { customer_id, site_id, total, picked_by, picked_at, payment_method, payment_merchant, items, sisa_pembayaran: picked_by.length > 0 ? pembayaran : null, uang_muka: dp, status };

            $.ajax({
                url: $('#pickup-item-form').attr('action'),
                method: 'PUT',
                data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    console.log(response);
                    const user_id = response.id;
                    const type = 'success';
                    const message = 'Barang berhasil diambil!'
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

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-pickup-item').modal('hide');

                    // Reset form
                    $('#pickup-item-form').trigger("reset");
                    if(picked_by.length > 0) {
                        $('#btn-add-customer, #btn-add-item, #customer, #site_id, #btn-pickup, #btn-simpan, .btn-edit, .btn-remove').attr("disabled", true);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Pelanggan tidak berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-pickup-item').modal('hide');
                }
            });

            $('html, body').animate({
                scrollTop: $("#alert-container").offset().top - 125
            }, 1000);
        }

        function renderItems() {
            const tbody = $('#table-items tbody'); // Ganti '#itemTable' dengan ID dari elemen tabel Anda
            tbody.empty(); // Kosongkan isi tabel sebelum menambahkan item baru


            // Loop melalui setiap item dan tambahkan baris HTML untuk masing-masing item
            items.forEach(function(item, index) {
                console.log('Jenis Barang', item.type);
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
                                <span>Show images</span>
                            </span>
                        </td>
                        <td>
                            <b>${parseInt(item.biaya, 10).toLocaleString('id-ID')}</b>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-xs margin-r-5" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm(${index})">Edit</button>
                            <!-- <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeItem(event, this, ${index})">Remove</button> -->
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
                console.log('id type', type);
                const keterangan = $('#keterangan').val();
                const biaya = $('#biaya').val().replace(/\./g, '');
                const gambar = dataFile;

                // Buat objek item
                const newItem = { type, keterangan, biaya, gambar };

                console.log(newItem);

                // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

                // Contoh operasi CRUD sederhana (tambahkan item ke array)
                items.push(newItem); // items adalah variabel yang berisi array item

                renderItems();

                // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
                const message = 'Barangh berhasil ditambahkan!';
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

            function editItem(event) {
                $('#items').show();
                $('.total-items').show();
                event.preventDefault(); // Menghentikan aksi default dari submit form

                const element = $('#item_element').val();

                // Ambil nilai dari form
                const type = $('#type_edit').val();
                const keterangan = $('#keterangan_edit').val();
                const biaya = $('#biaya_edit').val().replace(/\./g, '');

                // Buat objek item
                const newItem = { id: items[element].id, type, keterangan, biaya, gambar: dataFile};

                console.log(newItem);

                // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

                // Contoh operasi CRUD sederhana (tambahkan item ke array)
                items[element] = newItem; // items adalah variabel yang berisi array item

                renderItems();

                // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
                const message = 'Barang berhasil diedit!';
                $('.top-right').notify({
                    message: { text: `Success! ${message}` }
                }).show();

                // Tutup modal setelah selesai menyimpan data
                $('#modal-edit-item').modal('hide');

                // Reset form
                $('#edit-item-form').trigger("reset");
                // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

                sumTotalItem();

                dataFile = [];
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

            function sumTotalItem() {
                let total = 0;
                for(let i = 0; i < items.length; i++) {
                    total += parseInt(items[i].biaya, 10);
                }
                $('#total').text(total.toLocaleString('id-ID'));
                const total_expense = parseInt(total, 10);
                const dp = $('#dp').val().replace(/\./g, '');

                // Hapus semua karakter selain digit
                let digitsOnly = dp.replace(/\D/g, '');

                total = total_expense - parseInt(digitsOnly, 10);

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $('#dp').val(formattedNumber);

                const kekurangan = !isNaN(total) ? total.toLocaleString('id-ID') : '-';
                $('#kekurangan').text(kekurangan);
            }

            function padTwoDigits(num) {
                return num.toString().padStart(2, "0");
            }

            function dateInYyyyMmDdHhMmSs(date, dateDivider = "-") {

                return (
                    [
                        date.getFullYear(),
                        padTwoDigits(date.getMonth() + 1),
                        padTwoDigits(date.getDate()),
                    ].join(dateDivider) +
                    " " +
                    [
                        padTwoDigits(date.getHours()),
                        padTwoDigits(date.getMinutes()),
                        padTwoDigits(date.getSeconds()),
                    ].join(":")
                );
            }

            $(function() {
                $('#modal-add-customer').on('shown.bs.modal', function () {
                    $('#add-customer-form #name').focus();
                });
                $('#modal-show-image-item').on('hidden.bs.modal', function () {
                    $('.content-image').empty();
                });
                $('#modal-add-item').on('hidden.bs.modal', function () {
                    resetFormAddItem();
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
                        <p class="text-center margin-b-10"><b id="status">DIPROSES</b></p>
                        <p class="text-center margin-b-2"><b>Oleh: </b> <span id="oleh">-</span></p>
                        <p class="text-center margin-b-2"><b>Pada: </b> <span id="pada">-</span></p>
                    `);

                    $('#customer_id').val(customerId);
                });

                // $('#customer').select2('open');
                // setTimeout(function() {
                //     // $('#customer').val('12').trigger('change.select2');
                //     $('#customer').on("select2:selecting", function(e) {
                //         $(this).val('12').trigger('change.select2')
                //     });
                // }, 500)
                $('#customer').val(6);
                // $('#customer').select2('open').trigger('change');

                const $newOption = $(`<option selected="selected"></option>`).val("{{ $order->id }}").text("{{ $order->client->name }}");
                $("#customer").append($newOption).trigger('change');

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
                });

                $('#diambil').on('input', function (e) {
                    const value = $('#kekurangan-sisa').val();
                    console.log('test');
                    // Hapus semua karakter selain digit
                    let digitsOnly = value.replace(/\D/g, '');

                    // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                    let kekuranganSisa = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    const kekurangan = $('#kekurangan').text();
                    console.log(kekurangan);
                    console.log(kekuranganSisa);
                    if (kekurangan === kekuranganSisa) {
                        $('#btn-ambil').removeAttr('disabled');
                    } else {
                        $('#btn-ambil').attr('disabled', true);
                    }
                });
                $('#biaya, #biaya_edit, #kekurangan-sisa').on('input', function (e) {
                    const value = $(this).val();

                    // Hapus semua karakter selain digit
                    let digitsOnly = value.replace(/\D/g, '');

                    // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Update nilai input dengan angka yang diformat
                    $(this).val(formattedNumber);
                });

                $('#site_id').select2();

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
