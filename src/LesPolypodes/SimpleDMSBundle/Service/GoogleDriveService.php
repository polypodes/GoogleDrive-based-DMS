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
            $file = $resource['file'];
            $request     = new \Google_Http_Request($file['downloadUrl'], 'GET', null, null);
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
            $file = $this->service->files->get($fileId);
            $result = array(
                'modelData'         => $file['modelData'],
                'file'              => $file,
            );

            return $result;
        } catch (\Exception $e) {
            $errorMessage = $this->translator->trans('Given File ID do not match any be Google File you can access');
            throw new HttpException(500, $errorMessage, $e);
        }
    }

    /**
     * @param null $folderId
     *
     * @return array
     */
    public function getFolders($folderId = null)
    {
        $folderId = !empty($folderId) ? $folderId :  $this->getRootFolderId();
        $result = $this->getChildren($folderId, true);
        $treeView = array();
        foreach ($result as $index => $folder) {
            $treeView[] = array('id' => $folder['id'], 'title' => $folder['title']);
        }

        return $treeView;
    }

    /**
     * @return mixed
     */
    public function getRootFolderId()
    {
        return $this->container->getParameter('dms.root_folder_id');
    }

    /**
     * @param int  $folderId
     * @param bool $isFolder
     *
     * @slink http://stackoverflow.com/a/16299157/490589 (C# version)
     * @link http://stackoverflow.com/a/17743049/490589 (Java version)
     *
     * @return array
     * @throws \Exception
     */
    public function getChildren($folderId, $isFolder = false)
    {
        if (is_null($folderId)) {
            return;
        }

        $nextToken = null;
        $result = array();
        $optParams = new GoogleDriveListParameters();
        $optParams->setMaxResults(GoogleDriveListParameters::MAX_RESULTS);
        $optParams->setQuery(GoogleDriveListParameters::NO_TRASH);
        $condition = GoogleDriveListParameters::NO_FOLDERS;
        if ($isFolder) {
            $condition = GoogleDriveListParameters::FOLDERS;
        }
        $optParams->setQuery(sprintf(
            "%s and %s",
            $optParams->getQuery(),
            $condition
        ));
        $optParams->setQuery(sprintf(
            "%s and '%s' in parents",
            $optParams->getQuery(),
            $folderId
        ));

        $this->container->get('monolog.logger.queries')->info($optParams->getJson());

        return array_merge($result, $this->service->files->listFiles($optParams->getArray())->getItems());
    }

    /**
     * @param string                    $relativeInterval
     * @param GoogleDriveListParameters $optParams
     *
     * @link http://php.net/manual/en/datetime.formats.relative.php
     *
     * @return array
     */
    public function getLastModifiedFiles($relativeInterval = '-1 week', $optParams = null)
    {
        $now = new \DateTime($relativeInterval);
        $condition = sprintf("modifiedDate > '%s'", $now->format(\DateTime::ISO8601));
        $optParams = empty($optParams) ? new GoogleDriveListParameters() : $optParams;
        $optParams->setQuery(sprintf("%s and (%s)", $condition, $optParams->getQuery()));

        return $this->getFilesList(false, $optParams);
    }

    /**
     * @param bool                      $isFolder
     * @param GoogleDriveListParameters $optParams
     * @param string                    $parentFolderId
     * @param string                    $filter
     *
     * @return array
     */
    public function getFilesList($isFolder = false, GoogleDriveListParameters $optParams = null, $parentFolderId = null, $filter = null)
    {
        $files = $this->getFiles($isFolder, $optParams, $parentFolderId, $filter);
        $result = array(
            'optParams'         => (!is_null($optParams)) ? $optParams->getArray(true) : null,
            'query'             => $files['query'],
            'has_pagination'    => !empty($files['result']['nextPageToken']),
            'usages'            => $this->getUsage(),
            'count'             => count($files['result']['modelData']['items']),
            'nextPageToken'     => $files['result']['nextPageToken'],
            'list'       => $files['result']['modelData']['items'],
        );

        //usort($result['orderedList'], array($this, "fileCompare"));
        return $result;
    }

    /**
     * @param bool                      $isFolder       = true
     * @param GoogleDriveListParameters $optParams
     * @param string                    $parentFolderId
     * @param string                    $filter
     *
     * @link https://developers.google.com/drive/web/search-parameters
     *
     * @throws HttpException
     *
     * @return \Google_Service_Drive_FileList
     */
    public function getFiles($isFolder = true, GoogleDriveListParameters $optParams = null, $parentFolderId = "", $filter = null)
    {
        $result = array();
        $optParams = empty($optParams) ? new GoogleDriveListParameters() : $optParams;
        if (0 < strlen($optParams->getQuery())) {
            $optParams->setQuery(sprintf("%s and (%s)", GoogleDriveListParameters::NO_TRASH, $optParams->getQuery()));
        } else {
            $optParams->setQuery(GoogleDriveListParameters::NO_TRASH, $optParams->getQuery());
        }

        // Filters
        $filters = array();
        $filters['type'] = (!is_null($filter)) ? $this->buildTypeFilter($filter) : null;
        if (empty($filters['type'])) {
            $filters['type'] = $isFolder ? GoogleDriveListParameters::FOLDERS : GoogleDriveListParameters::NO_FOLDERS;
        }
        if (!empty($parentFolderId)) {
            $filters['parentFolder'] = sprintf('"%s" in parents', $parentFolderId);
        }

        foreach ($filters as $filter) {
            $optParams->setQuery(sprintf("%s and (%s)", $optParams->getQuery(), $filter));
        }

        $this->container->get('monolog.logger.queries')->info($optParams->getJson());
        try {
            $files = $this->service->files->listFiles($optParams->getArray());
            $result['query']            = $optParams->getQuery();
            $result['nextPageToken']    = $files->getNextPageToken();
            $result['result']           = $files;
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

    protected function buildTypeFilter($type = "")
    {
        $result = $or = "";
        $types = $this->getFilesTypesPerGroup($type, true);
        if (!empty($types)) {
            foreach ($types as $index => $type) {
                if (0 < $index) {
                    $or = ' or ';
                }
                $result .= sprintf('%s mimeType="%s"', $or, $type);
            }
        }

        return $result;
    }

    /**
     * @param string $groupName
     * @param bool   $uniqueValues
     *
     * @return array
     */
    protected function getFilesTypesPerGroup($groupName = null, $uniqueValues = false)
    {
        $list = $this->getFilesTypesGroupsDefinitions();
        $result = null;

        if (!empty($groupName) && array_key_exists($groupName, $list)) {
            $result = $list[$groupName];

            if ($uniqueValues) {
                $result = array_unique(array_values($result));
            }
        }

        return $result;
    }

    /**
     * Please keep both 'groupedBy' and 'single' lists sync'ed
     *
     * @link http://filext.com/faq/office_mime_types.php
     * @link http://www.openoffice.org/framework/documentation/mimetypes/mimetypes.html
     * @link https://developers.google.com/drive/web/mime-types
     *
     * @return array
     */
    public function getFilesTypesGroupsDefinitions()
    {
        return array(
            "image" => array(
                'jpg' =>         'image/jpeg',
                'png' =>         'image/png',
                'gif' =>         'image/gif',
                'bmp' =>         'image/bmp',
                'gpng' =>       'application/vnd.google.drive.ext-type.png',
                'gjpg' =>       'application/vnd.google.drive.ext-type.jpg',
                'ggif' =>       'application/vnd.google.drive.ext-type.gif',
                'gphoto' =>     'application/vnd.google-apps.photo',
            ),
            'spreadsheet' => array(
                'xls' =>    'application/vnd.ms-excel',
                'xlsx' =>   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xltx' =>   'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'xlsm' =>   'application/vnd.ms-excel.sheet.macroEnabled.12',
                'xltm' =>   'application/vnd.ms-excel.template.macroEnabled.12',
                'xlam' =>   'application/vnd.ms-excel.addin.macroEnabled.12',
                'xlsb' =>   'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'ods' =>    'application/vnd.oasis.opendocument.spreadsheet',
                'ots' =>    'application/vnd.oasis.opendocument.spreadsheet-template',
                'gsheet' => 'application/vnd.google-apps.spreadsheet',
                //'csv' => 'text/plain', // may match a lot of other files: txt, etc.
            ),
            'text' => array(
                'doc' =>     'application/msword',
                'dot' =>    'application/msword',
                'docx' =>   'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dotx' =>   'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'docm' =>   'application/vnd.ms-word.document.macroEnabled.12',
                'dotm' =>   'application/vnd.ms-word.template.macroEnabled.12',
                'odt' =>    'application/vnd.oasis.opendocument.text',
                'ott' =>    'application/vnd.oasis.opendocument.text-template',
                'oth' =>    'application/vnd.oasis.opendocument.text-web',
                'odm' =>    'application/vnd.oasis.opendocument.text-master',
                'gdoc' =>   'application/vnd.google-apps.document',
                'txt' =>    'text/plain',
                'pdf' =>    'application/pdf',
            ),
            'presentation' => array(
                'ppt' =>        'application/vnd.ms-powerpoint',
                'pot' =>        'application/vnd.ms-powerpoint',
                'pps' =>        'application/vnd.ms-powerpoint',
                'ppa' =>        'application/vnd.ms-powerpoint',
                'pptx' =>       'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'potx' =>       'application/vnd.openxmlformats-officedocument.presentationml.template',
                'ppsx' =>       'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'ppam' =>       'application/vnd.ms-powerpoint.addin.macroEnabled.12',
                'pptm' =>       'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'potm' =>       'application/vnd.ms-powerpoint.template.macroEnabled.12',
                'ppsm' =>       'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
                'gslides' =>    'application/vnd.google-apps.presentation',
                'odp' =>        'application/vnd.oasis.opendocument.presentation',
                'otp' =>        'application/vnd.oasis.opendocument.presentation-template',
            ),
            'animation' => array(
                'swf' =>     'application/x-shockwave-flash',
            ),
            'audio' => array(
                'mp3' => 'audio/mpeg',
            ),
            'video' => array(
                'mpeg' =>       'video/mpeg',
                'mp4' =>        'video/mp4',
                'flv' =>        'video/flv',
                'webm' =>       'video/webm',
                'gvideo' =>     'application/vnd.google-apps.video',
            ),
            'archives' => array(
                'zip' =>    'application/zip',
                'rar' =>    'application/rar',
                'tar' =>    'application/tar',
                'arj' =>    'application/arj',
                'cab' =>    'application/cab',
            ),
            'code' => array(
                'php' =>        'application/x-httpd-php',
                'js' =>         'text/js',
                'html' =>       'text/html',
                'xml' =>        'text/xml',
                'gscript' =>    'application/vnd.google-apps.script',
            ),
            'folder' => array(
                'folder' => 'application/vnd.google-apps.folder',
            ),
            'various' => array(
                'default' =>    'application/octet-stream',
                'file' =>       'application/vnd.google-apps.file',
                'unknown' =>    'application/vnd.google-apps.unknown',
            ),
        );
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        return $this->service->about->get();
    }

    /**
     * @return array
     */
    public function getFilesPerType()
    {
        return array();
    }

    /**
     * (A single API call to get both)
     *
     * @return array      (found, grouped, definition)
     * @throws \Exception
     */
    public function getTypes()
    {
        $singles = $this->getFoundTypes();

        return array(
            'found' => $singles,
            'grouped' => $this->getFoundGroupedTypes($singles),
            'definitions' => $this->getFilesTypesGroupsDefinitions(),
        );
    }

    /**
     * @throws \Exception
     */
    protected function getFoundTypes()
    {
        $nextToken = null;
        $loop = 0;
        $maxLoop = 10;
        $maxItemPerLoop = GoogleDriveListParameters::MAX_RESULTS;
        $optParams = new GoogleDriveListParameters();
        $optParams->setMaxResults($maxItemPerLoop);
        $optParams->setQuery(GoogleDriveListParameters::NO_TRASH);
        $items = array();
        do {
            $result = $this->service->files->listFiles($optParams->getArray());
            $items = array_merge($items, array_values($result->getItems()));
            //$items += $result->getItems();

            $nextToken = $result->getNextPageToken();
            $optParams->setPageToken($nextToken);
            $loop++;
            if ($maxLoop <= $loop) {
                // 10 * 1000 files is enough
                throw new \Exception(sprintf("Preventing too-many API calls : max %d loops occurred of %d items each time ", $loop, $optParams->getMaxResults()));
            }
        } while (!empty($nextToken));

        $types = array();
        foreach ($items as $item) {
            $types[$item->mimeType] = $item->mimeType; // trick do avoid duplicate entries
        }

        return array_values($types);
    }

    /**
     * @param array $types
     *
     * Good practice: give a types list in order to limit API calls
     *
     * @return array
     * @throws \Exception
     */
    protected function getFoundGroupedTypes($types = array())
    {
        $types = !empty($types) ? $types : $this->getFoundTypes();
        $definitions = $this->getFilesTypesGroupsDefinitions();
        $result = array();
        foreach ($definitions as $groupName => $definitionList) {
            //var_dump("crawling into $groupName");
            foreach ($definitionList as $ext => $typeMIME) {
                //var_dump("testing $typeMIME:");
                $found = false;
                foreach ($types as $key => $type) {
                    // var_dump(" is $type === with $typeMIME ?");
                    if ($type === $typeMIME) {
                        //var_dump("YES, we add $groupName as a known group");
                        $found    = true;
                        $result[] = $groupName;
                        break;
                    }
                }
                if ($found) {
                    //var_dump("we stop crawling into $groupName.");
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param string                    $nextPageToken
     * @param GoogleDriveListParameters $optParams
     * @param string                    $type          MIME
     * @param string                    $prefixUrl     route Url
     *
     * @return array
     */
    public function buildPagination($nextPageToken, $optParams = null, $type = null, $prefixUrl = null)
    {
        $nextUrl = (!empty($prefixUrl)) ? $prefixUrl : null;
        $searchTerm = (!is_null($optParams)) ? $optParams->getSearchTerm() : '';
        if (!empty($nextPageToken)) {
            $nextUrl .= sprintf(
                "?q=%s&pageToken=%s&type=%s",
                $searchTerm,
                $nextPageToken,
                $type
            );
        }

        return array(
            'nextUrl' => $nextUrl,
        );
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    protected function fileCompare($a, $b)
    {
        return strcmp($a['title'], $b['title']);
    }
}
