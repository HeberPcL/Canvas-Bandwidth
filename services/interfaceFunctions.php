<?php
    function isLinux()
    {
        $uname = explode(' ', php_uname());
        return $uname[0] === OS_LINUX;
    }
    
    function getInterfaceReceivedBytes($interface)
    {
        if (isLinux()) {
            $filepath = '/sys/class/net/%s/statistics/rx_bytes';
            $output = file_get_contents(sprintf($filepath, $interface));

            return $output;
        }
        throw new Exception('Unable to guess OS');
    }
    
    function getInterfaceSentBytes($interface)
    {
        if (isLinux()) {
            $filepath = '/sys/class/net/%s/statistics/tx_bytes';
            $output = file_get_contents(sprintf($filepath, $interface));

            return $output;
        }
        throw new Exception('Unable to guess OS');
    }

    function getValidInterface($interface)
    {
        return (shell_exec('cat /sys/class/net/'.$interface.'/operstate') != NULL) ? true : false;
    }


    function getBandwidthTraffic($interface)
    {
        $interface = ($interface) ? $interface : 'eth0';
        if (!ctype_alnum($interface)) {
            throw new RuntimeException('Invalid interface name. Must contain only alphabetic and numeric characters.');
        }
        // Session & Define Values
        session_start();
        define('OS_LINUX', 'Linux');
        define('PATH_NETSTAT', '/usr/sbin/netstat');
        $traffic = array();

        // Traffic Get
        $rx[] = getInterfaceReceivedBytes($interface);
        $tx[] = getInterfaceSentBytes($interface);
        sleep(1);
        $rx[] = getInterfaceReceivedBytes($interface);
        $tx[] = getInterfaceSentBytes($interface);

        // Traffic Diff
        $rbps = $rx[1] - $rx[0];
        $tbps = $tx[1] - $tx[0];

        // Traffic Convert
        $round_rx = round($rbps/1024, 2);
        $round_tx = round($tbps/1024, 2);

        // Time
        $time = date("U")."000";

        // Array Values
        $_SESSION['rx'][] = array($time, $round_rx);
        $_SESSION['tx'][] = array($time, $round_tx);

        if (count($_SESSION['rx']) > 60)
        {
            $down = min(array_keys($_SESSION['rx']));
            $up   = min(array_keys($_SESSION['tx']));
            unset($_SESSION['rx'][$down]);
            unset($_SESSION['tx'][$up]);
        }
        $traffic[] = array_values($_SESSION['rx']);
        $traffic[] = array_values($_SESSION['tx']);

        return $traffic;
    }