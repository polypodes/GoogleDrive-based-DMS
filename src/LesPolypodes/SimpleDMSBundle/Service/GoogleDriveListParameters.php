<?php

namespace LesPolypodes\SimpleDMSBundle\Service;

/**
 * Class GoogleDriveListParameters
 * @package LesPolypodes\SimpleDMSBundle\Service
 */
class GoogleDriveListParameters
{
    /**
     * @var string The body of items (files/documents) to which the query applies
     *             "DEFAULT": The items that the user has accessed.
     *             "DOMAIN": Items shared to the user's domain.
     */
    protected $corpus;
    /**
     * @var int Maximum number of files to return.
     *          Acceptable values are 0 to 1000, inclusive. (Default: 100)
     */
    protected $maxResults;
    /**
     * @var string Page token for files
     */
    protected $pageToken;
    /**
     * @var string Query string for searching files
     * @link https://developers.google.com/drive/web/search-parameters
     */
    protected $query;

    /**
     * @param string $corpus
     * @param int    $maxResults
     * @param string $pageToken
     * @param string $query
     */
    public function __construct($corpus = null, $maxResults = 100, $pageToken = null, $query = null)
    {
        $this->corpus     = $corpus;
        $this->maxResults = $maxResults;
        $this->pageToken  = $pageToken;
        $this->query      = $query;
    }

    /**
     * @see Google_Service_Drive_Files_Resource::listFiles()
     *
     * @return array
     */
    public function getArray()
    {
        return array(
            'corpus'     => $this->corpus,
            'maxResults' => $this->maxResults,
            'pageToken'  => $this->pageToken,
            'q'          => $this->query,
        );
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
}
