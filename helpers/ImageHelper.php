<?php

namespace pine\yii\helpers;

use Yii;

/**
 * Image helper class
 *
 * Contain helper methods for common image operations.
 *
 * @author Aleksandar Glisovic <aleksandar@pine.rs>
 * @version 1.0
 * @copyright Pine Media
 */
class ImageHelper
{
    /**
     * Resize
     *
     * @param string $imagePath
     * @param array $options
     * @return string
     */
    public static function resize($imagePath, $options=[])
    {
        // get prefered width and height
        $width = isset($options['width']) ? intval($options['width']) : 0;
        $height = isset($options['height']) ? intval($options['height']) : 0;
        $rounded = isset($options['rounded']) && $options['rounded'] == true ? true : false;
        $radius = isset($options['radius']) ? intval($options['radius']) : 0;

        // create new image name
        $imageExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if ($rounded && $radius>0) {
            $imageExtension =  'png';
        }
        $newImageName = md5($imagePath.$width.$height).'.'.$imageExtension;
        $newImageName2x = md5($imagePath.$width.$height).'@2x.'.$imageExtension; // retina ready

        // paths to assets folder
        $assetsPath = Yii::$app->basePath.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
        $assetsUrl = Yii::$app->request->hostInfo.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;

        // new image path
        $newImagePath = $assetsPath.$newImageName;
        $newImagePath2x = $assetsPath.$newImageName2x; // retina ready

        if (file_exists($newImagePath) && is_readable($newImagePath)) {
            // return resized image if exist
            return $assetsUrl.$newImageName;
        } else {
            // create resizet image
            if (file_exists($imagePath) && is_readable($imagePath) && is_file($imagePath)) {
                // copy to assets folder
                if (false!==copy($imagePath, $newImagePath)) {
                    copy($imagePath, $newImagePath2x); // retina ready
                    // resize new image
                    self::resizeImageToOptions($newImagePath, $width, $height, $options);
                    self::resizeImageToOptions($newImagePath2x, $width*2, $height*2, $options); // retina ready
                    // return path to resized image
                    if ($rounded && $radius>0) {
                        $newImageName = self::createRounded($newImagePath, $radius);
                        $newImageName2x = self::createRounded($newImagePath2x, $radius*2); // retina ready

                        $newImagePath = $assetsPath.$newImageName;
                        $newImagePath2x = $assetsPath.$newImageName2x; // retina ready
                    }
                    return $assetsUrl.$newImageName;
                }
            }
        }
        return '';
    }

