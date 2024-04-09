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
				<a href="{{ route('orders.index') }}" title="Orders"><i class="fa fa-shopping-cart"></i> <span> Order</span></a>
			</li>

            <li class="{{Request::segment(1) === 'cities' ? 'active' : null}}">
                <a href="{{ route('cities.index') }}" title="City"><i class="fa fa-map-marker"></i> <span> City</span></a>
            </li>

            <li class="{{Request::segment(1) === 'clients' ? 'active' : null}}">
                <a href="{{ route('clients.index') }}" title="Clients"><i class="fa fa-users"></i> <span> Client</span></a>
            </li>

            <li class="{{Request::segment(1) === 'sites' ? 'active' : null}}">
                <a href="{{ route('sites.index') }}" title="Site"><i class="fa fa-list-ol"></i> <span> Sites</span></a>
            </li>

            <li class="{{Request::segment(1) === 'item-types' ? 'active' : null}}">
                <a href="{{ route('item-types.index') }}" title="Item Type"><i class="fa fa-list-alt"></i> <span> Item Type</span></a>
            </li>

            <li class="{{Request::segment(1) === 'payment-methods' ? 'active' : null}}">
                <a href="{{ route('payment-methods.index') }}" title="Payment Method"><i class="fa fa-exchange"></i> <span> Payment Method</span></a>
            </li>

            <li class="{{Request::segment(1) === 'payment-merchants' ? 'active' : null}}">
                <a href="{{ route('payment-merchants.index') }}" title="Payment Merchant"><i class="fa fa-building"></i> <span> Payment Merchant</span></a>
            </li>

			@if(Request::segment(1) === 'profile')

			<li class="{{ Request::segment(1) === 'profile' ? 'active' : null }}">
				<a href="{{ route('profile') }}" title="Profile"><i class="fa fa-user"></i> <span> PROFILE</span></a>
			</li>

			@endif
			<li class="treeview
				{{ Request::segment(1) === 'config' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'user' ? 'active menu-open' : null }}
				{{ Request::segment(1) === 'role' ? 'active menu-open' : null }}
				">
				<a href="#">
					<i class="fa fa-gear"></i>
					<span>SETTINGS</span>
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
						<a href="{{ route('user') }}" title="Users">
							<i class="fa fa-user"></i> <span> Users</span>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</section>
</aside>
