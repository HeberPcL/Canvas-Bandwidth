<?php
    include('interfaceFunctions.php');
    $start      = array_key_exists('start', $_GET) ? $_GET['start'] : null;
    $interface  = array_key_exists('interface', $_GET) ? $_GET['interface'] : 'eth0';

    if (!ctype_alnum($interface)) {
        echo('Invalid interface name. Must contain only alphabetic and numeric characters.'); exit;
    }
    if (!getValidInterface($interface)) {
        echo('Interface not valid!'); exit;
    }

    $dataPoints = getDataPoints($start);
    header('Content-Type: application/json');
    echo json_encode(
        array(
            'label' => $interface,
            'data' => $dataPoints
        )
    );

