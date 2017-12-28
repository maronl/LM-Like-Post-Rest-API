<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use LM\WPPostLikeRestApi\Request\LMWallPostsPictureUpdateRequest;

class LMWallPostsPictureWordpressRepository implements LMWallPostsPictureRepository
{
    private $updateRequest;
    /**
     * @var
     */
    private $uploadDir;
    private $userId;
    private $postId;

    function __construct(
        LMWallPostsPictureUpdateRequest $updateRequest,
        $uploadDir
    ) {
        $this->updateRequest = $updateRequest;
        $this->uploadDir = $uploadDir;
        $this->userId = null;
        $this->postId = null;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    public function updatePicture($request)
    {
        $validation = $this->updateRequest->validateRequest($request);

        if (!empty($validation) && is_array($validation)) {
            return $validation;
        }

        // cancella file legati all'utente
        $this->deletePicture();

        // salva il nuovo file
        return $this->savePicture($request);
    }

    public function deletePicture()
    {
        $files = glob($this->getUploadDir() . '*'); // get all file names

        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            } // delete file
        }

        return true;
    }

    private function savePicture($request)
    {
        $files = $request->get_file_params();

        $file = $files['picture'];

        if (!$this->initUploadDirectory()) {
            return array('picture' => 'Non è stato possibile creare la cartella per il salvataggio dei file');
        }

        $destination = $this->getPicturePath(time() . '-' . sanitize_file_name($file['name']));
        $source = $file['tmp_name'];

        $image = wp_get_image_editor($source);
        if (is_wp_error($image)) {
            return array('picture' => 'Non è stato possibile salvare l\'immagine caricata');
        }

        // extract crea le variabili $height e $width;
        extract($image->get_size());

        $resize_width = 1366;
        if($width > $resize_width) {
            $image->resize($resize_width, ($height * $resize_width) / $width, false);
        }

        $image->set_quality(30);

        $image->save($destination);

        return $destination;
    }

    public function initUploadDirectory()
    {
        $dir = $this->getUploadDir();
        $dirArray = explode('/', $dir);
        $dir = "/";
        foreach ($dirArray as $folder) {
            if (empty($folder)) {
                continue;
            }
            $dir .= $folder . '/';
            if (!file_exists($dir) && !is_dir($dir)) {
                mkdir($dir, 0755);
            }
        }

        $f = fopen($dir . ".htaccess", "a+");
        fwrite($f, " Order deny,allow
Deny from all
<Files ~ \"^[0-9A-Za-z-_]+\.(jpg|png)$\">
Allow from all
</Files>");
        fclose($f);

        if (!file_exists($dir . ".htaccess")) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function getUploadDir()
    {
        if (!defined('LM_SF_REST_API_UPLOAD_FILES_PATH')) {
            $dir = trailingslashit(WP_CONTENT_DIR) . 'secured-uploads/' . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';
        }else{
            $dir = trailingslashit(LM_SF_REST_API_UPLOAD_FILES_PATH) . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';

        }
        return $dir;
    }

    private function getUploadURL()
    {
        if (!defined('LM_SF_REST_API_UPLOAD_FILES_URL')) {
            $url = trailingslashit(WP_CONTENT_URL) . 'secured-uploads/' . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';
        }else{
            $url = trailingslashit(LM_SF_REST_API_UPLOAD_FILES_URL) . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';
        }
        return $url;
    }

    public function getPicturePath($filename = null)
    {
        if (is_null($filename)) {
            $filename = $this->getPictureFileName();
        }
        if (empty($filename)) {
            return false;
        }
        $destination = $this->getUploadDir() . $filename;
        $destination = str_replace('wp/../', '', $destination);
        return $destination;
    }

    public function getPictureURL($filename = null)
    {
        if (is_null($filename)) {
            $filename = $this->getPictureFileName();
        }
        if (empty($filename)) {
            return false;
        }
        $path = $this->getUploadURL() . $filename;
        $path = str_replace('wp/../', '', $path);
        return $path;
    }

    private function getPictureFileName()
    {
        $files = glob($this->getUploadDir() . '*'); // get all file names

        if (is_array($files) && isset($files[0])) {
            $filePath = $files[0];
            $filename = explode('/', $filePath);
            return end($filename);
        }

        return false;
    }

}