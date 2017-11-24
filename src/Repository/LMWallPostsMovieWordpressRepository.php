<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use LM\WPPostLikeRestApi\Request\LMWallPostsMovieUpdateRequest;

class LMWallPostsMovieWordpressRepository implements LMWallPostsPictureRepository
{
    private $updateRequest;
    /**
     * @var
     */
    private $uploadDir;
    private $userId;
    private $postId;

    function __construct(
        LMWallPostsMovieUpdateRequest $updateRequest,
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

        $file = $files['movie'];

        if (!$this->initUploadDirectory()) {
            return array('movie' => 'Non è stato possibile creare la cartella per il salvataggio dei file');
        }

        $destination = $this->getPicturePath(time() . '-' . sanitize_file_name($file['name']));
        $source = $file['tmp_name'];

        $image = wp_get_image_editor($source);
        if (is_wp_error($image)) {
            return array('movie' => 'Non è stato possibile salvare l\'immagine caricata');
        }

        $image->set_quality(95);

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
<Files ~ \"^[0-9A-Za-z-_]+\.(mp4|mov)$\">
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
        $dir = trailingslashit(WP_CONTENT_DIR) . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';
        return $dir;
    }

    private function getUploadURL()
    {
        $dir = trailingslashit(WP_CONTENT_URL) . $this->uploadDir . '/' . $this->userId . '/' . $this->postId . '/';
        return $dir;
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