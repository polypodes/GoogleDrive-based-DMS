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
namespace LesPolypodes\SimpleDMSBundle\Controller;

use LesPolypodes\SimpleDMSBundle\Service\GoogleDriveListParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class ApiController extends Controller
{

    /**
     * @Route("/files/{pageToken}", name="_api_files", defaults={"pageToken"=null})
     * @param Request $request
     * @param string  $pageToken Google-side generated result page token
     *
     * @return array|RedirectResponse
     */
    public function apiFilesListAction(Request $request, $pageToken)
    {
        $optParams = new GoogleDriveListParameters(null, $pageToken);
        $result = $this->get('google_drive')->getFilesList(false, $optParams, null);
        // JSON rendering improvements
        return $this->getJsonResponse($request, $result);
    }

    /**
     * @Route("/files/type/{type}/{pageToken}", name="_api_files_types", defaults={"type"=null,"pageToken"=null})
     * @param Request $request
     * @param string  $type      MIME
     * @param string  $pageToken Google-side generated result page token
     *
     * @return array|RedirectResponse
     */
    public function apiFilesListPerTypeAction(Request $request, $pageToken, $type)
    {
        $optParams = new GoogleDriveListParameters(null, $pageToken);
        $result = $this->get('google_drive')->getFilesList(false, $optParams, null, $type);
        // JSON rendering improvements
        return $this->getJsonResponse($request, $result);
    }

    /**
     * @param Request $request
     * @param mixed   $data
     *
     * @return JsonResponse
     */
    protected function getJsonResponse(Request $request, $data = null)
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

    /**
     * @Route("/files/search/{searchTerm}/{pageToken}", name="_api_files_search", defaults={"searchTerm"=null, "pageToken"=null})
     * @param Request $request
     * @param string  $searchTerm full-text search parameter
     * @param string  $pageToken  Google-side generated result page token
     *
     * @return array|RedirectResponse
     */
    public function apiFilesSearchAction(Request $request, $searchTerm, $pageToken)
    {
        $form = $this->get('form.factory')->createNamedBuilder(
            '',
            'form',
            array('q' => null, 'pageToken' => null),
            array(
                'csrf_protection' => false,
            )
        )
            ->add('q', 'text', array('label' => ' ', 'required' => false))
            ->add('pageToken', 'hidden', array('label' => ' ', 'required' => false));
        $form->setMethod('GET');
        $form = $form->getForm();

        $data = array("q" => $searchTerm);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
        }

        $optParams = new GoogleDriveListParameters($data['q'], $pageToken);
        $result = $this->get('google_drive')->getFilesList(false, $optParams);
        // JSON rendering improvements
        return $this->getJsonResponse($request, $result);
    }

    /**
     * @Route("/folders", name="_folders")
     * @param Request $request
     *
     * @return array
     */
    public function apiFoldersAction(Request $request)
    {
        $result = $this->get('google_drive')->getFolders();

        return $this->getJsonResponse($request, $result);
    }

    /**
     * @Route("/folders/{folderId}", name="_api_folder")
     * @param Request $request
     * @param string  $folderId
     *
     * @return array|RedirectResponse
     */
    public function apiFolderAction(Request $request, $folderId)
    {
        if ($folderId === $this->get('google_drive')->getRootFolderId()) {
            return $this->redirect($this->generateUrl('_files'), 301);
        }
        $optParams = new GoogleDriveListParameters();
        if ($request->query->has("pageToken")) {
            $optParams->setPageToken($request->get("pageToken"));
        }

        $result = $this->get('google_drive')->getFilesList(false, $optParams, $folderId);

        $result['pagination'] = $this->get('google_drive')->buildPagination(
            $result['nextPageToken'],
            $optParams,
            $this->generateUrl('_folder', array('folderId' => $folderId)),
            null
        );
        $result['folder'] = $this->get('google_drive')->getFile($folderId);
        $result['folders'] = $this->get('google_drive')->getFolders($folderId);
        //$result['children'] = $this->get('google_drive')->getChildren($folderId);
        $result['total'] = count($result['list']);

        if ($request->query->has("pageToken")
            && !empty($result['nextPageToken'])
            && $request->query->get("pageToken") == $result['nextPageToken']) {
            $result['has_pagination'] = false;
        }

        return $this->getJsonResponse($request, $result);
    }

    /**
     * @Route("/lastmodified", name="_api_last_modified")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function apiLastModifiedAction(Request $request)
    {
        $data =  $this->get('google_drive')->getLastModifiedFiles();

        return $this->getJsonResponse($request, $data);
    }

    /**
     * @Route("/filetypes", name="_api_filetypes")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function apiFileTypes(Request $request)
    {
        $data =  $this->get('google_drive')->getTypes();

        return $this->getJsonResponse($request, $data);
    }

    /**
     * @Route("/stats", name="_api_stats")
     * @param Request $request
     *
     * @return array|RedirectResponse
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
}
