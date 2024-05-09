<aside class="main-sidebar">
	<section class="sidebar">
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header" style="color:#fff;"> MAIN MENU <i class="fa fa-level-down"></i></li>
			<li class="
						{{ Request::segment(1) === null ? 'active' : null }}
						{{ Request::segment(1) === 'home' ? 'active' : null }}
					  ">
				<a href="{{ route('home') }}" title="Dashboard"><i class="fa fa-dashboard"></i> <span> Dashboard</span></a>
			</li>

			<li class="{{Request::segment(1) === 'orders' ? 'active' : null}}">
				<a href="{{ route('orders.index') }}" title="Pesanan"><i class="fa fa-shopping-cart"></i> <span> Pesanan</span></a>
			</li>

			@if(Request::segment(1) === 'profile')

			<li class="{{ Request::segment(1) === 'profile' ? 'active' : null }}">
				<a href="{{ route('profile') }}" title="Profil"><i class="fa fa-user"></i> <span> Profil</span></a>
			</li>

			@endif
			<li class="treeview
				{{ Request::segment(1) === 'config' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'user' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'role' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'cities' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'clients' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'sites' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'item-types' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'payment-methods' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'payment-merchants' ? 'active menu-open' : null }}
				">
				<a href="#" title="Pengaturan">
					<i class="fa fa-gear"></i>
					<span>Pengaturan</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					@if (Auth::user()->can('root-dev', ''))
						<li class="{{ Request::segment(1) === 'config' && Request::segment(2) === null ? 'active' : null }}">
							<a href="{{ route('config') }}" title="App Config">
								<i class="fa fa-gear"></i> <span> Settings App</span>
							</a>
						</li>
					@endif
					<li class="
						{{ Request::segment(1) === 'user' ? 'active' : null }}
						{{ Request::segment(1) === 'role' ? 'active' : null }}
						">
						<a href="{{ route('user') }}" title="Pengguna">
							<i class="fa fa-user"></i> <span> Pengguna</span>
						</a>
					</li>
					<li class="{{Request::segment(1) === 'cities' ? 'active' : null}}">
						<a href="{{ route('cities.index') }}" title="Kota"><i class="fa fa-map-marker"></i> <span> Kota</span></a>
					</li>
		
					<li class="{{Request::segment(1) === 'clients' ? 'active' : null}}">
						<a href="{{ route('clients.index') }}" title="Pelanggan"><i class="fa fa-users"></i> <span> Pelanggan</span></a>
					</li>
		
					<li class="{{Request::segment(1) === 'sites' ? 'active' : null}}">
						<a href="{{ route('sites.index') }}" title="Cabang"><i class="fa fa-list-ol"></i> <span> Cabang</span></a>
					</li>
		
					<li class="{{Request::segment(1) === 'item-types' ? 'active' : null}}">
						<a href="{{ route('item-types.index') }}" title="Jenis Barang"><i class="fa fa-list-alt"></i> <span> Jenis Barang</span></a>
					</li>
		
					<li class="{{Request::segment(1) === 'payment-methods' ? 'active' : null}}">
						<a href="{{ route('payment-methods.index') }}" title="Metode Pembayaran"><i class="fa fa-exchange"></i> <span> Metode Pembayaran</span></a>
					</li>
		
					<li class="{{Request::segment(1) === 'payment-merchants' ? 'active' : null}}">
						<a href="{{ route('payment-merchants.index') }}" title="Penyedia Pembayaran"><i class="fa fa-building"></i> <span> Penyedia Pembayaran</span></a>
					</li>
				</ul>
			</li>
		</ul>
	</section>
</aside>
