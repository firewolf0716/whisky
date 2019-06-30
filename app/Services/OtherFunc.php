<?php

namespace App\Services;


class OtherFunc
{
    /**
     * Get string between two strings.
     *
     * @param string $string, $start, $end
     *
     * @return string
     */
    public static function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = mb_strpos($string, $start);
        if ($ini == 0) return '';
        $ini += mb_strlen($start);
        $len = mb_strpos($string, $end, $ini) - $ini;
        return rtrim(ltrim(mb_substr($string, $ini, $len)));
    }

    /**
     * Check whether image exists on remote URL.
     *
     * @param string $webfile
     *
     * @return boolean
     */
    public static function is_webfile($webfile)
    {
        $fp = @fopen($webfile, "r");
        if ($fp !== false) fclose($fp);
        return($fp);
    }

    /**
     * Get float value from price string. 
     *
     * @param string $s
     *
     * @return float $s
     */
    public static function priceToFloat($s)
    {
        // convert "," to "."
        $s = str_replace(',', '.', $s);

        // remove everything except numbers and dot "."
        $s = preg_replace("/[^0-9\.]/", "", $s);

        // remove all seperators from first part and keep the end
        $s = str_replace('.', '',mb_substr($s, 0, -3)) . mb_substr($s, -3);

        // return float
        return (float) $s;
    }

    /**
     * Check If String Contains a Substring. 
     *
     * @param string $str, $sub
     *
     * @return bool
     */
    public static function checkIfContainStr($str, $sub)
    { 
        if (mb_strpos($str, $sub) !== false) return true;
        else return false;
    }

    /**
     * Get first monday from string(date)
     *
     * @param string $str
     *
     * @return date
     */
    public static function getFirstDateFromStr($str)
    { 
        $date = strtotime($str);
        if (!$date) return false;
        $date = date("Y-m", $date);
        $date_y_m = date("Y-m", strtotime("first monday ".$date));
        $date_j = date("j", strtotime("first monday ".$date));
        if ($date_j>7) $date_j = (int)$date_j % 7; 
        $date = $date_y_m . "-" . $date_j;
        return $date;
    }

}
