<?php
namespace app\helpers;

class Ipv4
{

    /**
     * Gets IP range information for a given address and prefix length
     * 获取地址段的相关信息
     *
     * @param string $address the IPv6 Address
     * @param integer $prefix_len The prefix length
     * @return array an array of information about the IP address range
     */
    public function getAddressRange(string $address, int $prefix_len): array
    {
        $base = ip2long('255.255.255.255');
        $ip = ip2long($address);
        $mask = pow(2,32-intval($prefix_len))-1;//mask=0.0.0.255(int)
        $smask = $mask ^ $base;
        $min = $ip & $smask;
        $max = $ip | $mask;
        return [
            'start_address'       => long2ip($min),
            'end_address'         => long2ip($max),
        ];
    }
}
