<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use LMWallPostInsertRequest;

class LMWallPostWordpressRepository implements LMWallPostRepository
{
    /**
     * @var LMWallPostInsertRequest
     */
    private $insertRequest;

    function __construct(LMWallPostInsertRequest $insertRequest)
    {
        $this->insertRequest = $insertRequest;
    }

    public function createPost($request)
    {
        $validate = $this->insertRequest->validateRequest($request);

        if($validate !== true) {
            return $validate;
        }

        $dataRequest = $this->insertRequest->getDataFromRequest($request);

        $newPost = array();

        //default values
        $newPost['post_title'] = wp_generate_uuid4();
        $newPost['post_status'] = 'publish';
        $newPost['post_type'] = 'lm_wall';
        $newPost['comment_status'] = 'open';

        if(array_key_exists('title', $dataRequest) && !empty($dataRequest['title'])) {
            $newPost['post_title'] = $dataRequest['title'];
        }else{
            $newPost['post_title'] = 'UID: '. $dataRequest['author'] .' - POSTID: ' . wp_generate_uuid4();
        }

        if(array_key_exists('status', $dataRequest)) {
            $newPost['post_status'] = $dataRequest['status'];
        }

        $newPost['post_content'] = $dataRequest['content'];
        $newPost['post_author'] = $dataRequest['author'];
        $newPost['post_category'] = explode(',', $dataRequest['categories']);

        // creo post
        $postId = wp_insert_post($newPost);

        if(is_wp_error($postId)) {
            return $postId;
        }

        if(array_key_exists('format', $dataRequest)) {
            set_post_format($postId, $dataRequest['format'] );
        }

        return $postId;
    }
}