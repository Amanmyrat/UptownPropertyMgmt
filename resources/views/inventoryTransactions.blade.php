@extends('app')
@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="inventoryMainInfos">
                <div>
                    From: {{ $report['fromDate'] }}
                </div>
                <div>To: {{ $report['toDate'] }}</div>
                <div>Total: <span class="inventoryTotal"></span></div>
            </div>
            <div class="table-responsive">
                <h1 class="mt-4">Inventory Transactions</h1>
                <table class="table table-bordered" id="outInventory" width="100%" cellspacing="0">
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        var total = 0;
        var properties = [];
        var inventoryData = @json($report['data']);
        inventoryData.forEach(element => {
            if (element[8] != 'Total' && element[8] != '-') {
                total += element[8];
            }
            if (!properties.includes(element[9])) {
                properties.push(element[9]);
            }
        });
        var table = null;
        document.querySelector('.inventoryTotal').innerHTML = total;
        $(document).ready(function() {
            table = $('#outInventory').DataTable({
                data: inventoryData,
                dom: 'Bfrtip',
                buttons: [{
                    text: '<span class="fa fa-table" aria-hidden="true"></span> Excel export',
                    attr: {
                        id: 'btn-add-review'
                    },
                    action: function(e, dt, node, config) {
                        console.log("click");
                    },
                }],
                columns: @json($report['columns']).map(function(d) {
                    return {
                        "title": d
                    };
                }),
                paging: false,
                "order": [
                    [1, 'asc']
                ]
            });
            table.on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        });
        var link = document.createElement('a');

        link.href = "#";
        properties.reverse().forEach(property => {

            var linkClone = link.cloneNode()
            linkClone.addEventListener('click', (el) => {
                table.columns(9).search(property).draw();

            })
            linkClone.innerHTML = property;
            document.querySelector('.propertyButtons').appendChild(linkClone);

        });
    </script>
@endsection
