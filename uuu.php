<?php
session_start();

$correct_password = 'fffffx2'; // 设置您的密码

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['access_password'])) {
    if ($_POST['access_password'] == $correct_password) {
        $_SESSION['access_granted'] = true;
    } else {
        echo "密码错误";
        exit();
    }
}

if (!isset($_SESSION['access_granted']) || $_SESSION['access_granted'] !== true) {
    header('Location: index.html');
    exit();
}

$upload_result = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $target_dir = "ppp/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // 可自行添加更多所支持的文件类型
    $allowed_file_types = array('jpg', 'png', 'jpeg', 'gif', 'rar', 'zip', 'xlsx');

    // 文件类型验证
    if (!in_array($imageFileType, $allowed_file_types)) {
        $upload_result = "抱歉，上传的文件类型不被支持";
        $uploadOk = 0;
    }

    // 文件大小限制
    if ($_FILES["fileToUpload"]["size"] > 500000000) { // 500MB
        $upload_result = "抱歉，文件太。";
        $uploadOk = 0;
    }

    // 检查 $uploadOk 是否设置为 0
    if ($uploadOk == 0) {
        $upload_result = "抱歉，您的文件未上。";
    } else {
        $unique_name = uniqid('', true) . '.' . $imageFileType;
        $unique_file_path = $target_dir . $unique_name;
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $unique_file_path)) {
            $share_link = "https://" . $_SERVER['HTTP_HOST'] . "/ppp/" . $unique_name;
            $link_length = strlen($share_link) * 10; // 根据链接长度计算输入框宽度
            $upload_result = "文件 " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " 已成功上传。<br><div style='border: 1px solid #ccc; border-radius: 5px; padding: 10px; background-color: #f9f9f9; margin-top: 10px;'>分享链接: <input type='text' value='$share_link' id='shareLink' readonly style='width: $link_length px; border: none;'><button onclick='copyLink()' style='background-color: #4CAF50; color: white; padding: 8px 12px; margin-left: 10px; border: none; border-radius: 4px; cursor: pointer;'>点击复制</button></div>";
        } else {
            $upload_result = "抱歉，上传您的文件时出错";
        }
    }



    $_SESSION['upload_result'] = $upload_result;
    header('Location: uuu.php');
    exit();
}

if (isset($_SESSION['upload_result'])) {
    $upload_result = $_SESSION['upload_result'];
    unset($_SESSION['upload_result']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件上传</title>
    <script>
        function copyLink() {
            var copyText = document.getElementById("shareLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // 适用于移动设备
            document.execCommand("copy");
            alert("已复制分享链接: " + copyText.value);
        }

        function clearFileInput() {
            document.getElementById("fileToUpload").value = "";
        }

        window.onload = function() {
            <?php if (!empty($upload_result)): ?>
                clearFileInput();
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <h2>上传文件</h2>
    <form action="uuu.php" method="post" enctype="multipart/form-data">
        选择文件:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="上传文件" name="submit">
    </form>

    <?php
    if (!empty($upload_result)) {
        echo $upload_result;
    }
    ?>
</body>
</html>
