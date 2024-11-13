<?php
include "resize_image.php";
include "compressed_image.php";
include "generate_unique_filename.php";

function handleImageUpload() {
    global $sta;
    $upload_dir1 = "uploads/raw/";
    $upload_dir2 = "uploads/resized/";
    $upload_dir3 = "uploads/compressed/";
    $msg = "";

    if ($_FILES['file_upload1']['tmp_name'] == "") {
        $msg = "<strong>Rất tiếc!</strong> Không có file để tải lên.";
        $sta = "warning";
    } else {
        if ($_FILES['file_upload1']['size'] > 2 * 1024 * 1024) {
            $msg = "<strong>Rất tiếc!</strong> File quá lớn. Vui lòng tải lên file nhỏ hơn 2MB.";
            $sta = "danger";
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['file_upload1']['type'];

            if (!in_array($file_type, $allowed_types)) {
                $msg = "<strong>Rất tiếc!</strong> Loại file không hợp lệ.";
                $sta = "danger";
            } else {
                $tmp_file1 = $_FILES['file_upload1']['tmp_name'];
                $file_typ1 = pathinfo($_FILES['file_upload1']['name'], PATHINFO_EXTENSION);
                $f_inst1 = generate_unique_filename($upload_dir1, "Hình_ảnh", $file_typ1);
                $rename_file1 = $upload_dir1 . $f_inst1;

                if (move_uploaded_file($tmp_file1, $rename_file1)) {
                    compressImage($rename_file1, $upload_dir3 . $f_inst1, 9);
                    resize_image($upload_dir3 . $f_inst1, $upload_dir2 . $f_inst1, 300, 300);

                    $msg = "<strong>Thành công!</strong> File <a href='$upload_dir1/$f_inst1'><i>$f_inst1</i></a> đã được tải thành công vào thư mục: <i>$upload_dir1</i><br>
                    <small>Resized version: <a href='$upload_dir2/$f_inst1'><i>$f_inst1</i></a>| Compressed version: <a href='$upload_dir3/$f_inst1'><i>$f_inst1</i></a></small>";
                    $sta = "success";
                } else {
                    $msg = "<strong>Đã có lỗi xảy ra!</strong> Không thể tải lên file.";
                    $sta = "danger";
                }
            }
        }
    }

    return $msg;
}
?>


