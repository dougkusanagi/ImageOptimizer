<?php 

define('DS', DIRECTORY_SEPARATOR);

$data = $_POST['data'];
$dir = $_POST['dir'];
$newwidth = $_POST['width'];

$ext = $_POST['ext'];
$ext = str_replace('image/', '', $ext);

$name = $_POST['name'];
$name = utf8_decode($name);
$name = pathinfo($name, PATHINFO_FILENAME);

$dir = str_replace('\\', DS, $dir);

list(, $data) = explode(',', $data);
$data = base64_decode($data);

if(!is_dir($dir)){
	mkdir($dir);
}

$image = imagecreatefromstring($data);
list($width, $height) = getimagesizefromstring($data);
$dst = imagecreatetruecolor($newwidth, $newwidth);

var_dump($ext);

// return;

switch ($ext) {
	default:
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagejpeg($dst, $dir . DS . $name);
		break;

	case 'jpeg':
        $name = $name . '.jpg';
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagejpeg($dst, $dir . DS . $name);
		break;

	case 'png':
		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagepng($dst, $dir . DS . $name);
		break;

	case 'webp':
        // $name = $name . '.jpeg';
		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagewebp($dst, $dir . DS . $name);
		break;
}
