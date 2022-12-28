@extends('app')
@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="table-responsive">
                <h1 class="mt-4">Employee Work Orders</h1>
                <table class="table table-bordered" id="propertyReportTable" width="100%" cellspacing="0">
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        var myTable = null;
        var columns = @json($columns);
        var balanceColumn = columns.indexOf("Balance");
        var report = @json($report);

        $(document).ready(function() {
            myTable = $('#propertyReportTable').DataTable({
                data: @json($report),
                columns: columns.map(function(d) {
                    return {
                        "title": d
                    };
                }),
                "scrollX": true,
                paging: false,
                dom: 'BPlfrtip',
                columnDefs: [{
                        searchPanes: {
                            show: true
                        },
                        targets: [0]
                    },
                    {
                        searchPanes: {
                            show: false
                        },
                        targets: ['_all']
                    }
                ],
                buttons: [{
                    text: '<span class="fa fa-table" aria-hidden="true"></span> Excel export',
                    attr: {
                        id: 'btn-add-review'
                    },
                    action: function(e, dt, node, config) {
                        console.log("click");
                    },
                }],

                "initComplete": function(settings, json) {
                },
                order: [],


            });
            $(myTable.table().node()).parent().scrollLeft($('.dataTables_scrollBody').get(0).scrollWidth)
        });
    </script>
@endsection
@section('extraImports')
@endsection
