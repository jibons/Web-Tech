<?php
class UploadHandler {
    private $uploadDir;
    
    public function __construct($uploadDir = '../uploads/') {
        $this->uploadDir = $uploadDir;
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadFile($file) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'No file uploaded or upload error'];
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Only allow images
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return ['success' => false, 'error' => 'Only JPG and PNG files are allowed'];
        }

        $newFilename = uniqid() . '.' . $extension;
        $destination = $this->uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $newFilename];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
}
