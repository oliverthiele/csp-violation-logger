<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use OliverThiele\CspViolationLogger\Controller\LogController;
use OliverThiele\CspViolationLogger\Environment\Init;

$loader = require __DIR__ . '/vendor/autoload.php';

Init::loadSettings();

$jsonData = file_get_contents('php://input');
try {
    $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
}
$cspReportArray = $data['csp-report'];

/**
 * Safari has no csp-report
 */
if (!is_array($data['csp-report'])) {
    $cspReportArray = $data;
}
$cspReportArray['httpUserAgent'] = $_SERVER['HTTP_USER_AGENT'];

try {
    $logController = new LogController();

    $return = $logController->addAction($cspReportArray);

    if ($return === false) {
        $fileLoggerWarning = new Monolog\Logger('fallback');
        $fileLoggerWarning->pushHandler(
            new Monolog\Handler\StreamHandler(
                'csp-violations.log',
                Monolog\Logger::WARNING
            )
        );
        $fileLoggerWarning->warning(json_encode($cspReportArray, JSON_THROW_ON_ERROR));
    } else {
        http_response_code(204); // HTTP 204 No Content
    }
} catch (\Throwable $e) {
    $fileLogger = new Monolog\Logger('Exception');
    $fileLogger->pushHandler(
        new Monolog\Handler\StreamHandler(
            'csp-violations.log',
            Monolog\Logger::ERROR
        )
    );
    $fileLogger->error('Exception abgefangen: ' . $e->getMessage());
}
