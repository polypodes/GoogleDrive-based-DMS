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
        $files = $this->getGoogleDrive()->getFiles($optParams);

        return array(
            'query'         => $files['query'],
            'has_pagination'    => !empty($files['result']['nextPageToken']),
            'usages'        => $this->getGoogleDrive()->getUsage(),
            'folders'       => $this->getGoogleDrive()->getFolders()['result'],
            'files'         => $files['result'],
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
