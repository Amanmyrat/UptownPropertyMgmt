@extends('app')
@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="table-responsive">
                <h1>Collections Report</h1>
                <table class="table table-bordered" id="collectionTable" width="100%" cellspacing="0">

                </table>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <h1>Vacancy Report</h1>
                <table class="table table-bordered" id="vacancyTable" width="100%" cellspacing="0">

                </table>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        columns = @json($propertyCollections[1]);
        totalStatisticsColumns = [];
        columns.forEach((el, index) => {
            if (el == "Prev Month Today" || el == "12 Months Max Collection" ||
                el == "Max Collection 5% Increase" || el == "Total SQ Feet" ||
                el == "Total Charged Rent To Current Tenant" || el == "Total Uncollected Rent") {
                totalStatisticsColumns.push(index);
            }
        })

        function initTable(collectionData, tableId, columns, type) {
            var myTable = null
            $.fn.dataTable.ext.errMode = 'none';
            $(document).ready(function() {
                myTable = $('#' + tableId).DataTable({

                    data: collectionData,
                    columns: columns.map(function(d) {
                        return {
                            "title": d
                        };
                    }),
                    "scrollX": true,
                    paging: false,
                    dom: type === 'collection' ? 'Bfrtip' : 'frtip',
                    fixedColumns: {
                        leftColumns: 2
                    },
                    "bInfo": false, // hide "Showing 1 of N Entries" 
                    buttons:  [{
                        text: '<span class="fa fa-table" aria-hidden="true"></span> Excel export',
                        attr: {
                            id: 'btn-add-review'
                        },
                        action: function(e, dt, node, config) {
                            console.log("click");

                            window.open('{{ route("report_download") }}', '_blank');
                        },
                    }] ,
                    "initComplete": function(settings, json) {},
                    order: [],
                    rowCallback: function(row, data) {
                        if (type == "collection") {
                            totalStatisticsColumns.forEach(index => {
                                color = "#B4C6E7"
                                if (row.childNodes[1].innerText.search(/TOTAl/i) != -1) {
                                    color = "#32C732";
                                }
                                row.childNodes[index].style.background = color;
                            });
                        }
                    }

                });
                $(myTable.table().node()).parent().scrollLeft($('.dataTables_scrollBody').get(0).scrollWidth)

                if (type == "vacancy") {
                    $('.dataTables_scrollHeadInner tr th:nth-of-type(8)')[0].style.background = "#00B050";
                    $('.dataTables_scrollHeadInner tr th:nth-of-type(9)')[0].style.background = "red";
                    $('.dataTables_scrollHeadInner tr th:nth-of-type(10)')[0].style.background = "#996633";
                    $('.dataTables_scrollHeadInner tr th:nth-of-type(11)')[0].style.background = "black";
                    $('.dataTables_scrollHeadInner tr th:nth-of-type(11)')[0].style.color = "red";
                }
                
            });

        }

        initTable(@json($vacancyReport[1]), "vacancyTable", @json($vacancyReport[0]), "vacancy");
        initTable(@json($propertyCollections[0]), "collectionTable", columns, "collection");
    </script>
@endsection
@section('extraImports')
@endsection