    /**
     * Resize Image To Options
     *
     * @param string $imagePath
     * @param int $newWidth
     * @param int $newHeight
     * @param array $options
     * @return bool|void
     */
    private static function resizeImageToOptions($imagePath, $newWidth=0, $newHeight=0, $options=[])
    {
        if ($newWidth==0 && $newHeight==0) return; // nothing to resize
        if (!function_exists('imagecreatefrompng')) return; // GD not available
        if (!file_exists($imagePath) || !is_readable($imagePath)) return; // image not accessable

        $imageInfo = getimagesize($imagePath);

        switch ($imageInfo['mime']) {
            case 'image/png':
                $imageObject = imagecreatefrompng($imagePath);
                break;
            case 'image/jpeg':
                $imageObject = imagecreatefromjpeg($imagePath);
                break;
            case 'image/gif':
                $oldImageObject = imagecreatefromgif($imagePath);
                $imageObject  = imagecreatetruecolor($imageInfo[0],$imageInfo[1]);
                imagecopy($imageObject,$oldImageObject,0,0,0,0,$imageInfo[0],$imageInfo[1]);
                imagedestroy($oldImageObject);
                break;
            default:
                break;
        }

        $currentWidth = imagesx($imageObject);
        $currentHeight = imagesy($imageObject);

        if (!$imageObject) return;

        // calculate missing width/height
        if ($newHeight==0 and $newWidth>0) $newHeight = $currentHeight * $newWidth / $currentWidth;
        if ($newHeight>0 and $newWidth==0) $newWidth = $currentWidth * $newHeight / $currentHeight;

        $offsetX = 0;
        $offsetY = 0;

        // crop to square if new ratio is equal 1
        if ($newWidth==$newHeight) {
            if ($currentWidth > $currentHeight) {
                $offsetX = round(($currentWidth-$currentHeight)/2);
            }
            if ($currentWidth < $currentHeight) {
                $offsetY = round(($currentHeight-$currentWidth)/2);
            }
        }

        $newImageObject = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($newImageObject, false);

        // create resized image
        // imagecopyresized($newImageObject, $imageObject, 0, 0, $offsetX, $offsetY, $newWidth, $newHeight, ($currentWidth-$offsetX*2), ($currentHeight-$offsetY*2)); // This uses a fairly primitive algorithm that tends to yield more pixelated results.
        imagecopyresampled($newImageObject, $imageObject, 0, 0, $offsetX, $offsetY, $newWidth, $newHeight, ($currentWidth-$offsetX*2), ($currentHeight-$offsetY*2)); // This uses a smoothing and pixel interpolating algorithm that will generally yield much better results then imagecopyresized at the cost of a little cpu usage.

        $imageExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // save resized image
        switch($imageExtension) {
            case 'png':
                return imagepng($newImageObject, $imagePath);
                break;
            case 'jpeg': case 'jpg':
                return imagejpeg($newImageObject, $imagePath);
                break;
            case 'gif':
                return imagegif($newImageObject, $imagePath);
                break;
            default:
                break;
        }

        imagedestroy($newImageObject);
        imagedestroy($imageObject);
    }

    /**
     * Create Rounded
     *
     * @param string $imagePath
     * @param int $radius
     * @return mixed
     */
    private static function createRounded($imagePath, $radius)
    {
        $info = getimagesize($imagePath);
        $w = $info[0];
        $h = $info[1];
        $res = true;
        switch ($info['mime']) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($imagePath);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($imagePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($imagePath);
                break;
            default:
                $res = false;
        }

        if ($res) {
            $q = 8; # change this if you want
            $radius *= $q;

            # find unique color
            do {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
            } while (imagecolorexact($src, $r, $g, $b) < 0);

            $nw = $w*$q;
            $nh = $h*$q;

            $img = imagecreatetruecolor($nw, $nh);
            $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
            imagealphablending($img, false);
            imagesavealpha($img, true);
            imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

            imagefill($img, 0, 0, $alphacolor);
            imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            imagearc($img, $radius-1, $radius-1, $radius*2, $radius*2, 180, 270, $alphacolor);
            imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
            imagearc($img, $nw-$radius, $radius-1, $radius*2, $radius*2, 270, 0, $alphacolor);
            imagefilltoborder($img, $nw-1, 0, $alphacolor, $alphacolor);
            imagearc($img, $radius-1, $nh-$radius, $radius*2, $radius*2, 90, 180, $alphacolor);
            imagefilltoborder($img, 0, $nh-1, $alphacolor, $alphacolor);
            imagearc($img, $nw-$radius, $nh-$radius, $radius*2, $radius*2, 0, 90, $alphacolor);
            imagefilltoborder($img, $nw-1, $nh-1, $alphacolor, $alphacolor);
            imagealphablending($img, true);
            imagecolortransparent($img, $alphacolor);

            # resize image down
            $dest = imagecreatetruecolor($w, $h);
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            imagefilledrectangle($dest, 0, 0, $w, $h, $alphacolor);
            imagecopyresampled($dest, $img, 0, 0, 0, 0, $w, $h, $nw, $nh);

            # output image
            $res = $dest;
            imagedestroy($src);
            imagedestroy($img);
        }

        imagepng($res, $imagePath, 9);

        $filename = pathinfo($imagePath, PATHINFO_BASENAME);

        return $filename;
    }
}