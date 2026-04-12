@extends('layouts.AdminLTE.index')

@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('stock-move.index') }}" class="link_menu_page">
			<i class="fa fa-list-ol"></i> Gudang
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ request('type') === 'out' ? 'Barang Keluar' :  (request('type') === 'move' ? 'Mutasi Barang' : 'Barang Masuk') }}</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Site">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					 <form action="" method="post" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="active" value="1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('warehouse_id') ? 'has-error' : '' }}">
                                    <label for="warehouse_id">Gudang</label>
                                    <!-- <input type="text" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Name" required value="" autofocus> -->
                                    <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                        <option disabled selected> -- Pilih Gudang -- </option>
                                        <option> 001 - Gundang A </option>
                                        <option> 002 - Gundang B </option>
                                        <option> 003 - Gundang C </option>
                                    </select>
                                </div>
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name">Product / Item</label>
                                    <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                        <option disabled selected> -- Pilih Produk -- </option>
                                        <option> 001 - Produk A </option>
                                        <option> 002 - Produk B </option>
                                        <option> 003 - Produk C </option>
                                    </select>
                                </div>
                                <div class="form-group {{ $errors->has('product_quantity') ? 'has-error' : '' }}">
                                    <label for="product_quantity">Jumlah</label>
                                    <input type="text" name="product_quantity" id="product_quantity" class="form-control" placeholder="Jumlah Barang" required value="">
                                </div>
                                <div class="form-group {{ $errors->has('uom') ? 'has-error' : '' }}">
                                    <label for="uom">UOM</label>
                                    <select class="form-control" id="uom" name="uom">
                                        <option disabled selected> -- Pilih Satuan -- </option>
                                        <option> Pcs </option>
                                        <option> Unit </option>
                                        <option> Box </option>
                                        <option> Pack </option>
                                        <option> Kilogram </option>
                                        <option> Meter </option>
                                    </select>
                                </div>

                                <div class="form-group {{ $errors->has('note') ? 'has-error' : '' }}">
                                    <label for="note">Notes</label>
                                    <input type="text" name="note" id="note" class="form-control" placeholder="Notes" required value="">
                                </div>

                                @if (request('type') === 'move')
                                <div class="form-group {{ $errors->has('origin_id') ? 'has-error' : '' }}">
                                    <label for="origin_id">Gudang Asal</label>
                                    <!-- <input type="text" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Name" required value="" autofocus> -->
                                    <select class="form-control" id="origin_id" name="origin_id" required>
                                        <option disabled selected> -- Pilih Gudang -- </option>
                                        <option> 001 - Gundang A </option>
                                        <option> 002 - Gundang B </option>
                                        <option> 003 - Gundang C </option>
                                    </select>
                                </div>
                                <div class="form-group {{ $errors->has('dest_id') ? 'has-error' : '' }}">
                                    <label for="dest_id">Gudang Tujuan</label>
                                    <!-- <input type="text" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Name" required value="" autofocus> -->
                                    <select class="form-control" id="dest_id" name="dest_id" required>
                                        <option disabled selected> -- Pilih Gudang -- </option>
                                        <option> 001 - Gundang A </option>
                                        <option> 002 - Gundang B </option>
                                        <option> 003 - Gundang C </option>
                                    </select>
                                </div>
                                @endif

                                @if (request('type') === 'in')
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="harga_product_in">Harga</label>
                                    <input type="number" name="price" id="price" class="form-control" placeholder="Harga Pembelian" value="0">
                                </div>
                                @endif
                                @if (request('type') === 'in')
                                <div class="form-group {{ $errors->has('vendor') ? 'has-error' : '' }}">
                                    <label for="vendor">Vendor / Partner</label>
                                    <select class="form-control" id="vendor_id" name="vendor_id" required>
                                        <option disabled selected> -- Pilih Vendor / Partner -- </option>
                                        <option> 001 - vendor A </option>
                                        <option> 002 - vendor B </option>
                                        <option> 003 - vendor C </option>
                                    </select>
                                </div>
                                @endif
                                @if (request('type') === 'in')
                                <div class="form-group {{ $errors->has('shipper_name') ? 'has-error' : '' }}">
                                    <label for="shipper_name">Pengirim</label>
                                    <input type="text" name="shipper_name" id="shipper_name" class="form-control" placeholder="Pengirim (opsional)" value="">
                                </div>
                                @endif
                                @if (request('type') === 'in')
                                <div class="form-group {{ $errors->has('transaction_proof') ? 'has-error' : '' }}">
                                    <label for="transaction_proof">Bukti Transaksi (gambar)</label>
                                    <input type="file" name="transaction_proof" id="transaction_proof" class="form-control" accept="image/*" onchange="previewImage(event)">
                                    <small class="form-text text-muted">Format: JPG, PNG. Maks 2MB.</small>
                                    <div id="preview-container" style="margin-top: 10px;">
                                        <img id="preview-image" src="#" alt="Preview" style="max-height: 200px; display: none; border: 1px solid #ccc; padding: 5px;" />
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-12">
                               <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-fw fa-plus"></i> Submit</button>
                                <a href="{{ route('sites.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Batalkan</a>
                            </div>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('layout_js')

    <script>
        $(function(){
            $('.select2').select2({
                "language": {
                    "noResults": function(){
                        return "Nenhum registro encontrado.";
                    }
                }
            });
        });

    </script>
    <script>
        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();
            const preview = document.getElementById('preview-image');

            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

@endsection
