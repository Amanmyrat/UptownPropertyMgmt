<?php

namespace App\Library\Reports;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Classes\TimeFunctions;
use App\Library\Services\RentManagerApi;


class EmployeeWorkOrdersReport
{
    public $api;
    function __construct(RentManagerApi $api)
    {
        $this->api = $api;
    }

    function getReport($property_id, $startDate = null, $endDate = null)
    {
        $dates = TimeFunctions::getMonthStartEndDates();
        if ($startDate != null) {
            $dates[0] = $startDate;
            $dates[1] = $endDate;
        }
        $issues = $this->api->getFilteredIssueList($property_id, $dates[0], $dates[1]);

        $breakdownarrays = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $issues), true)['Grid1'];
        RedisHelper::set(RedisKeys::getFilteredIssuesList(), $breakdownarrays);
        $issueList = collect($breakdownarrays);

        $rows = array();
        foreach ($issueList as $issue) {
            $row = array();
            array_push($row, (array_key_exists('UsersName', $issue) ? $issue['UsersName'] : ''));
            array_push($row, (array_key_exists('ServiceManagerIssuesServiceManagerIssueID', $issue) ? $issue['ServiceManagerIssuesServiceManagerIssueID'] : ''));
            array_push($row, (array_key_exists('ServiceManagerIssuesTitle', $issue) ? $issue['ServiceManagerIssuesTitle'] : ''));
            array_push($row, (array_key_exists('ServiceManagerIssuesDescription', $issue) ? $issue['ServiceManagerIssuesDescription'] : ''));
            array_push($row, (array_key_exists('EntitiesName', $issue) ? $issue['EntitiesName'] : ''));
            array_push($row, (array_key_exists('SubEntitiesName', $issue) ? $issue['SubEntitiesName'] : ''));
            array_push($row, (array_key_exists('AccountsName', $issue) ? $issue['AccountsName'] : ''));
            array_push($row, (array_key_exists('ServiceManagerPrioritiesName', $issue) ? $issue['ServiceManagerPrioritiesName'] : ''));
            array_push($row, (array_key_exists('ServiceManagerIssuesAssignedOpenDate', $issue) ? $issue['ServiceManagerIssuesAssignedOpenDate'] : ''));
            array_push($row, (array_key_exists('ServiceManagerIssuesIsClosed', $issue) ? $issue['ServiceManagerIssuesIsClosed'] : ''));
            array_push($rows, $row);
        }
        $columns = ["Employee Name", "Issue ID", "Issue", "Issue Detail", "Property", "Unit #", "Tenant Name", "Priority", "Date", "IsIssueClosed"];
        return [$rows, $columns, $dates];
    }
}
