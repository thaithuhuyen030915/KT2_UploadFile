<?php
include "process_image.php";  // Gọi file xử lý ảnh
$msg = "";
$sta = "";

// Xử lý khi nhấn nút tải lên
if (isset($_POST['upload'])) {
    $msg = handleImageUpload();  // Gọi hàm từ file `image_process.php` để xử lý ảnh
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Tải lên File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel = "stylesheet" href = "style.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Tải lên File</h5>
                    <?php if ($msg != ""): ?>
                        <div class="alert alert-<?php echo $sta; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data" class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="formFile" class="form-label">Chọn ảnh để tải lên</label>
                                <input class="form-control" type="file" name="file_upload1" onchange="loadFile(event)" accept=".jpg,.jpeg,.png,.gif">
                            </div>
                            <button type="submit" name="upload" class="btn btn-primary mt-3">Tải lên ngay</button>
                        </div>
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

<script>
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() { URL.revokeObjectURL(output.src); };
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
