<?php

namespace LesPolypodes\SimpleDMSBundle\Service;

/**
 * Class GoogleDriveListParameters
 * @package LesPolypodes\SimpleDMSBundle\Service
 */
class GoogleDriveListParameters
{
     /**
     * @var string Query string for searching files
     * @link https://developers.google.com/drive/web/search-parameters
     */
    protected $query;

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
    public function __construct($query = null, $maxResults = 100, $pageToken = null, $corpus = null)
    {
        $this->query      = $query;
        $this->maxResults = $maxResults;
        $this->pageToken  = $pageToken;
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
