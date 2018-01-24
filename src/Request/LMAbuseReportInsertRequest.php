<?php

namespace LM\WPPostLikeRestApi\Request;

/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 25/10/17
 * Time: 11:32
 */
class LMAbuseReportInsertRequest
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
        $userId = array_key_exists('user_id', $data) ? $data['user_id'] : null;
        $type = array_key_exists('reference_type', $data) ? $data['reference_type'] : null;
        $referenceId = array_key_exists('reference_id', $data) ? $data['reference_id'] : null;
        $issue = array_key_exists('issue', $data) ? $data['issue'] : null;

        $this->validateUserId($userId);
        $this->validateType($type);
        $this->validateReferenceId($referenceId);
        $this->validateIssue($issue);

        if (empty($this->errors)) {
            return true;
        }

        return $this->errors;
    }

    public function getDataFromRequest($request)
    {
        $user_id = $request->get_param('user_id');
        $reference_type = $request->get_param('reference_type');
        $reference_id = $request->get_param('reference_id');
        $issue = $request->get_param('issue');

        return compact('user_id', 'reference_type', 'reference_id', 'issue');
    }

    private function validateUserId($userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            $this->errors[] = array('user_id' => 'Indicare ID dell\'utente che riporta l\'abuso');
        }
    }

    private function validateType($type)
    {
        $validTypes = array('user', 'post');
        if (empty($type) || (!in_array($type, $validTypes))) {
            $this->errors[] = array('reference_type' => 'Indicare elemento indicato dall\'abuso. I valori ammessi sono: ' . implode(', ', $validTypes));
        }
    }

    private function validateReferenceId($referenceId)
    {
        if (empty($referenceId) || !is_numeric($referenceId)) {
            $this->errors[] = array('reference_id' => 'Indicare ID dell\'elemento interessato dalla segnalazione di abuso');
        }
    }

    private function validateIssue($issue)
    {
        if (empty($issue)) {
            $this->errors[] = array('issue' => 'Indicare la descrizione dell\'abuso segnalato');
        }

        if(strlen($issue) > 255) {
            $this->errors[] = array('issue' => 'La descrizione dell\'abuso non può superare i 255 caratteri');
        }
    }

}