<?php

//$coent = file_get_contents("/www/gzt/com_sn_111.jpg");
$file = "/www/gzt/IMG_20190128_133924.jpg";

$img = file_get_contents($file);

var_dump(getimagesize($img));

echo strlen($img);


//image_png_size_add("/www/test.jpg","/www/com_sn_test.jpg");

/**

 * desription 压缩图片

 * @param sting $imgsrc 图片路径

 * @param string $imgdst 压缩后保存路径

 */

function image_png_size_add($imgsrc,$imgdst){

    list($width,$height,$type)=getimagesize($imgsrc);

    $new_width = ($width>600?600:$width)*0.9;

    $new_height =($height>600?600:$height)*0.9;

    switch($type){

        case 1:

            $giftype=check_gifcartoon($imgsrc);

            if($giftype){

                header('Content-Type:image/gif');

                $image_wp=imagecreatetruecolor($new_width, $new_height);

                $image = imagecreatefromgif($imgsrc);

                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                imagejpeg($image_wp, $imgdst,75);

                imagedestroy($image_wp);

            }

            break;

        case 2:

            header('Content-Type:image/jpeg');

            $image_wp=imagecreatetruecolor($new_width, $new_height);

            $image = imagecreatefromjpeg($imgsrc);

            imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagejpeg($image_wp, $imgdst,75);

            imagedestroy($image_wp);

            break;

        case 3:

            header('Content-Type:image/png');

            $image_wp=imagecreatetruecolor($new_width, $new_height);

            $image = imagecreatefrompng($imgsrc);

            imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagejpeg($image_wp, $imgdst,75);

            imagedestroy($image_wp);

            break;

    }

}

/**

 * desription 判断是否gif动画

 * @param sting $image_file图片路径

 * @return boolean t 是 f 否

 */

function check_gifcartoon($image_file){

    $fp = fopen($image_file,'rb');

    $image_head = fread($fp,1024);

    fclose($fp);

    return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true;

}

