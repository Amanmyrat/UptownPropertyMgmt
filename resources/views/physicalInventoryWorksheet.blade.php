@extends('app')
@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="table-responsive">
                <h1 class="mt-4">Physical Worksheet</h1>
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
            if (element[7] != 'Total' && element[7] != '-') {
                total += element[7];
            }
            if (!properties.includes(element[8])) {
                properties.push(element[8]);
            }
        });
        var table = null;
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
        });
    </script>
@endsection
