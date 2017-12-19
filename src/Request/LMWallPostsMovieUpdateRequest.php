<?php

namespace LM\WPPostLikeRestApi\Request;

/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 25/10/17
 * Time: 11:32
 */
class LMWallPostsMovieUpdateRequest
{

    private $errors;

    function __construct()
    {
        $this->errors = array();
    }

    public function validateRequest(\WP_REST_Request $request)
    {
        $files = $request->get_file_params();

        if (!array_key_exists('movie', $files)) {
            $this->errors['movie'] = 'Nessun file caricato';
            return $this->errors;
        }

        $file = $files['movie'];

        $this->checkUploadErrors($file);
        if (!empty($this->errors)) {
            return $this->errors;
        }

        $this->checkFileType($file);
        if (!empty($this->errors)) {
            return $this->errors;
        }

        $this->checkFileSize($file);
        if (!empty($this->errors)) {
            return $this->errors;
        }

        return $this->errors;
    }

    private function checkUploadErrors($file)
    {
        $phpFileUploadErrors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );

        if ($file['error'] !== 0 && array_key_exists($file['error'], $phpFileUploadErrors)) {
            $this->errors['movie'] = $phpFileUploadErrors[$file['error']];
        } elseif ($file['error'] !== 0) {
            $this->errors['movie'] = 'Errore nel caricamento del file';
        }
    }

    private function checkFileType($file)
    {
        $allowedMimeType = array('video/mp4', 'video/quicktime');
        if (!in_array($file['type'], $allowedMimeType)) {
            $this->errors['movie'] = 'Non sono ammessi file di tipo "' . $file['type'] . '". File validi sono: ' . implode(',',
                    $allowedMimeType);
        }
    }

    private function checkFileSize($file)
    {
        //maxSize = 50Mb
        $max = 50 * 1024 * 1024;
        if ($file['size'] > $max) {
            $this->errors['movie'] = 'Il file caricato è troppo grande. il limite è di 50Mb';
        }
    }

}