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
     * @Route("/files/{token}", name="_api_files", defaults={"token" = null})
     * @param Request $request
     * @param string  $token   Google-side generated result page token
     *
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiListAction(Request $request, $token)
    {
        $form = $this->createFormBuilder()
            ->add('q', 'text', array('label' => ' ', 'required' => false))
            ->setMethod('GET')
            ->getForm();
        $query = $form_query = '';
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $form_query = $data['q'] = str_replace("'", "\\'", $data['q']);
            $query = sprintf("title contains '%s'", $data['q']);
            $query .= sprintf(" or fullText contains '%s'", $data['q']);
        }

        $optParams = new GoogleDriveListParameters($query, $token);
        $data = $this->getList($optParams);
        // JSON rendering improvements
        $data['form_query'] = $form_query;
        $data['files_count'] = count($data['files']['modelData']['items']);
        $data['files_list'] = $data['files']['modelData']['items'];
        $data['folder_count'] = count($data['folders']['modelData']['items']);
        $data['folder_list'] = $data['folders']['modelData']['items'];

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
