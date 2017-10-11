<?php
namespace Libyaf\Helper;

class IdCard
{
    public static function mainLand($idcard)
    {
        $provinces = array('11', '12', '13', '14', '15', '21', '22', '23', '31', '32', '33', '34',
            '35', '36', '37', '41', '42', '43', '44', '45', '46', '50', '51', '52',
            '53', '54', '61', '62', '63', '64', '65', '71', '81', '82', '91');

        /***检查省份***/
        $idProvince = substr($idcard, 0, 2);
        if (!in_array($idProvince, $provinces)) {
            return false;
        }

        if (strlen($idcard) == 18) {
            $keys  = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $tails = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

            /***检查生日***/
            $year  = (int)substr($idcard, 6, 4);
            $month = (int)substr($idcard, 10, 2);
            $day   = (int)substr($idcard, 12, 2);
            list($y, $m, $d) = explode(":", date("Y:m:d"));
            if (((int)$y - $year) < 3 || ((int)$y - $year) > 120 || $month < 0 || $month > 12 || $day < 0 || $day > 31) {
                return false;
            }

            /***校验尾数***/
            $checkSum = 0;
            for ($i = 0;$i < 17; $i++) {
                $checkSum += (int)substr($idcard, $i, 1) * $keys[$i];
            }

            $check = $checkSum % 11;
            if ($check < 0 || $check > 10) {
                return false;
            }

            $tail = $tails[$check];
            if ($tail+"" != substr($idcard, -1)) {
                return false;
            }

            return true;
        } elseif(strlen($idcard) == 15) {
            /***检查生日***/
            // $year  = (int)substr($idcard, 6, 2);
            // $month = (int)substr($idcard, 8, 2);
            // $day   = (int)substr($idcard, 10, 2);
            // list($y, $m, $d) = explode(":", date("Y:m:d"));
            // if($year < (int)($y%100)){
            //     $year += 2000;
            // }else{
            //     $year += 1900;
            // }
            // if(((int)$y - $year) < 3 || ((int)$y - $year) > 120 || $month < 0 || $month > 12 || $day < 0 || $day > 31){
            //     return false;
            // }
            return true;
        }
        return false;
    }

    public static function hongKong($idcard)
    {
    }

    public static function macau($idcard)
    {
    }

    /***Y＝X1＋9×X2＋8×D2＋7×D3＋6×D4＋5×D5＋4×D6＋3×D7＋2×D8＋1×D9***/
    public static function taiwan($idcard)
    {
        $str = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
        $firstChar = substr($idcard, 0, 1);
        if (ord($firstChar) < ord('A') || ord($firstChar) > ord('Z')) {
            return false;
        }

        $pos = strpos($str, $firstChar);
        if ($pos == false) {
            return false;
        }

        $tt = $pos + 10;
        $x1 = (int)($tt / 10);
        $x2 = $tt % 10;
        $Y  = $x1 + 9 * $x2;
        for ($i = 2; $i<=9; $i++) {
            $Y += (10 - $i) * (int)substr($idcard, $i - 1, 1);
        }

        $tail = 10 - $Y % 10;
        if ($tail != substr($idcard, -1)) {
            return false;
        }

        return true;
    }

    public static function isIdCard($identity)
    {
        $identity = strtoupper($identity);
        if (strlen($identity) == 15) {
            if(!(bool) preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/", (string) $identity)){
                return false;
            }
        } elseif (strlen($identity) == 18) {
            if (!(bool) preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|X)$/", (string) $identity)){
                return false;
            }
        } else {
            return false;
        }

        return self::mainLand($identity);
    }
}
