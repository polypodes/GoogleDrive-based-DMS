<?php

namespace LesPolypodes\SimpleDMSBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GoogleDriveService
 * @package LesPolypodes\SimpleDMSBundle\Service
 */
class GoogleDriveService {

    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @var \Google_Service_Drive
     */
    private $service;


    /**
     * @param ContainerInterface $container
     *
     * @throws \InvalidConfigurationException
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        // Check if we have the API key
        $rootDir    = $this->container->getParameter('kernel.root_dir');
        $configDir  = $rootDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $apiKeyFile = $configDir . $this->container->getParameter('dms.service_account_key_file');
        if (!file_exists($apiKeyFile)) {
            throw new \InvalidConfigurationException('Store your Google API key in ' . $apiKeyFile . ' - see https://code.google.com/apis/console');
        }
        // Perform API authentication
        $apiKeyFileContents  = file_get_contents($apiKeyFile);
        $serviceAccountEmail = $this->container->getParameter('dms.service_account_email');
        $auth    = new \Google_Auth_AssertionCredentials(
            $serviceAccountEmail,
            array('https://www.googleapis.com/auth/drive'),
            $apiKeyFileContents
        );
        $client = new \Google_Client();
        if (isset($_SESSION['service_token'])) {
            $client->setAccessToken($_SESSION['service_token']);
        }
        $client->setAssertionCredentials($auth);
        /*
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($auth);
        }
        */
        $this->service = new \Google_Service_Drive($client);
    }

    /**
     * @return \Google_Service_Drive
     */
    public function get() {
        return $this->service;
    }


}