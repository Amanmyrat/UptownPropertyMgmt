@inject('rep', 'App\Library\Services\ApiRepository')
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="">
                    Uptown Property Management
                </div>
                <div class="sb-sidenav-menu-heading">Main</div>
                <a class="nav-link" href="/">
                    <div class="sb-nav-link-icon"><i class="fas fa-home"></i></i></div>
                    Dashboard
                </a>
                {{-- <a class="nav-link" href="/inventoryTransactions">
                    <div class="sb-nav-link-icon"><i class="fas fa-business-time"></i></i></div>
                   Inventory Transactions
                </a>
                <a class="nav-link" href="/inventoryPhysicalWorksheet">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></i></div>
                    Inventory Physical Worksheet
                </a> --}}
                <a class="nav-link" href="/employeeWorkOrdersReport">
                    <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                    Employee Work Orders Report
                </a>
                {{-- <a class="nav-link" href="/transactionsReport">
                    <div class="sb-nav-link-icon"><i class="fas fa-random"></i></div>
                    Transactions Report
                </a> --}}
                <a class="nav-link" href="/vacancyMapReport">
                    <div class="sb-nav-link-icon"><i class="fas fa-globe-americas"></i></div>
                    Shady Vacancy Map Report
                </a>
            </div>
        </div>
    </nav>
</div>
