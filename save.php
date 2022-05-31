<?php 

define('DS', DIRECTORY_SEPARATOR);

$data = $_POST['data'];
$dir = $_POST['dir'];
$newwidth = $_POST['width'];
$saveWebpAsJpeg = (boolean) $_POST['saveWebpAsJpeg'];

if(empty($data) || empty($dir)) {
    echo json_encode([
        'success' => false,
        'message' => 'Imagem não encontrada'
    ]);
    exit;
}

if(!is_dir($dir)) {
    echo json_encode([
        'success' => false,
        'message' => 'Diretório não encontrado'
    ]);
    exit;
}

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

switch ($ext) {
	default:
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagejpeg($dst, $dir . DS . $name);
		break;

	case 'jpeg':
        $name = $name . '.jpg';

		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagejpeg($dst, $dir . DS . $name);

        echo json_encode([
            'success' => true,
            'message' => 'Imagem salva com sucesso',
            'name' => $name
        ]);

		break;

	case 'png':
        $name = $name . '.png';

		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagepng($dst, $dir . DS . $name);

        echo json_encode([
            'success' => true,
            'message' => 'Imagem salva com sucesso',
            'name' => $name
        ]);
        
		break;

	case 'webp':
        $name = $name . '.webp';

        if($saveWebpAsJpeg){
            $name = str_replace('.webp', '.jpeg', $name);
        }

		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		imagewebp($dst, $dir . DS . $name);

        echo json_encode([
            'success' => true,
            'message' => 'Imagem salva com sucesso',
            'name' => $name
        ]);

		break;
}
