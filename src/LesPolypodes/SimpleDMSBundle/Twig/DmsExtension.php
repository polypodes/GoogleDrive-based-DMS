<?php

namespace LesPolypodes\SimpleDMSBundle\Twig;

/**
 * Class DmsExtension
 * @package LesPolypodes\SimpleDMSBundle\Twig
 */
class DmsExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('format_bytes', array($this, 'format_bytes')),
        );
    }

    /**
     * Filter for converting bytes to a human-readable format, as Unix command "ls -h" does.
     *
     * @param string|int     $bytes A string or integer number value to format.
     * @param bool    $base2conversion Defines if the conversion has to be strictly performed as binary values or
     *      by using a decimal conversion such as 1 KByte = 1000 Bytes.
     *
     * @link https://github.com/twigphp/Twig-extensions/pull/116/files
     *
     * @return string The number converted to human readable representation.
     * @todo: Use Intl-based translations to deal with "11.4" conversion to "11,4" value
     */
    public function format_bytes($bytes, $base2conversion = true)
    {
        $unit = $base2conversion ? 1000 : 1024;
        if ($bytes <= $unit) return $bytes . " B";
        $exp = intval((log($bytes) / log($unit)));
        $pre = ($base2conversion ? "kMGTPE" : "KMGTPE");
        $pre = $pre[$exp - 1] . ($base2conversion ? "" : "i");

        return sprintf("%.1f %sB", $bytes / pow($unit, $exp), $pre);
    }

    /**
     * @param $bytes
     *
     * @return string
     */
    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dms_extension';
    }
}