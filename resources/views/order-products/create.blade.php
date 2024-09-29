@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Tambah Penjualan')
@section('layout_css')
    <link href="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }
        .glyphicon.spinning {
            animation: spin 1s infinite linear;
            -webkit-animation: spin2 1s infinite linear;
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg); }
            to { transform: scale(1) rotate(360deg); }
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg); }
            to { -webkit-transform: rotate(360deg); }
        }
    </style>
@endsection

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('order-products.index') }}" class="link_menu_page">
            <i class="fa fa-shopping-basket"></i> Penjualan
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
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-customer"
                        style="margin: 25px auto;border-product-left: 1px solid #ccc;">Tambah Pelanggan</button>
                    <!-- Modal -->
                    <div id="modal-add-customer" class="modal fade" role="dialog" data-keyboard="false"
                        data-backdrop="static">
                        <div class="modal-dialog modal-md">
                            <!-- Modal content-->
                            <form id="add-customer-form" action="{{ route('customers.store') }}" method="post"
                                onsubmit="saveCustomer(event)" autocomplete="off">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Tambah Pelanggan</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Nama: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_number">No Telepon: <small
                                                    class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="city_id">Kota: <small class="text-danger">*</small></label>
                                            <select class="form-control" id="city_id" name="city_id" required>
                                                <option disabled selected> -- Pilih Kota -- </option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Alamat:</label>
                                            <textarea class="form-control" id="address" name="address"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="reset" id="btn-reset-add-customer" class="btn btn-danger pull-left"
                                            onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"
                                            onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()"
                                            style="margin-right: 15px;">Batalkan</button>
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
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->name }}</option>
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
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive no-padding">
                        <table class="table table-hover table-bordered" style="border: 1px solid #ddd !important;" id="table-items">
                            <thead class="bg-navy">
                                <tr>
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
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Modal Edit Item -->
                    <div class="modal fade" id="modal-edit-item" role="dialog" data-keyboard="false"
                        data-backdrop="static">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <form id="edit-item-form" action="" method="get" onsubmit="formEditItem(event)"
                                enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="item_element" id="item_element" value="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Edit Item</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_item_edit">Jenis Produk: <small
                                                    class="text-danger">*</small></label>
                                            <select id="type_item_edit" class="form-control" name="type_item_edit">
                                                <option value="" disabled selected>-- Pilih Jenis Produk --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan_edit">Keterangan: <small
                                                    class="text-danger">*</small></label>
                                            <textarea class="form-control" id="keterangan_edit" name="keterangan_edit"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="harga_edit">Harga: <small class="text-danger">*</small></label>
                                            <input type="text" readonly class="form-control" id="harga_item_edit"
                                                name="harga_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="kuantitas_item_edit">Quantity: <small class="text-danger">*</small></label>
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
                                        <button type="button" class="btn btn-default margin-r-5"
                                            data-dismiss="modal">Batalkan</button>
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
        <div class="col-md-1" style="margin-bottom: 15px;">
            <span class="btn btn-success" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-add-item">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Tambah</span>
            </span>
            <!-- Modal Add Item -->
            <div class="modal fade" id="modal-add-item" role="dialog" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="add-item-form" action="" method="get" onsubmit="saveItem(event)"
                        enctype="multipart/form-data" autocomplete="off">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Tambah Barang</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type_item">Jenis Produk: <small class="text-danger">*</small></label>
                                    <select id="type_item" class="form-control" name="type_item" required>
                                        <option value="" disabled selected>-- Pilih Jenis Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan: <small class="text-danger">*</small></label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="harga_item">Harga: <small class="text-danger">*</small></label>
                                    <input type="text" readonly class="form-control" id="harga_item" name="harga_item" required>
                                </div>
                                <div class="form-group">
                                    <label for="kuantitas_item">Quantity: <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="kuantitas_item" name="kuantitas_item" value="1" required>
                                </div>
                                 <div class="form-group">
                                     <label for="discount_item">Discount:</label>
                                     <input type="text" class="form-control" id="discount_item" name="discount_item">
                                 </div>
                                <div class="form-group" style="display: none;">
                                    <label for="total_after_discount">Discount Amount: </label>
                                    <span id="total_after_discount"></span>
                                </div>
                                <div class="form-group">
                                    <label for="total_item">Total: <small class="text-danger">*</small></label>
                                    <input readonly type="text" class="form-control" id="total_item" name="total_item">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default margin-r-5"
                                    data-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn bg-blue" data-toggle="modal" data-target="#modal-add-product">Tambah Produk</button>
            <div id="modal-add-product" class="modal fade" role="dialog" data-keyboard="false"
                 data-backdrop="static">
                <div class="modal-dialog modal-md">
                    <!-- Modal content-->
                    <form id="form-add-product" action="{{ route('products.store') }}" method="post"
                          onsubmit="saveProduct(event)" autocomplete="off">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Tambah Produk</h4>
                            </div>
                            <div class="modal-body">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" id="add_type" value="0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-price form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                                            <label for="price">Harga</label>
                                            <input type="number" name="price" id="price" class="form-control" placeholder="Harga" value="{{ old('name') }}" autofocus>
                                            @if($errors->has('price'))
                                                <span class="help-block">
                                            <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                            <label for="name">Nama Produk</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name') }}" autofocus>
                                            @if($errors->has('name'))
                                                <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" id="btn-reset-add-customer" class="btn btn-danger pull-left" onclick="document.getElementById('form-add-product').reset();document.querySelector('#form-add-product #price').focus()">Reset</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal"
                                        onclick="document.getElementById('form-add-product').reset();document.querySelector('#form-add-product #price').focus()"
                                        style="margin-right: 15px;">Batalkan</button>
                                <button type="submit" class="btn bg-blue">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 value-items">
            <div class="box box-info">
                <div class="box-header with-border-product">
                    <h3 class="box-title">Pembayaran</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                title="" data-original-title="Collapse Form Pembayaran">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
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
                            <option value="">-</option>
{{--                            @foreach($payment_merchants as $payment_merchant)--}}
{{--                                <option value="{{ $payment_merchant->id }}">{{ $payment_merchant->name }}</option>--}}
{{--                            @endforeach--}}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 value-items">
            <div class="box box-primary">
                <div class="box-header with-border-product">
                    <h3 class="box-title">Nilai</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="" data-original-title="Collapse Form Nilai">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('order-products.store') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" value="">
                        {{ csrf_field() }}
                        <p class="margin-b-2" style="display: none;"><b>Bruto: </b><span id="bruto"></span></p>
                        <p class="margin-b-2"><b>Sub total: </b><span id="sub_total"></span></p>
                        <p class="margin-b-2"><b>Discount: </b><input readonly type="text" id="discount" name="discount" value="" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2" style="display: none;"><b>Netto: </b><span id="netto"></span></p>
                        <p class="margin-b-2"><i>INCLUDED PPN: </i><span id="tax">11%</span></p>
                        <p class="margin-b-2"><b>Total: </b><span id="total"></span></p>
                        <p class="margin-b-2" style="display: none;"><b>VAT: </b><span id="vat"></span></p>
                        {{-- <p class="margin-b-2"><b>Pembayaran: </b><input type="text" id="pembayaran" name="pembayaran" value="0" readonly class="form-control" style="display: inline"></p> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 value-items">
            {{--  <button type="submit" class="btn bg-olive pull-left" data-toggle="modal" data-target="#modal-take" style="margin-right: 15px;"> --}}
            {{--      <i class="fa fa-fw fa-save"></i> --}}
            {{--      <span>Ambil</span> --}}
            {{--  </button> --}}
            {{-- <!-- Modal --> --}}
            {{-- <div id="modal-take" class="modal fade" role="dialog"> --}}
            {{--     <div class="modal-dialog modal-lg"> --}}
            {{--         <!-- Modal content--> --}}
            {{--         <form id="add-customer-form" action="{{ route('customers.store') }}" method="post" onsubmit="saveCustomer(event)"> --}}
            {{--             <div class="modal-content"> --}}
            {{--                 <div class="modal-header"> --}}
            {{--                     <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
            {{--                     <h4 class="modal-title">Ambil</h4> --}}
            {{--                 </div> --}}
            {{--                 <div class="modal-body"> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type">Metode Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type" name="type" required> --}}
            {{--                             @foreach ($products as $product) --}}
            {{--                                 <option value="{{ $product->id }}">{{ $product->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type_merchant">Penyedia Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type_merchant" name="type_merchant" required> --}}
            {{--                             @foreach ($payment_merchants as $payment_merchant) --}}
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
            <button type="button" class="btn bg-purple pull-left" id="btn-order-product" style="margin-right: 15px;"
                onclick="createOrder()">
                <i class="fa fa-fw fa-save"></i>
                <span>Simpan</span>
            </button>
            <a href="{{ route('order-products.index') }}" class="btn btn-default pull-left"><i class="fa fa-fw fa-close"></i>
                Batalkan</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script type="text/javascript" src="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.js') }}"></script>
    <script>
        $('#items').hide();
        $('.value-items').hide();
        const products = @json($products);
        const items = [];

        function getTypeById(nameKey, myArray) {
            for (let i = 0; i < myArray.length; i++) {
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
                let r = Math.random() * 16 | 0,
                    v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function showEditItemForm(index) {
            $('#item_element').val(index);

            const dataItem = items[index];

            $(`#type_item_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_item_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const kuantitas = $('#kuantitas_item_edit').val(parseInt(dataItem.kuantitas, 10).toLocaleString('id-ID'));
            const discount = $('#discount_item_edit').val(dataItem.discount_item === 0 ? '' : parseInt(dataItem.discount_item, 10).toLocaleString('id-ID'));
            const harga = $('#harga_item_edit').val(parseInt(dataItem.harga, 10).toLocaleString('id-ID'));

            if(dataItem.discount_item > 100) {
                $('#total_after_discount_edit').text('').parent().css('display', 'none');
            } else {
                $('#total_after_discount_edit').text((dataItem.harga * dataItem.kuantitas * (dataItem.discount_item / 100)).toLocaleString('id-ID')).parent().css('display', 'inline');
            }
            $('#kuantitas_item_edit, #discount_item_edit').trigger('input');
        }

        function removeItem(e, el, index) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS item ini?") === true) {

                items.splice(index, 1);
                renderItems();

                if (items && !items.length) {
                    $('#items').hide();
                    $('.value-items').hide();
                }

                const message = 'Item berhasil dihapus';
                $('.top-right').notify({
                    message: {
                        text: `Sukses! ${message}`
                    }
                }).show();

            }
        }

        function selectCustomerById(customerId) {
            $.ajax({
                type: 'GET',
                url: '{{ route('customer.search') }}' + '?term=' + customerId + '&_type=query&q=' +
                customerId, // Asumsi endpoint mendukung query parameter 'id'
                success: function(data) {
                    if (data && data.length > 0) {
                        let item = data[data.length -
                        1]; // asumsi data kembali sebagai array dan pelanggan yang dicari selalu index 0
                        let newOption = new Option(item.name, item.id, true, true);

                        $(newOption).data('address', item.address);
                        $(newOption).data('phone', item.phone_number);

                        $('#customer').append(newOption).trigger('change');
                        $('#customer').trigger({
                            type: 'select2:select',
                            params: {
                                data: item,
                            }
                        });
                    }
                }
            });
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
                    // console.log(response);
                    const customerId = response.id;
                    selectCustomerById(customerId);
                    const type = 'success';
                    const message = 'Pelanggan berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');

                    $('#customer').val(customerId).trigger('change');

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
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');
                }
            });
        }

        function saveProduct(event) {
            event.preventDefault();
            $.ajax({
                url: $('#form-add-product').attr('action'),
                method: 'POST',
                data: $('#form-add-product').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    // console.log(response);
                    const productId = response.id;
                    const type = 'success';
                    const message = 'Barang berhasil disimpan!'
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
                    $('#modal-add-product').modal('hide');

                    $('#type').val(productId).trigger('change');

                    $('#form-add-product').trigger("reset");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Barang tidak bisa disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-product').modal('hide');
                }
            });
        }

        function resetFormAddItem() {
            $('#add-item-form, #add-customer-form').trigger("reset");
        }

        function renderItems() {
            const tbody = $('#table-items tbody'); // Ganti '#itemTable' dengan ID dari elemen tabel Anda
            tbody.empty(); // Kosongkan isi tabel sebelum menambahkan item baru


            // Loop melalui setiap item dan tambahkan baris HTML untuk masing-masing item
            items.forEach(function(item, index) {
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
                            <b>${(parseInt(item.harga, 10) * parseInt(item.kuantitas, 10)).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
                            <b>
                                <!-- ${info_discount.toLocaleString('id-ID')}  -->
                                ${item.discount_item.toLocaleString('id-ID')}${item.discount_item > 100 ? '' : '%'} ${item.discount_item > 100 ? '' : "(" + (parseInt(item.harga, 10) * parseInt(item.kuantitas, 10) * (item.discount_item / 100)).toLocaleString('id-ID') + ")"}
                            </b>
                        </td>
                        <td class="text-center">
                            <b>${(parseInt(item.harga, 10) * parseInt(item.kuantitas, 10) - parseInt(info_discount, 10)).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
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
            $('.value-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            // Ambil nilai dari form
            const type = $('#type_item').val();
            const keterangan = $('#keterangan').val();
            const harga = $('#harga_item').val().replace(/\./g, '');
            const kuantitas = $('#kuantitas_item').val().replace(/\./g, '');
            const jumlah = parseInt(harga, 10) * parseInt(kuantitas, 10);
            const discount_item = $('#discount_item').val() !== '' ? parseInt($('#discount_item').val().replace(/\./g, ''), 10) : 0;
            const total_after_discount = $('total_after_discount').text().replace(/\./g, '');

            // Buat objek item
            const newItem = {
                type,
                keterangan,
                harga,
                kuantitas,
                jumlah,
                discount_item,
            };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items.push(newItem); // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil ditambahkan!';
            $('.top-right').notify({
                message: {
                    text: `Sukses! ${message}`
                }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-add-item').modal('hide');
            $('#total_after_discount').text('').parent().css('display', 'none');

            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumValueItem();
            $('#discount').trigger('input');
        }

        function formEditItem(event) {
            $('#items').show();
            $('.value-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            const element = $('#item_element').val();

            // Ambil nilai dari form
            const type = $('#type_item_edit').val();
            const keterangan = $('#keterangan_edit').val();
            const harga = $('#harga_item_edit').val().replace(/\./g, '');
            const kuantitas = $('#kuantitas_item_edit').val().replace(/\./g, '');
            const jumlah = parseInt(harga, 10) * parseInt(kuantitas, 10);
            const discount_item = $('#discount_item_edit').val() !== '' ? parseInt($('#discount_item_edit').val().replace(/\./g, ''), 10) : 0;

            // Buat objek item
            const newItem = {
                type,
                keterangan,
                harga,
                kuantitas,
                jumlah,
                discount_item,
            };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items[element] = newItem; // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil diedit!';
            $('.top-right').notify({
                message: {
                    text: `Sukses! ${message}`
                }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-edit-item').modal('hide');
            $('#total_after_discount_edit').text('').parent().css('display', 'none');

            // Reset form
            $('#edit-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumValueItem();
            $('#discount').trigger('input');
        }

        function sumValueItem() {
            let bruto = 0;
            let discount = 0;

            for (let i = 0; i < items.length; i++) {
                // let info_discount = parseInt(items[i].discount_items, 10) > 100 ? items[i].discount_items : items[i].jumlah * (parseInt(items[i].discount_item, 10) / 100);
                bruto += parseInt(items[i].jumlah, 10);
                discount += parseInt(items[i].discount_item, 10) > 100 ? Number(items[i].discount_item) : items[i].jumlah * (parseInt(items[i].discount_item, 10) / 100);
            }

            $('#bruto').text(bruto.toLocaleString('id-ID'));
            $('#sub_total').text(bruto.toLocaleString('id-ID'));
            $('#discount').val((discount).toLocaleString('id-ID'));
        }

        function createOrder() {
            const customer_id = $('#customer_id').val();
            const site_id = $('#site_id option:selected').val();
            const bruto = $('#bruto').text().replace(/\./g, '');
            const discount = $('#discount').val().replace(/\./g, '');
            const netto = $('#netto').text().replace(/\./g, '');
            const vat = $('#vat').text().replace(/\./g, '');
            const total = $('#total').text().replace(/\./g, '');
            const payment_method = $('#payment_method').val();
            const payment_merchant = $('#payment_merchant').val();

            // console.log('FINAL RESULT');
            // console.table([{items}, {customer_id}, {site_id}, {bruto}]);

            const url = "{{ route('order-products.store') }}";
            $('.alert').alert('close');

            const alert = `
                        <div class="alert alert-warning alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Mohon tunggu!</strong> Penjualan sedang diproses...
                            &nbsp;<span class="glyphicon glyphicon-refresh spinning"></span>
                        </div>
                    `;
            $('#alert-container').html(alert);

            $.ajax({
                url,
                data: {
                    items,
                    customer_id,
                    site_id,
                    bruto,
                    discount: discount === '' ? 0 : discount,
                    netto,
                    vat,
                    total,
                    payment_method,
                    payment_merchant,
                },
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    console.log(response);
                    $('#alert-container').empty();
                    const type = 'success';
                    const message = 'Penjualan berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                            &nbsp;<span class="glyphicon glyphicon-ok"></span>
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                        window.location.reload();
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    $('#alert-container').empty();
                    const type = 'danger';
                    const message = 'Penjualan tidak berhasil disimpan!'
                    const statusCode = `${xhr.statusText} (${xhr.status})`;
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message} ${statusCode}
                        &nbsp;<span class="glyphicon glyphicon-remove"></span>
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert
                }
            });

            $('html, body').animate({
                scrollTop: $("#alert-container").offset().top - 125
            }, 1000);
        }

        function calculateIncludedVAT(totalValue, vatRate) {
            const vatRateDecimal = vatRate / 100;
            // console.log((totalValue * vatRateDecimal) / (1 + vatRateDecimal));
            return (totalValue * vatRateDecimal) / (1 + vatRateDecimal);
        }

        $(function() {
            $('#modal-add-customer').on('shown.bs.modal', function() {
                $('#add-customer-form #name').focus();
            });
            $('#modal-add-item').on('hidden.bs.modal', function() {
                resetFormAddItem();
            });
            $('#modal-edit-item').on('hidden.bs.modal', function() {
                $('#item_element').val('');
                // $(`#type_item_edit option[value='1']`).attr("selected");
                $('#keterangan_edit').val('');
                $('#harga_item_edit').val('');
                $('#edit-item-form').trigger('reset');
                resetFormAddItem();
            });
            $('#customer').select2({
                placeholder: '-- Pelanggan --',
                ajax: {
                    url: '{{ route('customer.search') }}', // Ganti dengan URL endpoint Anda
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

            $('#type_item').select2({
                dropdownAutoWidth: true,
                width: 'resolve',
                dropdownParent: $("#modal-add-item .modal-body"),
                placeholder: '-- Jenis Produk --',
                ajax: {
                    url: '{{ route('products.search', ['type' => 0]) }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        const options = [];
                        $.each(data, function(index, item) {
                            options.push({
                                id: item.id,
                                text: `${item.name}`,
                                data: {
                                    id: item.id,
                                    name: item.name,
                                    price: item.price
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

            $('#type_item').on('change', function(e) {
                const price = $(this).find(":selected").data('price');
                $('#harga_item').val(parseInt(price)).trigger('input')
                $('#kuantitas_item, #discount_item').trigger('input')
            });

            $('#type_item_edit').on('change', function(e) {
                const price = $(this).find(":selected").data('price');
                $('#harga_item_edit').val(parseInt(price)).trigger('input')
            });

            $('#customer').on('select2:select', function(e) {
                const data = e.params.data;

                let customerId, customerName, address, phone;

                if (data.data === undefined) {
                    customerId = data.id;
                    customerName = data.name;
                    address = data.address;
                    phone = data.phone_number;
                } else {
                    const dataAttribute = data.data;

                    customerId = data.id;
                    customerName = data.text;
                    address = dataAttribute.address;
                    phone = dataAttribute.phone;
                }

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

                $('#customer_id').val(customerId);
            });

            $('#discount_item, #discount_item_edit').on('input', function(e) {
                const discount_item = $(this).val();

                let digitsOnly = discount_item.replace(/\D/g, '');

                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

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
                    $('#total_after_discount_edit').text('').parent().css('display', 'none');
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
                } else {
                    if(Number(digitsOnly) > 100) {
                        total_item_edit.val(total_harga === 0 ? '' : (total_harga - Number(digitsOnly)).toLocaleString('id-ID'));
                        $('#total_after_discount_edit_edit').text('').parent().css('display', 'none');
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
                const discount = $(this).val() === '' ? 0 : $(this).val();

                // Hapus semua karakter selain digit
                let digitsOnly = discount === 0 ? 0 : discount.replace(/\D/g, '');

                let netto = bruto_value - (digitsOnly === 0 ? 0 : parseInt(digitsOnly, 10));

                let formattedNumber = '';
                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                if($(this).val() !== '') {
                    formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);

                const totalValue = $(this).val() === '' ? bruto.toLocaleString('id-ID') :  netto.toLocaleString('id-ID');
                $('#netto').text(totalValue);
                // console.log(totalValue);
                $('#total').text(totalValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                const vatRate = 11;
                let vatAmount = calculateIncludedVAT(netto, vatRate);
                vatAmount = vatAmount;
                $('#vat').text(vatAmount.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

            });

            $('#harga_item, #harga_item_edit').on('input', function(e) {
                const value = $(this).val();

                // Hapus semua karakter selain digit
                let digitsOnly = value.replace(/\D/g, '');

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);
            });

            $('#site_id').select2();
            // $('#city_id').select2();
            // $('.select2').select2({
            //     "language": {
            //         "noResults": function(){
            //             return "Nenhum registro encontrado.";
            //         }
            //     }
            // });

            var defaultSite = '{{ session('user.default_site.0') }}';
            if (defaultSite) {
                $('#site_id').val(defaultSite).trigger('change');
            }

        });
    </script>

@endsection
