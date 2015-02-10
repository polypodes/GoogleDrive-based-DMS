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
namespace LesPolypodes\SimpleDMSBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class GoogleDriveService
 * @package LesPolypodes\SimpleDMSBundle\Service
 */
class GoogleDriveService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * @var \Google_Service_Drive
     */
    private $service;

    /**
     * @param ContainerInterface  $container
     * @param TranslatorInterface $translator
     * @param LoggerInterface     $logger
     *
     * @throws InvalidConfigurationException
     */
    public function __construct(ContainerInterface $container, TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->container = $container;
        // Check if we have the API key
        $rootDir    = $this->container->getParameter('kernel.root_dir');
        $configDir  = $rootDir.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
        $apiKeyFile = $configDir.$this->container->getParameter('dms.service_account_key_file');
        if (!file_exists($apiKeyFile)) {
            throw new InvalidConfigurationException('Store your Google API key in '.$apiKeyFile.' - see https://code.google.com/apis/console');
        }
        // Perform API authentication
        $apiKeyFileContents  = file_get_contents($apiKeyFile);
        $serviceAccountEmail = $this->container->getParameter('dms.service_account_email');
        $auth    = new \Google_Auth_AssertionCredentials(
            $serviceAccountEmail,
            array('https://www.googleapis.com/auth/drive'),
            $apiKeyFileContents
        );
        $this->client = new \Google_Client();
        if (isset($_SESSION['service_token'])) {
            $this->client->setAccessToken($_SESSION['service_token']);
        }
        $this->client->setAssertionCredentials($auth);
        /*
        if ($this->client->getAuth()->isAccessTokenExpired()) {
            $this->client->getAuth()->refreshTokenWithAssertion($auth);
        }
        */
        $this->translator = $translator;
        $this->logger = $logger;
        $this->service = new \Google_Service_Drive($this->client);
    }

    /**
     * @return \Google_Service_Drive
     */
    public function get()
    {
        return $this->service;
    }

    /**
     * @param GoogleDriveListParameters $optParams)
     *
     * @link https://developers.google.com/drive/web/search-parameters
     *
     * PRO MEMORIA: default file query is
     * mimeType!="application/vnd.google-apps.folder"
     *
     * @return \Google_Service_Drive_FileList
     */
    public function getFiles(GoogleDriveListParameters $optParams = null)
    {
        return $this->getFolders(false, $optParams);
    }

    /**
     * @param bool                      $isFolder  = true
     * @param GoogleDriveListParameters $optParams
     *
     * @link https://developers.google.com/drive/web/search-parameters
     *
     * @throws HttpException
     *
     * @return \Google_Service_Drive_FileList
     */
    public function getFolders($isFolder = true, GoogleDriveListParameters $optParams = null, $noTrash = true)
    {
        $result = array();
        $optParams = empty($optParams) ? new GoogleDriveListParameters() : $optParams;
        $optParams->setQuery(("" == $optParams->getQuery()) ? "" : sprintf(" and (%s)", $optParams->getQuery()));

        $operator = ($isFolder) ? "=" : "!=";
        $filter = sprintf("%s %s %s", 'mimeType', $operator, '"application/vnd.google-apps.folder"');

        $optParams->setQuery(sprintf("%s%s", $filter, $optParams->getQuery()));
        if ($noTrash) {
            $optParams->setQuery(sprintf("%s%s", $optParams->getQuery(), GoogleDriveListParameters::NO_TRASH));
        }
        $this->container->get('monolog.logger.queries')->info($optParams->getJson());

        try {
            $result['query']    = $optParams->getQuery();
            $result['result']   = $this->service->files->listFiles($optParams->getArray());
        } catch (\Exception $ge) {
            $errorMessage = sprintf(
                "%s.\n%s.",
                $this->translator->trans('Google Drive cannot authenticate our [email / .p12 key file]'),
                $this->translator->trans('Please check the parameters.yml file')
            );
            $this->logger->error($errorMessage);

            throw new HttpException(500, $errorMessage, $ge);
        }

        return $result;
    }

    /**
     * get Drive File metadata & content
     *
     * @param string|\Google_Service_Drive_DriveFile $resource downloadUrl or Drive File instance.
     *
     * @return array(\Google_Service_Drive_DriveFile resource, HTTP Response Body content)
     */
    public function getFileMetadataAndContent($resource)
    {
        if (!($resource instanceof \Google_Service_Drive_DriveFile)) {
            $resource = $this->getFile($resource);
        }
        $errorMessage = $this->translator->trans('Given File ID do not match any be Google File you can access');
        if (!empty($resource)) {
            $request     = new \Google_Http_Request($resource->downloadUrl, 'GET', null, null);
            $httpRequest = $this->client->getAuth()->authenticatedRequest($request);
            if ($httpRequest->getResponseHttpCode() == 200) {
                return array(
                    'file'  => $resource,
                    'content'   => $httpRequest->getResponseBody(),
                );
            }
        }
        throw new HttpException(500, $errorMessage);
    }

    /**
     * @param string $fileId
     *
     * @return \Google_Service_Drive_DriveFile $file metadata
     */
    public function getFile($fileId)
    {
        try {
            return $this->service->files->get($fileId);
        } catch (Exception $e) {
            $errorMessage = $this->translator->trans('Given File ID do not match any be Google File you can access');
            throw new HttpException(500, $errorMessage, $e);
        }
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        $about       = $this->service->about->get();

        return array(
            "Current user name: "   => $about->getName(),
            "Root folder ID: "      => $about->getRootFolderId(),
            "Total quota (bytes): " => $about->getQuotaBytesTotal(),
            "Used quota (bytes): "  => $about->getQuotaBytesUsed(),
        );
    }
}
