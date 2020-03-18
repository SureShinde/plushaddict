<?php
/**
 * @author     DAPL TEAM
 * @package    DAPL_OverRide
 */

namespace DAPL\OverRide\Block\Pdf\Column;

class Image extends \Fooman\PdfCore\Block\Pdf\Column\Image implements \Fooman\PdfCore\Block\Pdf\ColumnInterface
{
    
    /**
     * @param $row
     *
     * @return string
     */
    public function getImage($row)
    {
        $dim = $this->getImageDimensions('default');
        $imagePath = $this->getImagePath($row);
        if ($imagePath) {
            $params = [
                $imagePath,
                null,
                null,
                null,
                32,
                null,
                null,
                null,
                true,
                300,
                null,
                false,
                false,
                0,
                false,
                false,
                true
            ];
            return sprintf(
                '<tcpdf method="Image" %s /><span style="line-height:%s; max-width: 10px; display: table-cell;"></span>',
                $this->paramKeyHelper->getEncodedParams($params),
                $dim['spacer']
            );
        }

        return '';
    }

    private function getImageDimensions($size = 'default')
    {
        $sizes = [
            'large'      => ['image' => '20', 'spacer' => '20mm'],
            'xtra-large' => ['image' => '30', 'spacer' => '30mm'],
            'default'    => ['image' => '15', 'spacer' => '30mm'],
            'small'      => ['image' => '12', 'spacer' => '12mm']
        ];
        if (isset($sizes[$size])) {
            return $sizes[$size];
        }
        return $sizes['default'];
    }

}

