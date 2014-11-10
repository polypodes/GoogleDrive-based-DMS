<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class FilesController extends Controller
{

    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'folders' => $this->getFolders(),
            'files' => $this->getFiles(),
            'usages' => $this->getUsage()
        );
    }


    /**
     * @return array
     */
    private function getUsage()
    {
        $googleDrive = $this->get('google_drive')->get();
        $about       = $googleDrive->about->get();

        return array(
            "Current user name: "   => $about->getName(),
            "Root folder ID: "      => $about->getRootFolderId(),
            "Total quota (bytes): " => $about->getQuotaBytesTotal(),
            "Used quota (bytes): "  => $about->getQuotaBytesUsed(),
        );
    }


    /**
     * @param bool $isFolder = true
     *
     * @return \Google_Service_Drive_FileList
     */
    private function getFolders($isFolder = true)
    {
        $operator = ($isFolder) ? "=" : "!=";
        $googleDrive = $this->get('google_drive')->get();
        $params = [
            'q' => sprintf("%s%s%s", 'mimeType', $operator, '"application/vnd.google-apps.folder"')
        ];

        return $googleDrive->files->listFiles($params);
    }

    /**
     * @return \Google_Service_Drive_FileList
     */
    private function getFiles()
    {
        return $this->getFolders(false);
    }


}
