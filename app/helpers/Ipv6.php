<?php
namespace app\helpers;
/**
 * The goal of this class is to give basic information about a given
 * IPv6 subnet.
 *
 * It will provide the following information
 * - Abbreviated form of IPv6 Address
 * - Non-abbreviated form of IPv6 address
 * - Start and end address for a given subnet
 * - The number of interfaces in this subnet
 * - The subnet mask
 *
 *
 * @author Ben Burkhart <benburkhart1@gmail.com>
 */
class Ipv6
{
	/**
	 * Determines if a given IPv6 address is a valid address.
	 *
	 * @param string $address An IPv6 address.
	 * @return boolean true if IPv6 address is valid.
	 */
	public function testValidAddress($address)
	{
		// 8 groups of 4 hexidecimal characters
		return (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE);
	}

	/**
	 * This unabbreviates an abbreviated address
	 *
	 * @param string $address an IPv6 Address
	 * @return string an unabbreviated IPv6 address
     * 将输入的IPV6地址转换为标准IPV6地址
	 */
	public function unabbreviateAddress($address , $is_debug = false)
	{

		$ret = array();

		$unabbv = $address;

		if (strpos($unabbv, "::") !== FALSE)
		{
			$parts = explode(":", $unabbv);

			$ret[] = $parts;

			$cnt = 0;

			// Count number of parts with a number in it
			if(sizeof($parts)){
				foreach ($parts as $part)
				{
					if ( strlen($part) > 0 ){
						$cnt++;
					}
				}
			}

			// This is how many 0000 blocks is needed
			$needed = 8 - $cnt;
			$ret['needed'] = $needed;

			if($needed >= 1){
				//$unabbv = str_replace("::", ":0000:", $unabbv);
                if(substr($unabbv , -2 , 2) == "::"){
                    $unabbv = str_replace("::", ":".str_repeat("0000:", $needed -1 )."0000", $unabbv);
                }else{
                    $unabbv = str_replace("::", ":".str_repeat("0000:", $needed ), $unabbv);
                }
			}

			$ret['unabbv1'] = $unabbv;
		}

		$parts = explode(":", $unabbv);

		$ret[] = $parts;

		$new   = "";

		// Make sure all parts are fully 4 hex chars
		for ($i = 0; $i < count($parts); $i++)
		{
			$new .= sprintf("%04s:", $parts[$i]);
		}

		// Remove trailing :
		$unabbv = substr($new, 0, -1);

        $ret['unabbv2'] = $unabbv;

		//打印测试内容
		if($is_debug == true){
			print_r($ret);
		}

		return $unabbv;
	}

	/**
	 * Abbreviates an IPv6 address into shorthand form.
	 * Please note, this function is not as elegant as I would have
	 * liked, I had some issues with my regular expression, and I did
	 * the string parsing manually, additionally, I do not abbreviate
	 * the best way as for instance with
	 * '2001:0db8:0000:ff00:0000:0000:0000:0000' Doing the last 4 sets
	 * of '0000' with ':' would be more efficient than the first, but
	 * I didn't feel like this was the focus of your excercise.
     *
     * 将这样的地址 2409:876c:220:0:0:0:0:0 转换为 2409:876c:0220:0000:0000:0000:0000:0000
	 *
	 * @param string $address an IPv6 Address
	 * @return string an abbreviated IPv6 address
	 */
	public function abbreviateAddress($address)
	{
		$abbv = $address;

		// Check if we're already abbreviated
		if (strpos($abbv, "::") === FALSE){
			// Split it up into logical groups
			$parts  = explode(":", $abbv);
			$nparts = array();

			$ignore = false;
			$done   = false;

			for ($i=0;$i<count($parts);$i++){
				if (intval(hexdec($parts[$i])) === 0 && $ignore == false && $done == false){
					$ignore   = true;
					$nparts[] = '';

					// This is because a 2 part array with '' and '0001' would have resulted in :0001 rather
					// than ::0001
					if ($i == 0)
						$nparts[] = '';
				}
				else if (intval(hexdec($parts[$i])) === 0 && $ignore == true && $done == false){
					continue;
				}
				else if (intval(hexdec($parts[$i])) !== 0 && $ignore == true){
					$done   = true;
					$ignore = false;

					$nparts[] = $parts[$i];
				}
				else{
					$nparts[] = $parts[$i];
				}

			}
			$abbv = implode(":", $nparts);
		}

		// Remove one or more leading zeroes
		$abbv = preg_replace("/:0{1,3}/", ":", $abbv);

		return $abbv;
	}

	/**
	 * Gets the interface count for a given prefix length
	 *
	 * @param integer $prefix_len The prefix length
	 * @return string a formatted number of IPs in that prefix length
	 */
	public function getInterfaceCount($prefix_len)
	{
		$actual = pow(2, (128-$prefix_len));

		return number_format($actual);
	}

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
		// Unabbreviate it just in case this is called adhoc
		$unabbv = $this->unabbreviateAddress($address , false);
		$parts  = explode(":", $unabbv);

		// This is the start bit mask
		$bstring = str_repeat("1", $prefix_len) . str_repeat("0", 128-$prefix_len);
		// This is the end bit mask
		$estring = str_repeat("0", $prefix_len) . str_repeat("1", 128-$prefix_len);

		// I'm not sure I like doing this, but I am doing this out of abundance of
		// caution with PHP's data types
		$mins    = str_split($bstring, 16);
		$maxs    = str_split($estring, 16);

		$mb    = "";
		$start = "";
		$end   = "";

        $unabbv_string = "";

