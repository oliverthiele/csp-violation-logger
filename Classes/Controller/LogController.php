<?php

declare(strict_types=1);

namespace OliverThiele\CspViolationLogger\Controller;

use OliverThiele\CspViolationLogger\Domain\Repository\LogRepository;

/**
 *  Log Controller
 */
class LogController
{
    /**
     * @var LogRepository
     */
    protected LogRepository $logRepository;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->logRepository = new LogRepository();
    }

    /**
     * Output the log entries as html table
     *
     * @return string
     */
    public function listAction(): string
    {
        $rows = $this->logRepository->findAll();
        $content = '<table class="table table-bordered table-responsive table-condensed table-striped">
<tr class=" bg-light sticky-top shadow-sm">
    <th>tstamp</th>
    <th>document_uri</th>
    <th>effective_directive</th>
    <th>violated_directive</th>
    <th>source_file</th>
    <th>line_number</th>
    <th>columns_number</th>
    <th>blocked_uri</th>
    <th>script_sample</th>
    <th>Browser</th>
    <th>http_user_agent</th>
</tr>';
        foreach ($rows as $row) {
            $browser = '';
            if (strpos($row['http_user_agent'], 'Safari/') !== false) {
                $browser = 'Safari';
            }
            if (strpos($row['http_user_agent'], 'Chrome/') !== false) {
                $browser = 'Chrome';
            }
            if (strpos($row['http_user_agent'], 'Firefox/') !== false) {
                $browser = 'Firefox';
            }
            if (strpos($row['http_user_agent'], 'Edg/') !== false) {
                $browser = 'Microsoft Edge';
            }
            if (strpos($row['http_user_agent'], 'OPR/') !== false) {
                $browser = 'Opera';
            }

            $content .= '<tr>';
            $content .= '<td class="text-nowrap">' . $row['tstamp'] . '</td>';

            $content .= '<td>';
            $content .= $row['document_uri'];
            if (trim($row['referrer']) !== '') {
                $content .= '<span class="ms-3 bg-dark text-white" title="Referrer: ' . $row['referrer'] . '">[R]</span>';
            }
            $content .= '</td>';

            $content .= '<td>' . $row['effective_directive'] . '</td>';
            $content .= '<td>' . $row['violated_directive'] . '</td>';
            $content .= '<td class="text-nowrap">' . $row['source_file'] . '</td>';
            $content .= '<td>' . $row['line_number'] . '</td>';
            $content .= '<td>' . $row['columns_number'] . '</td>';
            $content .= '<td>' . $row['blocked_uri'] . '</td>';
            $content .= '<td>' . $row['script_sample'] . '</td>';
            $content .= '<td>' . $browser . '</td>';
            $content .= '<td><span class="d-inline-block text-truncate w-300px"
data-bs-toggle="popover" data-bs-trigger="hover"
title="User Agent" data-bs-content="' . $row['http_user_agent'] . '">' . $row['http_user_agent'] . '</span></td>';
            $content .= '</tr>';
        }
        $content .= '</table>';
        return $content;
    }

    /**
     * Add a CSP violation to the database
     *
     * @param $cspReportArray
     * @return bool
     */
    public function addAction($cspReportArray): bool
    {
        return $this->logRepository->add($cspReportArray);
    }
}
