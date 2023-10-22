<style>
  .sidebar a.nav-link.active {
    color: #fff !important
  }


</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-warning navbar-light elevation-4 sidebar-no-expand">
  <!-- Brand Logo -->
  <a href="<?php echo base_url ?>admin" class="brand-link bg-warning text-sm">
    <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="Store Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8;width: 1.5rem;height: 1.5rem;max-height: unset">
    <span class="brand-text font-weight-light text-light">
      <?php echo $_settings->info('short_name') ?>
    </span>
  </a>
  <!-- Sidebar -->
  <div
    class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
    <div class="os-resize-observer-host observed">
      <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
    </div>
    <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
      <div class="os-resize-observer"></div>
    </div>
    <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
    <div class="os-padding">
      <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
        <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
          <!-- Sidebar user panel (optional) -->
          <div class="clearfix"></div>
          <!-- Sidebar Menu -->
          <nav class="mt-1">
            <ul
              class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child"
              data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item dropdown">
                <a href="./" class="nav-link nav-home">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Dashboard
                  </p>
                </a>
              </li>
              <?php if ($_settings->userdata('type') != 3): ?>
                <li class="nav-item dropdown">
                  <a href="./?page=sales/manage_sale" class="nav-link nav-sales_manage_sale">
                    <i class="nav-icon fas fa-cash-register"></i>
                    <p>
                      POS
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="./?page=orders" class="nav-link nav-orders">
                    <i class="nav-icon fas fa-th-list"></i>
                    <p>
                      Order List
                    </p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($_settings->userdata('type') != 2): ?>
                <li class="nav-item dropdown">
                  <a href="./?page=kitchen" class="nav-link nav-kitchen">
                    <i class="nav-icon fas fa-table"></i>
                    <p>
                      Kitchen Side
                    </p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($_settings->userdata('type') == 1): ?>
                <li class="nav-header">Master List</li>
                <li class="nav-item dropdown">
                  <a href="./?page=categories" class="nav-link nav-categories">
                    <i class="nav-icon fas fa-th-list"></i>
                    <p>
                      Menu Category
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="./?page=menus" class="nav-link nav-menus">
                    <i class="nav-icon fas fa-hamburger"></i>
                    <p>
                      Menu List
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="./?page=coupon" class="nav-link nav-coupon">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p>
                      Discount & Coupon
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                <a href="./?page=reports" class="nav-link nav-reports">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                      Reports
          
                    </p>
                  </a>
                </li>
                <li class="nav-header">Maintenance</li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <p>
                      User List
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                    <i class="nav-icon fas fa-tools"></i>
                    <p>
                      System Information
                    </p>
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar-corner"></div>
  </div>
  <!-- /.sidebar -->
</aside>
<script>
  $(document).ready(function () {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    page = page.replace(/\//g, '_');
    if ($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active')
      if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
        $('.nav-link.nav-' + page).addClass('active')
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
      }
      if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open')
      }

    }
    // $('.nav-link.active').addClass('bg-gradient-warning')
    $('.main-sidebar .nav-link').each(function () {
      var text = $(this).text()
      $(this).attr('title', text)
    })
  })
</script>