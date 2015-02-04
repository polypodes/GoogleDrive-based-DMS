<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

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
     * @Route("/files/{token}", name="_api_files", defaults={"token" = null})
     * @param Request $request
     *
     * PRO MEMORIA: default file query is
     * mimeType!="application/vnd.google-apps.folder"
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiListAction(Request $request, $token)
    {
        $token = "3|0|f__-tPKVF_cwQjNqamhyM25GWjFKZFdrMmIweGhWVk5MV1ZFAA";
        $data = $this->getList('', $token);
        $data['files_count'] = count($data['files']['modelData']['items']);
        $data['files_list'] = $data['files']['modelData']['items'];

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
