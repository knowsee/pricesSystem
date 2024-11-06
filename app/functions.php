<?php
/**
 * Here is your custom functions.
 */
if(function_exists('gmp_strval')) {
    function ip2long_int($ip){
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return sprintf('%u',ip2long($ip));
        } else if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip_n = inet_pton($ip);
            $bits = 15; // 16 x 8 bit = 128bit
            $ipv6long = '';
            while ($bits >= 0) {
                $bin = sprintf("%08b", (ord($ip_n[$bits])));
                $ipv6long = $bin . $ipv6long;
                $bits--;
            }
            return gmp_strval(gmp_init($ipv6long, 2), 10);
        }
    }
} else {
    function ip2long_int($ip){
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return sprintf('%u',ip2long($ip));
        } else if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip_n = inet_pton($ip);
            $bin = '';
            for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
                $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
            }
            if (function_exists('gmp_init')) {
                return gmp_strval(gmp_init($bin, 2), 10);
            } elseif (function_exists('bcadd')) {
                $dec = '0';
                for ($i = 0; $i < strlen($bin); $i++) {
                    $dec = bcmul($dec, '2', 0);
                    $dec = bcadd($dec, $bin[$i], 0);
                }
                return $dec;
            } else {
                trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
            }
        }
    }
}

function makeHashId(string $val, string $type = 'ip') {
    $uid = uniqid("", true);
    $hash = strtoupper(hash('ripemd128', $uid . $val . md5($type)));
    return substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12);
}