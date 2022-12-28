@extends('app')
@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $property->shortname }}</h1>
        <div class="card-body">
            <a href="#" onclick="ShowRentRoll()" class="btn btn-secondary" tabindex="0" aria-controls="collectionTable" type="button" id="btn-add-review">
                <span>Rent Roll</span>
            </a>   
            <a href="#" onclick="ShowDelinquencies()" class="btn btn-secondary" tabindex="0" aria-controls="collectionTable" type="button" id="btn-add-review">
                <span>Delinquencies</span>
            </a>   
            <a href="{{route('vacancyReport')}}" class="btn btn-secondary" tabindex="0" aria-controls="collectionTable" type="button" id="btn-add-review">
                <span>Vacancy Map</span>
            </a> 
        </div>

        <div class="card-body">
            <div class="table-responsive">

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
        var unitAvailabilities = @json($unitAvailabilities);

        function getUnitAvailabilityStatus($unitName) {
            unitAvailabilities.forEach(unit => {
                if ($unitName == unit['unitname'])
                    return unit['statusId'];

            });
            return 0;
        }
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
                fixedColumns: {
                    leftColumns: 2
                },
                "initComplete": function(settings, json) {},
                order: [],
                rowCallback: function(row, data) {}
            });
            $(myTable.table().node()).parent().scrollLeft($('.dataTables_scrollBody').get(0).scrollWidth)

        });

        function ShowDelinquencies() {
            $.fn.dataTable.ext.search = [];
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var balance = parseFloat(data[balanceColumn]) || 0; // use data for the age column

                    if (balance > 0) {
                        return true;
                    }
                    return false;
                }

            );
            myTable.draw();
        }

        function ShowRentRoll() {
            $.fn.dataTable.ext.search = [];

            myTable.draw();
        }
    </script>
@endsection
