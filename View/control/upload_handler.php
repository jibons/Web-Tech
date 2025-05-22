<?php
function uploadFile($file, $targetDirectory, $allowedTypes) {
    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    $fileName = basename($file["name"]);
    $targetPath = $targetDirectory . $fileName;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

    // Check if file type is allowed
    if (!in_array($fileType, $allowedTypes)) {
        return ["success" => false, "message" => "Sorry, only " . implode(", ", $allowedTypes) . " files are allowed."];
    }

    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "Sorry, your file is too large. Max size is 5MB."];
    }

    // Generate unique filename
    $uniqueName = uniqid() . '_' . $fileName;
    $targetPath = $targetDirectory . $uniqueName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return ["success" => true, "filename" => $uniqueName];
    } else {
        return ["success" => false, "message" => "Sorry, there was an error uploading your file."];
    }
}
