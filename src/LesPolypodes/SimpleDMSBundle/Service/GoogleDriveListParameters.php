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

/**
 * Class GoogleDriveListParameters
 * @package LesPolypodes\SimpleDMSBundle\Service
 */
class GoogleDriveListParameters
{

    /**
     * query suffix helper
     */
    const NO_TRASH = " and trashed = false";

    /**
     * @var string Query string for searching files
     * @link https://developers.google.com/drive/web/search-parameters
     */
    protected $query;

    /**
     * @var string Page token for files
     */
    protected $pageToken;

    /**
     * @var int Maximum number of files to return.
     *          Acceptable values are 0 to 1000, inclusive. (Default: 100)
     */
    protected $maxResults;

    /**
     * @var string The body of items (files/documents) to which the query applies
     *             "DEFAULT": The items that the user has accessed.
     *             "DOMAIN": Items shared to the user's domain.
     */
    protected $corpus;

    /**
     * @param string $query
     * @param int    $maxResults
     * @param string $pageToken
     * @param string $corpus
     */
    public function __construct($query = null, $pageToken = null, $maxResults = 100, $corpus = null)
    {
        $this->query      = $query;
        $this->pageToken  = $pageToken;
        $this->maxResults = $maxResults;
        $this->corpus     = $corpus;
    }

    /**
     * @see Google_Service_Drive_Files_Resource::listFiles()
     *
     * @return array
     */
    public function getArray()
    {
        return array(
            'q'          => $this->query,
            'maxResults' => $this->maxResults,
            'pageToken'  => $this->pageToken,
            'corpus'     => $this->corpus,
        );
    }

    /**
     * @see Google_Service_Drive_Files_Resource::listFiles()
     *
     * @return array
     */
    public function getJson()
    {
        return json_encode($this->getArray());
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getPageToken()
    {
        return $this->pageToken;
    }

    /**
     * @param string $pageToken
     */
    public function setPageToken($pageToken)
    {
        $this->pageToken = $pageToken;
    }

    /**
     * @return int
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * @return string
     */
    public function getCorpus()
    {
        return $this->corpus;
    }

    /**
     * @param string $corpus
     */
    public function setCorpus($corpus)
    {
        $this->corpus = $corpus;
    }
}
