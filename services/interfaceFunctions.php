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