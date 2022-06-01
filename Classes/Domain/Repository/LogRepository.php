<?php

declare(strict_types=1);

namespace OliverThiele\CspViolationLogger\Domain\Repository;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use mysqli_result;

/**
 * Log Repository
 */
class LogRepository
{

    protected \mysqli $mysqli;

    /**
     * Constructor
     */
    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $connection = $this->mysqli = new \mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            $_ENV['DB_DB']
        );

        if ($connection->connect_error) {
            die('DB Connection Error');
        }
    }

    /**
     * Close the database connection
     */
    public function __destruct()
    {
        $this->mysqli->close();
    }

    /**
     * Get the latest 500 entries from the database
     *
     * @return mysqli_result|bool|array
     */
    public function findAll()
    {
        $rows = [];
        $query = 'select * from log order by tstamp DESC limit 0,500';
        $result = $this->mysqli->query($query);
        while (($row = mysqli_fetch_assoc($result))) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * todo Add function to the view
     * @return mysqli_result|bool|array
     */
    public function findGroupedRows()
    {
        $rows = [];
        $query = '
select document_uri,
       line_number,
       http_user_agent
from log
group by document_uri, line_number, http_user_agent
order by document_uri, line_number;';
        $result = $this->mysqli->query($query);
        while (($row = mysqli_fetch_assoc($result))) {
            $rows[] = $row;
        }
        return $rows;
    }


    /**
     * Adds an entry with CSP violations to the database
     *
     * @param $cspReportArray
     * @return bool
     */
    public function add($cspReportArray): bool
    {
        $columns = '`document_uri`,`referrer`,`violated_directive`,`effective_directive`,`original_policy`,' .
            '`disposition`,`blocked_uri`,`line_number`,`columns_number`,`source_file`,`status_code`,`script_sample`,`http_user_agent`';

        $documentUri = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['document-uri']) . '\'';

        if (isset($cspReportArray['referrer'])) {
            $referrer = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['referrer']) . '\'';
        } else {
            $referrer = '\'\'';
        }

        if (isset($cspReportArray['violated-directive'])) {
            $violatedDirective = '\'' . $this->mysqli->real_escape_string(
                    (string)$cspReportArray['violated-directive']
                ) . '\'';
        } else {
            $violatedDirective = '\'\'';
        }

        if (isset($cspReportArray['effective-directive'])) {
            $effectiveDirective = '\'' . $this->mysqli->real_escape_string(
                    (string)$cspReportArray['effective-directive']
                ) . '\'';
        } else {
            $effectiveDirective = '\'\'';
        }

        $originalPolicy = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['original-policy']) . '\'';

        if (isset($cspReportArray['disposition'])) {
            $disposition = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['disposition']) . '\'';
        } else {
            $disposition = '\'\'';
        }
        $blockedUri = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['blocked-uri']) . '\'';

        $lineNumber = 0;
        if (isset($cspReportArray['line-number'])) {
            $lineNumber = (int)$cspReportArray['line-number'];
        }

        $columnsNumber = 0;
        if (isset($cspReportArray['column-number'])) {
            $columnsNumber = (int)$cspReportArray['column-number']; // Safari
        }
        if (isset($cspReportArray['source-file'])) {
            $sourceFile = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['source-file']) . '\'';
        } else {
            $sourceFile = '\'\'';
        }

        $statusCode = 0;
        if (isset($cspReportArray['status-code'])) {
            $statusCode = (int)$cspReportArray['status-code'];
        }

        if (isset($cspReportArray['script-sample'])) {
            $scriptSample = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['script-sample']) . '\'';
        } else {
            $scriptSample = '\'\'';
        }

        $httpUserAgent = '\'' . $this->mysqli->real_escape_string((string)$cspReportArray['httpUserAgent']) . '\'';

        $values = $documentUri . ', ' .
            $referrer . ', ' .
            $violatedDirective . ', ' .
            $effectiveDirective . ', ' .
            $originalPolicy . ', ' .
            $disposition . ', ' .
            $blockedUri . ', ' .
            $lineNumber . ', ' .
            $columnsNumber . ', ' .
            $sourceFile . ', ' .
            $statusCode . ', ' .
            $scriptSample . ', ' .
            $httpUserAgent;
        $query = 'INSERT INTO log (' . $columns . ') VALUES (' . $values . ');';

        $this->mysqli->query($query);

        if ($this->mysqli->errno) {
            $fileLoggerQuery = new Logger('query');
            $fileLoggerQuery->pushHandler(
                new StreamHandler(
                    'query.log',
                    Logger::WARNING
                )
            );
            $fileLoggerQuery->warning($query);
            return false;
        }

        return true;
    }
}