		for ($i = 0; $i < 8; $i++)
		{
			$min    = base_convert($mins[$i], 2, 16);
			$max    = base_convert($maxs[$i], 2, 16);

			$mb    .= sprintf("%04s", $min) . ':';

			$start .= dechex(hexdec($parts[$i]) & hexdec($min)) . ':';
			$end   .= dechex(hexdec($parts[$i]) | hexdec($max)) . ':';

            //$unabbv_bstring .=  $parts[$i].",".sprintf("%016b", hexdec($parts[$i]) )."\n";
            $unabbv_string .=  sprintf("%016b", hexdec($parts[$i]) );
		}

		$prefix_address = substr($mb, 0, -1);

		$start = substr($start, 0, -1);
		$start_s = $this->unabbreviateAddress($start);

		$end = substr($end, 0, -1);
        $end_s = $this->unabbreviateAddress($end);

		$ret = array(
            'unabbv' 		        => $unabbv,
            'ustring'               => $unabbv_string,
            //'lenOfunabbv_string'  => strlen($unabbv_string),
            //'parts' 		        => $parts,

            'bstring' 		        => $bstring,
            //'lenOfbstring' 		=> strlen($bstring),

            'estring' 		        => $estring,
            //'lenOfestring' 		=> strlen($bstring),
            //'mins' 		 	    => $mins,
            //'maxs' 		 	    => $maxs,
            'prefix_address'        => $prefix_address,
            //'start_address'         => $start,
            'start_address'       => $start_s,
            //'end_address'           => $end,
            'end_address'         => $end_s,
            'NumOf64network'        => pow(2, 64 - $prefix_len ),
        );

		return $ret;
	}

	//将范围大的地址拆分为小段地址
	public function splitAddress($address , $prefix_len_from , $prefix_len_to , $is_debug = false){
        $ret = array();
        $subNetwork = array();

        $unabbv = $this->unabbreviateAddress($address , false);
        $parts  = explode(":", $unabbv);

        if( $prefix_len_from > $prefix_len_to ){
            return false;
        }else{
            $subNetwork_len = $prefix_len_to - $prefix_len_from;
            $numOfSubNetwork = pow(2 , $subNetwork_len);
            $numOfSub64Network = pow(2 , 64  - $prefix_len_from);
            $numOfSub60Network = pow(2 , 60  - $prefix_len_from);
            $numOfSub56Network = pow(2 , 56  - $prefix_len_from);
            $numOfSub52Network = pow(2 , 52  - $prefix_len_from);
            $numOfSub48Network = pow(2 , 48  - $prefix_len_from);
            $numOfSub44Network = pow(2 , 44  - $prefix_len_from);
        }

        $ret['numOfSubNetwork'] = $numOfSubNetwork;
        $ret['numOfSub64Network'] = $numOfSub64Network;
        $ret['numOfSub60Network'] = $numOfSub60Network;
        $ret['numOfSub56Network'] = $numOfSub56Network;
        $ret['numOfSub52Network'] = $numOfSub52Network;
        $ret['numOfSub48Network'] = $numOfSub48Network;
        $ret['numOfSub44Network'] = $numOfSub44Network;

        // This is the start bit mask
        $src_string = str_repeat("1", $prefix_len_from) . str_repeat("0", 128-$prefix_len_from);
        // This is the end bit mask
        $tar_string = str_repeat("1", $prefix_len_to) . str_repeat("0", 128-$prefix_len_to);

        $ret['src_string']      = $src_string;
        $ret['tar_string']      = $tar_string;

        $src_string_ts           = str_split($src_string, 16);
        $tar_string_ts           = str_split($tar_string, 16);

        //$ret['src_string_ts']    = $src_string_ts;
        //$ret['tar_string_ts']    = $tar_string_ts;

        $addressInfo = $this->getAddressRange($address, $prefix_len_from);
        $output_prefix = substr( $addressInfo['ustring'] , 0 , $prefix_len_from);
        //$ret['addressInfo'] = $addressInfo;

        $subNetwork['debug'] = $ret;

        for($i = 0 ; $i< $numOfSubNetwork ; $i++){
            $tstring = $output_prefix.sprintf("%0{$subNetwork_len}b", $i ).str_repeat("0", 128-$prefix_len_to);
            $tstring_temp = str_split($tstring, 16);
            //$subNetwork[$i]["tstring"] = $tstring;
            //$subNetwork[$i]["tstring_a"] = $tstring_temp;

            $mb_temp = "";
            $mb_temps = "";
            for ($j = 0; $j < 8; $j++) {
                $mb_temp    .= sprintf("%04s", base_convert($tstring_temp[$j], 2, 16)) . ':';

                if( $j*16 < $prefix_len_to){
                    $mb_temps    .= base_convert($tstring_temp[$j], 2, 16) . ':';
                }
            }

            if($prefix_len_to <= 112 ){
                $mb_temps    .= ":";
            }

            $mb_temps = str_replace(":0:",":",$mb_temps);

            //不需要就暂时关闭   子网中带着掩码
            //$subNetwork['subnet'][$i]["strl"] = substr($mb_temp, 0, -1);
            $subNetwork['subnet'][$i]["strl"] = substr($mb_temp, 0, -1)."/{$prefix_len_to}";

            //不需要就暂时关闭   子网中带着掩码
            //$subNetwork['subnet'][$i]["strs"] = $mb_temps;
            $subNetwork['subnet'][$i]["strs"] = $mb_temps."/{$prefix_len_to}";
        }

        //打印测试内容
        if($is_debug == true){
            //print_r($ret);
            //print_r($subNetwork);
        }
        return $subNetwork;
    }
}
