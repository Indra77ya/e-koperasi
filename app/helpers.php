<?php

if ( ! function_exists('format_rupiah'))
{
    /**
     * Convert to Format Rupiah
     *
     * @param    integer    required
     * @return    string
     */
    function format_rupiah($data)
    {
        return 'Rp ' . number_format($data, 0, ',', '.');
    }
}

if ( ! function_exists('abbreviate_number'))
{
    /**
     * Abbreviate number to K, Jt, M, T
     *
     * @param    integer    $number
     * @return    string
     */
    function abbreviate_number($number)
    {
        if ($number < 1000000) {
            return number_format($number, 0, ',', '.');
        } elseif ($number < 1000000000) {
            return rtrim(rtrim(number_format($number / 1000000, 1, ',', '.'), '0'), ',') . ' Jt';
        } elseif ($number < 1000000000000) {
            return rtrim(rtrim(number_format($number / 1000000000, 2, ',', '.'), '0'), ',') . ' M';
        } else {
            return rtrim(rtrim(number_format($number / 1000000000000, 2, ',', '.'), '0'), ',') . ' T';
        }
    }
}

if ( ! function_exists('short_rupiah'))
{
    /**
     * Convert to Short Format Rupiah
     *
     * @param    integer    $number
     * @return    string
     */
    function short_rupiah($number)
    {
        return 'Rp ' . abbreviate_number($number);
    }
}

if ( ! function_exists('religions'))
{
    /**
     * List Religion
     *
     * @return    array
     */
    function religions()
    {
        return array('Islam', 'Kristen', 'Hindu', 'Budha', 'Katolik');
    }
}

if ( ! function_exists('month_id'))
{
    function month_id($str)
    {
        $month = '';
        switch($str){
            case '01':
                $month = 'Januari';
                break;
            case '02':
                $month = 'Februari';
                break;
            case '03':
                $month = 'Maret';
                break;
            case '04':
                $month = 'April';
                break;
            case '05':
                $month = 'Mei';
                break;
            case '06':
                $month = 'Juni';
                break;
            case '07':
                $month = 'Juli';
                break;
            case '08':
                $month = 'Agustus';
                break;
            case '09':
                $month = 'September';
                break;
            case '10':
                $month = 'Oktober';
                break;
            case '11':
                $month = 'November';
                break;
            case '12':
                $month = 'Desember';
                break;
            default:
                $month = '-';
                break;
        }
        return $month;
    }
}

if ( ! function_exists('lang_url')) {
    function lang_url()
    {
        $locale = session('locale');

        return $locale == 'en'
            ? 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
            : 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json';
    }
}
