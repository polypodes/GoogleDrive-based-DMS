<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class BaseController extends Controller
{

    /**
     * @param $query string
     *
     * @return array
     */
    protected function getList($query = '')
    {
        return array(
            'folders' => $this->getGoogleDrive()->getFolders(),
            'files'   => $this->getGoogleDrive()->getFiles($query),
            'usages'  => $this->getGoogleDrive()->getUsage(),
        );
    }

    /**
     * @return \LesPolypodes\SimpleDMSBundle\Service\GoogleDriveService
     */
    protected function getGoogleDrive()
    {
        return $this->get('google_drive');
    }
}
