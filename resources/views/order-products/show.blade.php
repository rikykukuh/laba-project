@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Penjualan')

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('order-products.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Penjualan
        </a>
    </li>

@endsection

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
                    <button type="button" class="btn btn-info" id="btn-add-customer" data-toggle="modal" data-target="#modal-add-customer" style="margin: 25px auto;border-product-left: 1px solid #ccc;">Tambah Pelanggan</button>
                    <!-- Modal -->
                    <div id="modal-add-customer" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-md">
                            <!-- Modal content-->
                            <form id="add-customer-form" action="{{ route('customers.store') }}" method="post" onsubmit="saveCustomer(event)" autocomplete="off">
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
        <!-- Detail Customer -->
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border-product">
                    <h3 class="box-title">Detail Pelanggan</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" id="detail-user">
                    <p class="text-muted well well-sm no-shadow margin-b-10">
                        <strong>Nama:</strong> {{ $order->customer->name ?? '-' }}
                        <br>
                        <strong>Alamat:</strong> {{ $order->customer->address ?? '-' }}
                        <br>
                        <strong>Telepon:</strong> {{ $order->customer->phone_number ?? '-' }}
                    </p>
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
                        <table class="table table-hover table-bordered" style="border: 1px solid #ddd !important;" id="table-items">
                            <thead>
                                <tr class="bg-navy">
                                    <th class="text-center">#</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Sub total</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $orderItem)
{{--                                    @dd($orderItem->bruto * $orderItem->quantity * ($orderItem->discount / 100))--}}
                                    @php
                                        $infoDiscount = (int) $orderItem->discount > 100 ? $orderItem->discount : ($orderItem->bruto * $orderItem->quantity * ((int)$orderItem->discount / 100))
                                    @endphp
                                    <tr>
                                        <th class="text-center">{{ $loop->iteration }}</th>
                                        <td class="text-center">{{ $products->find($orderItem->product_id)->name }}</td>
                                        <td class="text-center">{{ $orderItem->note }}</td>
                                        <td class="text-center">
                                            <b>{{ number_format($orderItem->bruto,null,",",".") }}</b>
                                        </td>
                                        <td class="text-center">
                                            <b>{{ $orderItem->quantity }}</b>
                                        </td>
                                        <td class="text-center">
                                            <b>{{ number_format($orderItem->bruto * $orderItem->quantity, null, "," ,".") }}</b>
                                        </td>
                                        <td class="text-center">
                                            <b>{{ number_format($orderItem->discount,null,",",".")  }} {{ $orderItem->discount > 100 ? '' : '%' }} {{ $orderItem->discount > 100 ? '' : "(" . number_format($orderItem->bruto * $orderItem->quantity * ((int)$orderItem->discount / 100),null,",",".") . ")" }}</b>
                                        </td>
                                        <td class="text-center">
                                            <b>{{ number_format(($orderItem->bruto * $orderItem->quantity) - $infoDiscount,null,",",".") }}</b>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-xs margin-r-5 btn-edit" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm({{ $loop->iteration - 1 }})">Edit</button>
                                            {{-- <button type="button" class="btn btn-danger btn-xs margin-r-5 btn-remove" onclick="removeItem(event, this, {{ $loop->iteration - 1 }})">Remove</button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
                                            <label for="type_edit">Jenis Produk: <small class="text-danger">*</small></label>
                                            <select id="type_edit" class="form-control" name="type_edit" required>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan_edit">Keterangan: <small class="text-danger">*</small></label>
                                            <textarea class="form-control" id="keterangan_edit" name="keterangan_edit"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="harga_item_edit">Harga: <small class="text-danger">*</small></label>
                                            <input type="text" readonly class="form-control" id="harga_item_edit"
                                                   name="harga_item_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="kuantitas_item_edit">Jumlah: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="kuantitas_item_edit"
                                                   name="kuantitas_item_edit">
                                        </div>
                                         <div class="form-group">
                                             <label for="discount_item_edit">Discount:</label>
                                             <input type="text" class="form-control" id="discount_item_edit" name="discount_item_edit">
                                         </div>
                                        <div class="form-group" style="display: none;">
                                            <label for="total_after_discount_edit">Discount Amount: </label>
                                            <span id="total_after_discount_edit"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="total_item_edit">Total: <small class="text-danger">*</small></label>
                                            <input readonly type="text" class="form-control" id="total_item_edit" name="total_item_edit">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default margin-r-5" data-dismiss="modal">Batalkan</button>
                                        <button type="submit" class="btn btn-warning">Simpan</button>
                                    </div>
                                </div>
                            </form>
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
                                    <label for="type">Jenis Produk: <small class="text-danger">*</small></label>
                                    <select id="type" class="form-control" name="type" required>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
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
                <div class="box-header with-border-product">
                    <h3 class="box-title">Nilai</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form id="fileupload" action="{{ route('order-products.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ $order->customer->id }}">
                        {{ csrf_field() }}
                        <p class="margin-b-2" style="display: none;"><b>Bruto: </b><span id="bruto">{{ number_format($order->bruto,null,",",".")  }}</span></p>
                        <p class="margin-b-2"><b>Sub total: </b><span id="sub_total">{{ number_format($order->bruto,null,",",".")  }}</span></p>
                        <p class="margin-b-2"><b>Discount: </b><input type="text" id="discount" readonly name="discount" value="{{ $order->discount == 0 ? '' : number_format($order->discount,null,",",".")  }}" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2" style="display: none;"><b>Netto: </b><span id="netto">{{ number_format($order->netto,null,",",".")  }}</span></p>
                        <p class="margin-b-2"><i>INCLUDED PPN: </i><span id="tax">11%</span></p>
                        <p class="margin-b-2"><b>Total: </b><span id="total">{{ number_format($order->netto,null,",",".")  }}</span></p>
                        <p class="margin-b-2" style="display: none;"><b>VAT: </b><span id="vat"></span></p>
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
                     <form id="pickup-item-form" action="{{ route('order-products.update', $order->id) }}" method="post" onsubmit="pickUpItemForm(event)" autocomplete="off">
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
            <div class="vl" style="display:inline-block; border-product-left: 1px solid black; height: 30px; margin: auto 15px;"></div>
            &nbsp;
            <a href="{{ route('order-products.print', $order->id) }}" target="_blank" class="btn bg-navy" style="margin-left:15px;margin-right: 15px;margin-top: -20px;">
                <i class="fa fa-fw fa-print"></i>
                <span>Cetak</span>
            </a>
            <a href="{{ route('order-products.index') }}" class="btn btn-default pull-right"><i class="fa fa-fw fa-arrow-left"></i> Back to Page Order</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script type="text/javascript" src="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.js') }}"></script>
    <script>
        $('#items').show();
        $('.total-items').show();
        const products = @json($products);
        let items = [];
        let editItemSelected = null;
        let allItem = { id: null, type: '', keterangan: '', biaya: 0};

        const orderItems = <?= $order->orderItems->toJson(); ?>;

        for (let i=0; i < orderItems.length; i++) {

            allItem.id = orderItems[i].id;
            allItem.type = orderItems[i].product_id;
            allItem.keterangan = orderItems[i].note;
            allItem.harga = orderItems[i].bruto.toLocaleString('id-ID');
            allItem.bruto = orderItems[i].bruto.toLocaleString('id-ID');
            allItem.kuantitas = orderItems[i].quantity.toLocaleString('id-ID');
            allItem.jumlah = orderItems[i].quantity.toLocaleString('id-ID');
            allItem.discount_item = orderItems[i].discount.toLocaleString('id-ID');
            allItem.netto = orderItems[i].netto.toLocaleString('id-ID');
            allItem.vat = orderItems[i].vat.toLocaleString('id-ID');
            allItem.total = orderItems[i].total.toLocaleString('id-ID');

            items.push(allItem);

            allItem = {id: null, type: '', keterangan: '', biaya: 0};
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
                url: '{{ route("order-products.merchant_by_payment") }}',
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

        function showEditItemForm(index) {
            $('#item_element').val(index);

            const dataItem = items[index];
            editItemSelected = index;

            $(`#type_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const harga = $('#harga_item_edit').val(parseInt(dataItem.harga, 10).toLocaleString('id-ID'));
            const jumlah = $('#kuantitas_item_edit').val(parseInt(dataItem.kuantitas, 10).toLocaleString('id-ID'));
            const discount_item = $('#discount_item_edit').val(parseInt(dataItem.discount_item, 10).toLocaleString('id-ID'));

            if(dataItem.discount_item > 100) {
                $('#total_after_discount_edit').text('').parent().css('display', 'none');
            } else {
                $('#total_after_discount_edit').text((dataItem.bruto * (dataItem.discount_item / 100)).toLocaleString('id-ID')).parent().css('display', 'inline');
            }
            $('#kuantitas_item_edit, #discount_item_edit').trigger('input');
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
            const discount = $('#discount').val().replace(/\./g, '');
            const picked_at = dateInYyyyMmDdHhMmSs(new Date());
            const bruto = $('#bruto').text().replace(/\./g, '');
            const total = $('#netto').text().replace(/\./g, '');
            const status = picked_by.length > 0 ? 'DIAMBIL' : 'DIPROSES';
            // $('#pembayaran').val(picked_by.length > 0 ? pembayaran.toLocaleString('id-ID') : '');
            $('#oleh').text(picked_by);
            $('#pada').text(picked_at);
            let data = { customer_id, site_id, bruto, discount: discount === '' ? 0 : discount, total, netto: total, picked_by, picked_at, payment_method, payment_merchant, items, status };
            console.log(data);

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
                    const message = `Barang berhasil ${picked_by.length > 0 ? 'diambil' : 'disimpan'}!`;
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
                console.log('Jenis Produk', item.type);
                let type = getTypeById(item.type, products);
                let info_discount = parseInt(item.discount_item, 10) > 100 ? item.discount_item : item.jumlah * (parseInt(item.discount_item, 10) / 100);
                const row = `
                    <tr>
                        <th class="text-center">${index + 1}</th>
                        <td class="text-center">${type.name}</td>
                        <td class="text-center">${item.keterangan}</td>
                        <td class="text-center">
                            <b>${parseInt(item.harga, 10).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
                            <b>${parseInt(item.kuantitas, 10).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
                            <b>
                                ${parseInt(item.discount_item, 10).toLocaleString('id-ID')}${item.discount_item > 100 ? '' : '%'}
                            </b>
                        </td>
                           <td class="text-center">
                            <b>
                                ${item.discount_item.toLocaleString('id-ID')}${item.discount_item > 100 ? '' : '%'} ${item.discount_item > 100 ? '' : "(" + (parseInt(item.harga, 10) * parseInt(item.kuantitas, 10) * (item.discount_item / 100)).toLocaleString('id-ID') + ")"}
                            </b>
                        </td>
                        <td class="text-center">
                            <b>${(parseInt(item.harga, 10) * parseInt(item.kuantitas, 10) - parseInt(info_discount, 10)).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
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

                // Buat objek item
                const newItem = { type, keterangan, biaya };

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
            }

            function editItem(event) {
                $('#items').show();
                $('.total-items').show();
                event.preventDefault(); // Menghentikan aksi default dari submit form

                const element = $('#item_element').val();

                // Ambil nilai dari form
                const type = $('#type_edit').val();
                const keterangan = $('#keterangan_edit').val();
                const harga = $('#harga_item_edit').val().replace(/\./g, '');
                const kuantitas = $('#kuantitas_item_edit').val().replace(/\./g, '');
                const jumlah = parseInt(harga, 10) * parseInt(kuantitas, 10);
                const discount_item = $('#discount_item_edit').val().replace(/\./g, '');

                // Buat objek item
                const newItem = { id: items[element].id, type, keterangan, harga, kuantitas, jumlah, bruto: jumlah, discount_item };

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
                $('#discount').trigger('input');
            }

            function resetFormAddItem() {
                $('#add-item-form').trigger("reset");
            }

            function sumTotalItem() {
                let bruto = 0;
                let discount = 0;

                for(let i = 0; i < items.length; i++) {
                    bruto += parseInt(items[i].bruto, 10);
                    discount += parseInt(items[i].discount_item, 10) > 100 ? Number(items[i].discount_item) : (parseInt(items[i].bruto, 10)) * (parseInt(items[i].discount_item, 10) / 100);
                    console.log(discount)
                }
                $('#bruto').text(bruto.toLocaleString('id-ID'));
                $('#sub_total').text(bruto.toLocaleString('id-ID'));
                $('#discount').val((discount).toLocaleString('id-ID'));

                // const total_expense = parseInt(bruto, 10);

                // Hapus semua karakter selain digit
                // let digitsOnly = total_expense;

                // bruto = total_expense - parseInt(digitsOnly, 10);

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                // let formattedNumber = total.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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

            function calculateIncludedVAT(totalValue, vatRate) {
                const vatRateDecimal = vatRate / 100;
                console.log((totalValue * vatRateDecimal) / (1 + vatRateDecimal));
                return (totalValue * vatRateDecimal) / (1 + vatRateDecimal);
            }

            $(function() {
                $('#modal-add-customer').on('shown.bs.modal', function () {
                    $('#add-customer-form #name').focus();
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
                        url: '{{ route("customer.search") }}', // Ganti dengan URL endpoint Anda
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            const options = [];
                            $.each(data, function(index, item) {
                                options.push({
                                    id: item.id,
                                    text: `${item.name} (${item.phone_number})`,
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

                const $newOption = $(`<option selected="selected"></option>`).val("{{ $order->id }}").text("{{ $order->customer->name }}");
                $("#customer").append($newOption).trigger('change');

                // $('#diambil').on('input', function (e) {
                //     console.log('test');
                //     let digitsOnly = value.replace(/\D/g, '');
                // });

                $('#biaya, #biaya_edit, #discount_item, #discount_item_edit').on('input', function (e) {
                    const value = $(this).val();

                    // Hapus semua karakter selain digit
                    let digitsOnly = value.replace(/\D/g, '');

                    // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Update nilai input dengan angka yang diformat
                    $(this).val(formattedNumber);
                });

                $('#kuantitas_item').on('input', function(e) {
                    const kuantitas_item = $(this).val();

                    let digitsOnly = kuantitas_item.replace(/\D/g, '');

                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    $(this).val(formattedNumber);

                    const harga = $('#harga_item').val().replace(/\D/g, '');
                    const total_harga = (Number(harga) * Number(formattedNumber));

                    const discount_item = $('#discount_item').val().replace(/\D/g, '');
                    const total_item = $('#total_item');
                    if(discount_item === '') {
                        total_item.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                        $('#total_after_discount').text('').parent().css('display', 'none');
                    } else {
                        if(Number(discount_item) > 100) {
                            total_item.val(total_harga === 0 ? '' : (total_harga - Number(discount_item)).toLocaleString('id-ID'));
                            $('#total_after_discount').text('').parent().css('display', 'none');
                        } else {
                            const total_after_discount = total_harga * (Number(discount_item) / 100);
                            $('#total_after_discount').text(total_after_discount.toLocaleString('id-ID')).parent().css('display', 'inline');
                            total_item.val(total_harga === 0 ? '' : (total_harga - total_after_discount).toLocaleString('id-ID'));
                        }
                    }
                });

                $('#discount_item').on('input', function(e) {
                    const discount_item = $(this).val();

                    let digitsOnly = discount_item.replace(/\D/g, '');

                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    $(this).val(formattedNumber);

                    const kuantitas_item = $('#kuantitas_item').val().replace(/\D/g, '');

                    const harga = $('#harga_item').val().replace(/\D/g, '');
                    const total_harga = (Number(harga) * Number(kuantitas_item));

                    const total_item = $('#total_item');
                    if(discount_item === '') {
                        total_item.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                    } else {
                        if(Number(digitsOnly) > 100) {
                            total_item.val(total_harga === 0 ? '' : (total_harga - Number(digitsOnly)).toLocaleString('id-ID'));
                            $('#total_after_discount').text('').parent().css('display', 'none');
                        } else {
                            const total_after_discount = total_harga * (Number(digitsOnly) / 100);
                            if(Number(digitsOnly) === 100) {
                                total_item.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                                $('#total_after_discount').text('').parent().css('display', 'none');
                            } else {
                                $('#total_after_discount').text(total_after_discount.toLocaleString('id-ID')).parent().css('display', 'inline');
                                total_item.val(total_harga === 0 ? '' : (total_harga - total_after_discount).toLocaleString('id-ID'));
                            }
                        }
                    }
                });

                $('#kuantitas_item_edit').on('input', function(e) {
                    const kuantitas_item_edit = $(this).val();

                    let digitsOnly = kuantitas_item_edit.replace(/\D/g, '');

                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    $(this).val(formattedNumber);

                    const harga = $('#harga_item_edit').val().replace(/\D/g, '');
                    const total_harga = (Number(harga) * Number(formattedNumber));

                    const discount_item_edit = $('#discount_item_edit').val().replace(/\D/g, '');
                    const total_item_edit = $('#total_item_edit');
                    if(discount_item_edit === '') {
                        total_item_edit.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                        $('#total_after_discount_edit').text('').parent().css('display', 'none');
                    } else {
                        if(Number(discount_item_edit) > 100) {
                            total_item_edit.val(total_harga === 0 ? '' : (total_harga - Number(discount_item_edit)).toLocaleString('id-ID'));
                            $('#total_after_discount_edit').text('').parent().css('display', 'none');
                        } else {
                            const total_after_discount_edit = total_harga * (Number(discount_item_edit) / 100);
                            $('#total_after_discount_edit').text(total_after_discount_edit.toLocaleString('id-ID')).parent().css('display', 'inline');
                            total_item_edit.val(total_harga === 0 ? '' : (total_harga - total_after_discount_edit).toLocaleString('id-ID'));
                        }
                    }
                });

                $('#discount_item_edit').on('input', function(e) {
                    const discount_item_edit = $(this).val();

                    let digitsOnly = discount_item_edit.replace(/\D/g, '');

                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    $(this).val(formattedNumber);

                    const kuantitas_item_edit = $('#kuantitas_item_edit').val().replace(/\D/g, '');

                    const harga = $('#harga_item_edit').val().replace(/\D/g, '');
                    const total_harga = (Number(harga) * Number(kuantitas_item_edit));

                    const total_item_edit = $('#total_item_edit');
                    if(discount_item_edit === '') {
                        total_item_edit.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                        $('#total_after_discount_edit').text('').parent().css('display', 'none');
                    } else {
                        if(Number(digitsOnly) > 100) {
                            total_item_edit.val(total_harga === 0 ? '' : (total_harga - Number(digitsOnly)).toLocaleString('id-ID'));
                            $('#total_after_discount_edit').text('').parent().css('display', 'none');
                        } else {
                            const total_after_discount_edit = total_harga * (Number(digitsOnly) / 100);
                            if(Number(digitsOnly) === 100) {
                                total_item_edit.val(total_harga === 0 ? '' : total_harga.toLocaleString('id-ID'));
                                $('#total_after_discount_edit').text('').parent().css('display', 'none');
                            } else {
                                $('#total_after_discount_edit').text(total_after_discount_edit.toLocaleString('id-ID')).parent().css('display', 'inline');
                                total_item_edit.val(total_harga === 0 ? '' : (total_harga - total_after_discount_edit).toLocaleString('id-ID'));
                            }
                        }
                    }
                });

                $('#discount').on('input', function(e) {
                    let bruto = $('#bruto').text().replace(/\./g, '');
                    const bruto_value = parseInt(bruto, 10);
                    const discount = $(this).val();

                    // Hapus semua karakter selain digit
                    let digitsOnly = discount.replace(/\D/g, '');

                    let netto = bruto_value - parseInt(digitsOnly, 10);

                    // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                    let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Update nilai input dengan angka yang diformat
                    $(this).val(formattedNumber);

                    const totalValue = !isNaN(netto) ? netto.toLocaleString('id-ID') : '-';
                    $('#netto').text(totalValue);
                    $('#total').text(totalValue);
                    const vatRate = 11;
                    let vatAmount = calculateIncludedVAT(netto, vatRate);
                    vatAmount = vatAmount;
                    $('#vat').text(vatAmount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

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
