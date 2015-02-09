<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use LesPolypodes\SimpleDMSBundle\Service\GoogleDriveListParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class ApiController extends BaseController
{

    /**
     * @Route("/files/{searchTerm}/{token}", name="_api_files", defaults={"searchTerm" = null, "token" = null})
     * @param Request $request
     * @param string  $searchTerm full-text search parameter
     * @param string  $token      Google-side generated result page token
     *
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiListAction(Request $request, $searchTerm, $token)
    {
        $form = $this->createFormBuilder()
            ->add('q', 'text', array('label' => ' ', 'required' => false))
            ->setMethod('GET')
            ->getForm();
        $query = '';
        $form->handleRequest($request);
        if (!empty($searchTerm)) {
            $searchTerm = str_replace("'", "\\'", $searchTerm);
            $query = sprintf("title contains '%s'", $searchTerm);
            $query .= sprintf(" or fullText contains '%s'", $searchTerm);
        }

        $optParams = new GoogleDriveListParameters($query, $token);
        $data = $this->getList($optParams);
        // JSON rendering improvements
        $data['search_term'] = $searchTerm;

        return $this->getJsonResponse($request, $data);
    }

    /**
     * @Route("/stats", name="_api_stats")
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiStatsAction(Request $request)
    {
        $data = [
            "total" =>  600, // documents
            "etag" =>   new \DateTime(),
            "stats" =>  [
                "texts" =>  [ "count" => 300,   "percent" =>  50 ],
                "videos" => [ "count" => 75,    "percent" =>   12.5 ],
                "images" => [ "count" => 50,    "percent" =>   25 ],
                "others" => [ "count" => 75,    "percent" =>   12.5 ],
            ],
        ];

        return $this->getJsonResponse($request, $data);
    }

    protected function getJsonResponse(Request $request, $data = array())
    {
        $date = new \DateTime();
        $date->modify('+1 day');

        $response = new JsonResponse($data);
        $response->setExpires($date);
        $response->setETag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);
        $response->headers->set('X-Proudly-Crafted-By', "LesPolypodes.com"); // It's nerdy, I know that.

        return $response;
    }
}
