<?php

declare(strict_types=1);

use OliverThiele\CspViolationLogger\Controller\LogController;
use OliverThiele\CspViolationLogger\Environment\Init;

?>
<!DOCTYPE html>
<html dir="ltr" lang="en-EN">
<head>
    <title>CSP Violation Logger</title>
    <meta charset="utf-8">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="Resources/Public/Css/Styles.css?v=2" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <h1>CSP Violation Logger</h1>

    <?php

    $loader = require __DIR__ . '/vendor/autoload.php';

    Init::loadSettings();

    $logController = new LogController();
    echo $logController->listAction();

    ?>
</div>

<script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="Resources/Public/Js/Script.js"></script>
</body>
</html>
