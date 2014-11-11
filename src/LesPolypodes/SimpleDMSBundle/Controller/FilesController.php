<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @param bool $isFolder = true
     *
     * @return \Google_Service_Drive_FileList
     */
    private function getFolders($isFolder = true)
    {
        $operator = ($isFolder) ? "=" : "!=";
        $googleDrive = $this->getGoogleDrive();
        $params = [
            'q' => sprintf("%s%s%s", 'mimeType', $operator, '"application/vnd.google-apps.folder"')
        ];

        try {
            return $googleDrive->files->listFiles($params);
        } catch (\Exception $ge) {
            $translator = $this->get('translator');
            $logger = $this->get('logger');
            $errorMessage = sprintf(
                "%s.\n%s.",
                $translator->trans('Google Drive cannot authenticate our [email / .p12 key file]'),
                $translator->trans('Please check the parameters.yml file')
            );
            $logger->error($errorMessage);

            throw new HttpException(500, $errorMessage, $ge);
        }
    }

    protected function getGoogleDrive()
    {
            return $this->get('google_drive')->get();

    }

    /**
     * @return \Google_Service_Drive_FileList
     */
    private function getFiles()
    {
        return $this->getFolders(false);
    }

    /**
     * @return array
     */
    private function getUsage()
    {
        $googleDrive = $this->getGoogleDrive();
        $about       = $googleDrive->about->get();

        return array(
            "Current user name: "   => $about->getName(),
            "Root folder ID: "      => $about->getRootFolderId(),
            "Total quota (bytes): " => $about->getQuotaBytesTotal(),
            "Used quota (bytes): "  => $about->getQuotaBytesUsed(),
        );
    }

}
