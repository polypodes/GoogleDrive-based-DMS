<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use LesPolypodes\SimpleDMSBundle\Service\GoogleDriveListParameters;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class BaseController extends Controller
{

    /**
     * @param GoogleDriveListParameters $optParams
     *
     * @return array
     */
    protected function getList(GoogleDriveListParameters $optParams = null)
    {
        return array(
            'folders' => $this->getGoogleDrive()->getFolders(),
            'files'   => $this->getGoogleDrive()->getFiles($optParams),
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
