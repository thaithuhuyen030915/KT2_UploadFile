<?php
// Hàm compressImage để nén ảnh
function compressImage($source, $dest, $quality) {
    // Lấy thông tin ảnh gốc
    $info = getimagesize($source);

    // Tạo nguồn ảnh gốc từ file đã upload dựa vào loại ảnh
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);

    // Nén ảnh và lưu lại
    if ($info['mime'] == 'image/jpeg') 
        imagejpeg($image, $dest, $quality);
    elseif ($info['mime'] == 'image/gif') 
        imagegif($image, $dest);
    elseif ($info['mime'] == 'image/png') 
        imagepng($image, $dest);

    /* Mức độ nén (quality):
        - 0: Không nén
        - 1: Tốc độ nhanh nhất
        - 9: Mức độ nén cao nhất
        - -1: Mặc định
    */
}
?>