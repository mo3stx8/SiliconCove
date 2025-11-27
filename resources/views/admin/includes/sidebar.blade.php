<!-- SIDEBAR -->
<section id="sidebar">
    <a href="/" class="brand text-decoration-none">
        <i class="fas fa-store me-5"></i>SiliconCove
    </a>
    <ul class="side-menu">
        <!-- Add 'active' class if the current route is 'admin.dashboard' -->
        <li><a href="{{ route('admin.dashboard') }}" class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }} text-decoration-none"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>

        <li class="divider" data-text="Management">Management</li>

        <li>
            <!-- Add 'active' class if the current route is 'admin.all-users' -->
            <a href="{{ route('admin.all-users') }}" class="{{ Route::currentRouteName() == 'admin.all-users' ? 'active' : '' }} text-decoration-none">
                <i class='bx bxs-bar-chart-alt-2 icon'></i> All Users
            </a>
        </li>

        <li>
            <a href="#" class="text-decoration-none"><i class='bx bxs-inbox icon'></i> Manage Products <i class='bx bx-chevron-right icon-right'></i></a>
            <ul class="side-dropdown">
                <li><a href="{{ route('admin.add-product') }}" class="{{ Route::currentRouteName() == 'admin.add-product' ? 'active' : '' }} text-decoration-none"><i class='bx bx-plus-circle'></i> Add Product</a></li>
                <li><a href="{{ route('admin.view-products') }}" class="{{ Route::currentRouteName() == 'admin.view-products' ? 'active' : '' }} text-decoration-none"><i class='bx bx-list-ul'></i> View Products</a></li>
            </ul>
        </li>

        <li>
            <a href="#" class="text-decoration-none"><i class='bx bxs-box icon'></i> Manage Orders <i class='bx bx-chevron-right icon-right'></i></a>
            <ul class="side-dropdown">
                <li><a href="{{ route('admin.view-orders') }}" class="{{ Route::currentRouteName() == 'admin.view-orders' ? 'active' : '' }} text-decoration-none"><i class='bx bx-list-check'></i> View Orders</a></li>
                <li><a href="{{ route('admin.pending-orders') }}" class="{{ Route::currentRouteName() == 'admin.pending-orders' ? 'active' : '' }} text-decoration-none"><i class='bx bx-loader-circle'></i> Pending Orders</a></li>
                <li><a href="{{ route('admin.completed-orders') }}" class="{{ Route::currentRouteName() == 'admin.completed-orders' ? 'active' : '' }} text-decoration-none"><i class='bx bx-check-circle'></i> Completed Orders</a></li>
            </ul>
        </li>

        <li>
            <a href="#" class="text-decoration-none"><i class='bx bxs-receipt icon'></i> Manage Transactions <i class='bx bx-chevron-right icon-right'></i></a>
            <ul class="side-dropdown">
                <li><a href="{{ route('admin.process-refunds') }}" class="{{ Route::currentRouteName() == 'admin.process-refunds' ? 'active' : '' }} text-decoration-none"><i class='bx bx-undo'></i> Process Refunds</a></li>
            </ul>
        </li>

        <li>
            <!-- Add 'active' class if the current route is 'admin.analytics' -->
            <a href="{{ url('/admin/analytics') }}" class="{{ Route::currentRouteName() == 'admin.analytics' ? 'active' : '' }} text-decoration-none">
                <i class='bx bx-line-chart icon'></i> View Sales Analytics
            </a>
        </li>
    </ul>
</section>
