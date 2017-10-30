<?php

/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 25/10/17
 * Time: 11:32
 */
class LMWallPostInsertRequest
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
        $title = array_key_exists('title', $data) ? $data['title'] : null;
        $content = array_key_exists('content', $data) ? $data['content'] : null;
        $author = array_key_exists('author', $data) ? $data['author'] : null;
        $status = array_key_exists('status', $data) ? $data['status'] : null;
        $categories = array_key_exists('categories', $data) ? $data['categories'] : null;
        $format = array_key_exists('format', $data) ? $data['format'] : null;
        $file = array_key_exists('file', $data) ? $data['file'] : null;

        $this->validateTitle($title);
        $this->validateContent($content);
        $this->validateAuthor($author);
        $this->validateStatus($status);
        $this->validateCategories($categories);
        $this->validateFormat($format);
        $this->validateFile($file);

        if(empty($this->errors)) {
            return true;
        }

        return $this->errors;
    }

    public function getDataFromRequest($request)
    {
        $title = sanitize_text_field($request->get_param('title'));
        $content = sanitize_text_field($request->get_param('content'));
        $author = $request->get_param('author');
        $status = $request->get_param('status');
        $categories = $request->get_param('categories');
        $format = $request->get_param('format');
        $file = $request->get_file_params('following_id');
        $shared_post = $request->get_param('shared_post');

        return compact('title', 'content', 'author', 'status', 'categories', 'format', 'file', 'shared_post');
    }

    private function validateTitle($title)
    {
        // nessuna regola di validazione il titolo può essere anche nullo
        // il testo viene già ripulito con sanitize_text_field
    }

    private function validateContent($content)
    {
        if(empty($content)) {
            $this->errors[] = array('content' => 'il contenuto di un nuovo post non può essere vuoto');
        }
    }

    private function validateAuthor($author)
    {
        if(empty($author)) {
            $this->errors[] = array('author' => 'Non è possibile creare un nuovo post senza indicare l\'autore del post');
        }

        if(!is_numeric($author) || get_user_by('ID', $author) === false) {
            $this->errors[] = array('author' => 'Il campo autore non è valorizzato correttamente. L\'autore indica non esiste');
        }
    }

    private function validateStatus($status)
    {
        if(empty($status)) {
            return;
        }

        $validStatus = array('publish', 'pending');

        if(!in_array($status, $validStatus)) {
            $this->errors[] = array('status' => 'Il campo status può avere solo i seguenti valori: ' . implode(',', $validStatus));
        }
    }

    private function validateCategories($categories)
    {
        if(empty($categories)) {
            $this->errors[] = array('categories' => 'Non è possibile creare un nuovo post senza indicare la categoria del post');
        }
    }

    private function validateFormat($format)
    {
        if(empty($format)) {
            return;
        }

        $validFormats = array('standard', 'image', 'link', 'video');

        if(!in_array($format, $validFormats)) {
            $this->errors[] = array('format' => 'Il campo format può avere solo i seguenti valori: ' . implode(',', $validFormats));
        }
    }
    private function validateFile($file)
    {
        // nessuna regola di validazione il file può essere anche nullo
    }

}