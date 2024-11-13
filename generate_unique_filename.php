<?php
// Hàm kiểm tra và tạo tên tệp không trùng
function generate_unique_filename($directory, $prefix, $extension) {
    $index = 1;
    $date_str = date('d_m_Y');
    
    do {
        $new_filename = $prefix . "_" . $date_str . "_" . $index;
        // Sử dụng glob để tìm các file có tên tương tự mà không cần quan tâm đuôi file
        $existing_files = glob($directory . $new_filename . ".*");
        $index++;
    } while (!empty($existing_files));

    // Trả về tên file duy nhất (không bao gồm phần mở rộng)
    return $new_filename .".". $extension;
}
?>