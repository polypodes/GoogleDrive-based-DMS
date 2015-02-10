<?php
/**
 * This file is part of the SimpleDMS package.
 *
 * (c) 2015 Les Polypodes
 * Made in Nantes, France - http://lespolypodes.com
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * File created by ronan@lespolypodes.com
 */
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
    protected function getFilesList(GoogleDriveListParameters $optParams = null)
    {
        $files = $this->getGoogleDrive()->getFiles($optParams);

        return array(
            'query'             => $files['query'],
            'has_pagination'    => !empty($files['result']['nextPageToken']),
            'usages'            => $this->getGoogleDrive()->getUsage(),
            'folders'           => $this->getGoogleDrive()->getFolders()['result'],
            'files'             => $files['result'],
            'files_count'       => count($files['result']['modelData']['items']),
            'files_list'        => $files['result']['modelData']['items'],
            'folder_count'      => count($files['result']['folders']['modelData']['items']),
            'folder_list'       => $files['result']['folders']['modelData']['items'],
        );
    }

    protected function getFoldersList(GoogleDriveListParameters $optParams = null)
    {
        $folders = $this->getGoogleDrive()->getFolders(true, $optParams);
//        die(var_dump($folders));

        return array(
            'query'             => $folders['query'],
            'has_pagination'    => !empty($files['result']['nextPageToken']),
            'usages'            => $this->getGoogleDrive()->getUsage(),
            'folders'           => $this->getGoogleDrive()->getFolders()['result'],
            'folder_list'       => $folders['result']['modelData']['items'],
            'folder_count'      => count($folders['result']['modelData']['items']),
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
