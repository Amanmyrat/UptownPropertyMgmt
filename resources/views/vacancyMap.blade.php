@extends('app')
@section('content')
    
    <div class="card-body">
        <div style="margin-left: 20px;">
            <h1 class="mt-4">{{$property->shortname}}</h1>

            <a href="{{route('propertyReport', $property->shortname)}}" class="btn btn-secondary" tabindex="0" aria-controls="collectionTable" type="button" id="btn-add-review">
                <span>Rent Roll</span>
            </a>   
            <a href="{{route('report_download.property')}}" class="btn btn-secondary" tabindex="0" aria-controls="collectionTable" type="button" id="btn-add-review">
                <span>
                    <svg class="svg-inline--fa fa-table fa-w-16" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="table" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                        <path fill="currentColor" d="M464 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zM224 416H64v-96h160v96zm0-160H64v-96h160v96zm224 160H288v-96h160v96zm0-160H288v-96h160v96z" data-darkreader-inline-fill="" style="--darkreader-inline-fill:currentColor;"></path>
                    </svg>
                    Excel export
                </span>
            </a>   
        </div>
    </div>

    <div id="svgContainer"></div>
    <div id="containerfluid">
        <div id="vacanciesTab"  class="row" style="margin:0;">
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height: 75px">
                        TOTAL UNITS
                    </div>
                    <div id="totalUnits">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height: 75px">
                        TOTAL OCCUPIED
                    </div>
                    <div id="totalOccupied">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height: 75px">
                        TOTAL VACANT UNITS
                    </div>
                    <div id="totalVacantUnits">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height: 75px">
                        OCCUPANCY PERCENTAGE
                    </div>
                    <div id="occupancyPercentage">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="background-color:#00B050; height: 75px">
                        VACANCY DONE
                    </div>
                    <div id="vacantDone">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="background-color:#FF0000; height: 75px">
                       VACANT NOT DONE
                    </div>
                    <div id="vacantNotDone">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="background-color:#996633; height: 75px">
                        DOWN UNITS
                    </div>
                    <div id="downUnits">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="background-color:#000000; color:#FF0000; height:75px">
                        BURN UNITS
                    </div>
                    <div id="burnUnits">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height:75px">
                        PROJECTED VACANCY
                    </div>
                    <div id="projectedVacancy">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height:75px">
                        VACANT & PRELEASED
                    </div>
                    <div id="vacantAndPreleased">
                        0
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="vacancyBlock">
                    <div style="height:75px">
                        OCCUPIED WITH NOTICE
                    </div>
                    <div id="occupiedWithNotice">
                        0
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footerScripts')
    <script>

        var vacancyReport = @json($vacancyReport);
        var property = @json($property);

        function ColorUnit(unitname, color)
        {
            
            $('#svgContainer [data-unit=\''+unitname+'\']').each((x,el)=>{
                
                el.style.fill= color;
            });

        }

        function GetSum($statuses)
        {
            var $sum=0;
            vacancyReport.forEach((unit)=>{
                if($statuses.includes(unit.statusId))
                {
                    $sum++;
                }
            });
            return $sum;
        }

        vacancyReport.forEach(element => {
            //vacant not done

            let unitname = element.unitname;

            if([2,6,8,10].includes(element.statusId))
            {
                ColorUnit(unitname, '#ff00007d');
            }
            //vacant done
            else if([1,5].includes(element.statusId))
            {
                ColorUnit(unitname, 'rgb(0 176 80 / 47%)');
            }
            // burn units
            else if(element.statusId == 9)
            {
                ColorUnit(unitname, 'rgb(11 11 11 / 47%)');
            }
            // down units
            else if(element.statusId == 7)
            {
                ColorUnit(unitname, 'rgb(153 102 51 / 48%)');
            }

        });

        var ids = [
                    'totalUnits',
                    'totalOccupied',
                    'totalVacantUnits',
                    'occupancyPercentage',
                    'vacantDone',
                    'vacantNotDone',
                    'downUnits',
                    'burnUnits',
                    'projectedVacancy',
                    'vacantAndPreleased',
                    'occupiedWithNotice'
                ];

        var propertyStatusCounts = {};
        // setting default values so no error will occur
        for(var index =0; index < 15; index++)
        {
            propertyStatusCounts[index] = 0;
        }

        // counting unitStatuses
        vacancyReport.forEach(el =>{
            if(propertyStatusCounts[el.statusId] == undefined)
            {
                propertyStatusCounts[el.statusId] = 0;
            }
            propertyStatusCounts[el.statusId]++;
        });

        var occupiedCount = 0;

        Object.keys(propertyStatusCounts).forEach((key)=>{
            if(key != 3 && key != 4)
            {
                occupiedCount += propertyStatusCounts[key];
            }

        })
        // setting unit statuses on view
        ids.forEach((id)=>{
            var value =0;
            switch(id){
                case 'totalUnits':
                    value = property['total_units'];
                break;
                case 'totalOccupied':
                    value =  property['total_units'] - occupiedCount;
                break;
                case 'totalVacantUnits':
                    value = occupiedCount;
                break;
                case 'occupancyPercentage':
                    value = (((property['total_units'] - occupiedCount)/property['total_units'])*100).toFixed(2) + '%';
                break;
                case 'vacantDone':
                    value = propertyStatusCounts[1] + propertyStatusCounts[5];
                break;
                case 'vacantNotDone':
                    value = propertyStatusCounts[2] + propertyStatusCounts[6] +  propertyStatusCounts[8] + propertyStatusCounts[10];
                break;
                case 'downUnits':
                    value = propertyStatusCounts[7];
                break;
                case 'burnUnits':
                    value = propertyStatusCounts[9];
                break;
                case 'projectedVacancy':
                    value = property['total_units'] - occupiedCount -  (propertyStatusCounts[2] + propertyStatusCounts[6] +  propertyStatusCounts[8] + propertyStatusCounts[10]) +  propertyStatusCounts[7];
                break;
                case 'vacantAndPreleased':
                    value = propertyStatusCounts[5];
                break;
                case 'occupiedWithNotice':
                    value = propertyStatusCounts[3];
                break;

            }
            document.querySelector('#'+id).innerHTML = value;
        });

        svgPanZoom('#svgContainer svg');
    </script>
@endsection