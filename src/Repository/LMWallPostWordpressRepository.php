<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:19
 */

namespace LM\WPPostLikeRestApi\Repository;


use LM\WPPostLikeRestApi\Request\LMWallPostInsertRequest;
use LM\WPPostLikeRestApi\Request\LMWallPostUpdateRequest;
use LM\WPPostLikeRestApi\Utility\LMWPJWTFirebaseHeaderAuthorization;

class LMWallPostWordpressRepository implements LMWallPostRepository
{
    /**
     * @var LMWallPostInsertRequest
     */
    private $insertRequest;
    /**
     * @var LMWallPostUpdateRequest
     */
    private $updateRequest;
    /**
     * @var LMWallPostsPictureWordpressRepository
     */
    private $pictureRepository;
    /**
     * @var LMWPJWTFirebaseHeaderAuthorization
     */
    private $headerAuthorization;

    function __construct(
        LMWPJWTFirebaseHeaderAuthorization $headerAuthorization,
        LMWallPostInsertRequest $insertRequest,
        LMWallPostUpdateRequest $updateRequest,
        LMWallPostsPictureWordpressRepository $pictureRepository
    ) {
        $this->insertRequest = $insertRequest;
        $this->updateRequest = $updateRequest;
        $this->pictureRepository = $pictureRepository;
        $this->headerAuthorization = $headerAuthorization;
    }

    public function createPost($request)
    {
        $validate = $this->insertRequest->validateRequest($request);

        if ($validate !== true) {
            return $validate;
        }

        // creo post
        $newPost = $this->createNewPostData($request);
        $postId = wp_insert_post($newPost);

        if (is_wp_error($postId)) {
            return $postId;
        }

        $userId = $this->headerAuthorization->getUser();
        // salvo file allegato se è presente
        $this->pictureRepository->setPostId($postId);
        $this->pictureRepository->setUserId($userId);
        $this->pictureRepository->updatePicture($request);

        return $postId;
    }

    public function updatePostContent($request)
    {
        $validate = $this->updateRequest->validateRequest($request);

        if ($validate !== true) {
            return $validate;
        }
        $updateData = $this->createUpdatePostData($request);

        $postId = wp_update_post($updateData);

        if ($postId === 0) {
            return array('msg' => 'Non è stato possibile aggioranre il contenuto del post');
        }

        $userId = $this->headerAuthorization->getUser();
        // salvo file allegato se è presente
        $this->pictureRepository->setPostId($postId);
        $this->pictureRepository->setUserId($userId);
        $this->pictureRepository->updatePicture($request);

        return $postId;
    }

    /**
     * @param $request
     * @return array
     */
    private function createNewPostData($request)
    {
        $dataRequest = $this->insertRequest->getDataFromRequest($request);

        $newPost = array();

        //default values
        $newPost['post_title'] = wp_generate_uuid4();
        $newPost['post_status'] = 'publish';
        $newPost['post_type'] = 'lm_wall';
        $newPost['comment_status'] = 'open';

        if (array_key_exists('title', $dataRequest) && !empty($dataRequest['title'])) {
            $newPost['post_title'] = $dataRequest['title'];
        } else {
            $newPost['post_title'] = 'UID: ' . $dataRequest['author'] . ' - POSTID: ' . wp_generate_uuid4();
        }

        if (array_key_exists('status', $dataRequest)) {
            $newPost['post_status'] = $dataRequest['status'];
        }

        $newPost['post_content'] = $dataRequest['content'];
        $newPost['post_author'] = $dataRequest['author'];
        $newPost['tax_input'] = array('lm_wall_category' => explode(',', $dataRequest['categories']));

        return $newPost;
    }

    private function createUpdatePostData($request)
    {
        $dataRequest = $this->updateRequest->getDataFromRequest($request);

        $updateData = array();

        $updateData['ID'] = $dataRequest['post_id'];
        $updateData['post_content'] = $dataRequest['content'];

        return $updateData;
    }

    public function getLMWallPostsPictureRepository()
    {
        return $this->pictureRepository;
    }

}