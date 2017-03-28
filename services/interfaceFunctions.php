<?php
    function isLinux()
    {
        $uname = explode(' ', php_uname());
        return $uname[0] === OS_LINUX;
    }
    