<?php
// Khởi tạo biến chứa thông báo và trạng thái hiển thị thông báo
$msg = "";
$sta = "";

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

// Xử lý khi nhấn nút tải lên
if (isset($_POST['upload'])) {
    // Thư mục lưu trữ ảnh gốc, ảnh đã thay đổi kích thước và ảnh nén
    $upload_dir1 = "uploads/raw/";
    $upload_dir2 = "uploads/resized/";
    $upload_dir3 = "uploads/compressed/";

    // Kiểm tra nếu không có file được chọn
    if ($_FILES['file_upload1']['tmp_name'] == "") {
        $f_inst1 = "placeholder.png"; // Ảnh mặc định
        $msg = "<strong>Rất tiếc!</strong> Không có file để tải lên.";
        $sta = "warning"; // Trạng thái cảnh báo
    } else {
        // Kiểm tra dung lượng file (giới hạn tối đa 2MB)
        if ($_FILES['file_upload1']['size'] > 2 * 1024 * 1024) {
            $msg = "<strong>Rất tiếc!</strong> File quá lớn. Vui lòng tải lên file nhỏ hơn 2MB.";
            $sta = "danger"; // Trạng thái nguy hiểm
        } else {
            // Kiểm tra loại file hợp lệ
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['file_upload1']['type'];

            if (!in_array($file_type, $allowed_types)) {
                $msg = "<strong>Rất tiếc!</strong> Loại file không hợp lệ. Chỉ cho phép các định dạng JPG, PNG, và GIF.";
                $sta = "danger"; // Trạng thái nguy hiểm
            } else {
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

                // Xử lý upload file nếu hợp lệ
                $tmp_file1 = $_FILES['file_upload1']['tmp_name'];
                $file_typ1 = pathinfo($_FILES['file_upload1']['name'], PATHINFO_EXTENSION);
                $f_inst1 = generate_unique_filename($upload_dir1, "Hình_ảnh", $file_typ1); // Tạo tên ảnh không trùng
                $rename_file1 = $upload_dir1 . $f_inst1;

                // Di chuyển file đã upload vào thư mục lưu trữ
                if (move_uploaded_file($tmp_file1, $rename_file1)) {
                    // Thay đổi kích thước và nén ảnh
                    compressImage($rename_file1, $upload_dir3.$f_inst1, 9);
                    //Thay đổi kích thước sau khi nén ảnh
                    resize_image($upload_dir3.$f_inst1, $upload_dir2.$f_inst1, 300, 300);
                    // Hiển thị thông báo thành công
                    $msg = "<strong>Thành công!</strong> File <a href='$upload_dir1/$f_inst1'><i>$f_inst1</i></a> đã được tải thành công vào thư mục: <i>$upload_dir1</i><br>
                    <small>Resized version: <a href='$upload_dir2/$f_inst1'><i>$f_inst1</i></a>| Compressed version: <a href='$upload_dir3/$f_inst1'><i>$f_inst1</i></a></small>";
                    $sta = "success"; // Trạng thái thành công
                } else {
                    // Thông báo lỗi khi tải file thất bại
                    $msg = "<strong>Đã có lỗi xảy ra!</strong> Không thể tải lên file. Vui lòng thử lại.";
                    $sta = "danger"; // Trạng thái nguy hiểm
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- Cấu hình meta và liên kết Bootstrap -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Tải lên File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
        }
        .img-preview {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <!-- Tiêu đề của form -->
                    <h5 class="card-title mb-4">Tải lên File</h5>

                    <!-- Hiển thị thông báo sau khi tải lên -->
                    <?php if ($msg != ""): ?>
                        <div class="alert alert-<?php echo $sta; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Form tải lên -->
                    <form method="POST" enctype="multipart/form-data" class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="formFile" class="form-label">Chọn ảnh để tải lên</label>
                                <input class="form-control" type="file" name="file_upload1" onchange="loadFile(event)" accept=".jpg,.jpeg,.png,.gif">
                                <small class="form-text text-muted">Chúng tôi hỗ trợ các định dạng JPG, PNG và GIF, với dung lượng tối đa 2MB.</small>
                            </div>
                            <button type="submit" name="upload" class="btn btn-primary mt-3">Tải lên ngay</button>
                        </div>

                        <!-- Phần hiển thị ảnh xem trước -->
                        <div class="col-md-4">
                            <div class="text-center">
                                <img src="uploads/placeholder.png" id="output" class="img-preview">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript để hiển thị ảnh xem trước -->
<script>
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src); // Giải phóng bộ nhớ
        };
    };
</script>

<!-- Bootstrap script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
