<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 11/10/17
 * Time: 10:58
 */

namespace LM\WPPostLikeRestApi\Manager;


use LM\WPPostLikeRestApi\Model\LMWallPostModel;

class LMWPWallAdminManager
{
    /**
     * @var LMWallPostModel
     */
    private $wallPostModel;

    public function __construct(LMWallPostModel $wallPostModel) {
        $this->wallPostModel = $wallPostModel;
    }

    public function filter_posts($post_type, $which)
    {
        $this->wallPostModel->filter_posts($post_type, $which);
    }

}