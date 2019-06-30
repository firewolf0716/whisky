<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Bottler;
use App\Models\Distillery; 
use App\Models\Sery; 
use App\Services\OtherFunc;

class StringService
{
    public static function get_from_title($title)
    {
        $result = array();
        $result['country'] = '';
        // matching data
        $bottlers = Bottler::getSlugs();
        $brands = Brand::getSlugs();
        $distilleries = Distillery::getSlugs();
        $series = Sery::all();

        $bottler = static::checkIfExistInTitle($bottlers, $title, 'name');
        if ($bottler) 
        {
            $result['bottler'] = $bottler['name'];
            $result['country'] = $bottler['country'];
            $title = static::getRestPart($title, $bottler['name']);
        }

        $brand = static::checkIfExistInTitle($brands, $title, 'brand');
        if ($brand) 
        {
            $result['distillery_brand'] = $brand['brand'];
            $result['country'] = $brand['country'];
            $title = static::getRestPart($title, $brand['brand']);
        }

        $distillery = static::checkIfExistInTitle($distilleries, $title, 'name');
        if ($distillery) 
        {
            $result['distillery_brand'] = $distillery['name'];
            $result['country'] = $distillery['country'];
            $title = static::getRestPart($title, $distillery['name']);   
        }

        $sery = static::checkIfExistInTitle($series, $title, 'name');
        if ($sery) 
        {
            $result['series'] = $sery['name'];
            $title = static::getRestPart($title, $sery);   
        }

        $result['name'] = ltrim($title);

        return $result;
    }

    public static function checkIfExistInTitle($array, $title, $key)
    {   
        foreach ($array as $value) :
            if (OtherFunc::checkIfContainStr($title, $value[$key])) 
                return $value;
        endforeach;
        return false;
    }

    public static function getRestPart($string, $sub)
    {   
        $string = ' '.$string.' ';
        $pos = mb_strpos($string, $sub);
        $front = mb_substr($string, 0, $pos -1);
        $back = mb_substr( $string, $pos + mb_strlen($sub) - mb_strlen($string) );
        return rtrim(ltrim($front . $back));        
    }

    public static function getRestBackPart($string, $sub)
    {   
        $string = ' '.$string.' ';
        $pos = mb_strpos($string, $sub);
        $back = mb_substr( $string, $pos + mb_strlen($sub) - mb_strlen($string) );
        return rtrim(ltrim($back));        
    }

    public static function getRestFrontPart($string, $sub)
    {   
        $string = ' '.$string.' ';
        $pos = mb_strpos($string, $sub);
        $front = mb_substr($string, 0, $pos);
        return rtrim(ltrim($front));        
    }


    public static function get_from_description($units)
    {
        var_dump($units);
        $result = array();
        foreach ($units as $unit) {
            if (OtherFunc::checkIfContainStr($unit, 'Cask Type:')) {
                $result['cask_type'] = static::getRestBackPart($unit, 'Cask Type:');
                continue;
            }elseif (OtherFunc::checkIfContainStr($unit, 'Cask Number:')) {
                $result['cask_num'] = static::getRestBackPart($unit, 'Cask Number:');
                continue;
            }
        }
        return $result;
    }

}

