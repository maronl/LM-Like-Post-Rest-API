<?php

namespace LM\WPPostLikeRestApi\Request;

/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 25/10/17
 * Time: 11:32
 */
class LMWallPostUpdateRequest
{

    private $errors;

    function __construct()
    {
        $this->errors = array();
    }

    public function validateRequest(\WP_REST_Request $request)
    {
        $data = $this->getDataFromRequest($request);
        return $this->validateData($data);
    }

    public function validateData(array $data)
    {
        $postId = array_key_exists('post_id', $data) ? $data['post_id'] : null;
        $content = array_key_exists('content', $data) ? $data['content'] : null;
        $format = array_key_exists('format', $data) ? $data['format'] : null;
        $file = array_key_exists('file', $data) ? $data['file'] : null;

        $this->validatePostId($postId);
        $this->validateContent($content);
        $this->validateFormat($format);
        $this->validateFile($file);

        if (empty($this->errors)) {
            return true;
        }

        return $this->errors;
    }

    public function getDataFromRequest($request)
    {
        $post_id = $request->get_param('id');
        $content = sanitize_text_field($request->get_param('content'));
        $format = $request->get_param('format');
        $file = $request->get_file_params('following_id');

        return compact('post_id', 'content','format', 'file');
    }

    private function validatePostId($postId)
    {
        if (empty($postId)) {
            $this->errors[] = array('id' => 'Indicare ID del post da modificare');
        }
    }


    private function validateContent($content)
    {
        if (empty($content)) {
            $this->errors[] = array('content' => 'il contenuto di un post non può essere vuoto');
        }
    }

    private function validateFormat($format)
    {
        if (empty($format)) {
            return;
        }

        $validFormats = array('standard', 'image', 'link', 'video');

        if (!in_array($format, $validFormats)) {
            $this->errors[] = array(
                'format' => 'Il campo format può avere solo i seguenti valori: ' . implode(',', $validFormats)
            );
        }
    }

    private function validateFile($file)
    {
        // nessuna regola di validazione il file può essere anche nullo
    }

}