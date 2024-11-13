<?php
// Hàm resize_image để thay đổi kích thước ảnh
function resize_image($source, $dest, $w, $h) {
    // Lấy thông tin ảnh gốc
    $info = getimagesize($source);	
    list($width, $height) = $info; // Lấy chiều rộng và chiều cao của ảnh gốc
    
    // Kiểm tra loại ảnh và tạo nguồn từ file gốc
    if ($info['mime'] == 'image/jpeg') 
        $src = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $src = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $src = imagecreatefrompng($source);

    // Tạo ảnh mới với kích thước mong muốn
    $temp = imagecreatetruecolor($w, $h);
    // Thực hiện việc thay đổi kích thước và sao chép từ ảnh gốc
    imagecopyresampled($temp, $src, 0, 0, 0, 0, $w, $h, $width, $height);
    
    // Lưu ảnh đã thay đổi kích thước theo loại ảnh
    if ($info['mime'] == 'image/jpeg') 
        imagejpeg($temp, $dest);
    elseif ($info['mime'] == 'image/gif') 
        imagegif($temp, $dest);
    elseif ($info['mime'] == 'image/png') 
        imagepng($temp, $dest);
}
?>