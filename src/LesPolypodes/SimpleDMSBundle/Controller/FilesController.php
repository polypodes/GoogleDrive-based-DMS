<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use Faker\Provider\cs_CZ\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class FilesController extends Controller
{

    /**
     * @Route("/", name="_files")
     * @Template()
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function filesAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('q', 'text', array('label' => ' ', 'required' => false))
            ->getForm();
        $query = '';
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $data['q'] = str_replace("'", "\\'", $data['q']);
                $query = sprintf("title contains '%s'", $data['q']);
                $query .= sprintf(" or fullText contains '%s'", $data['q']);
                $query .= " and trashed = false";
            }
        }

        $result = array(
            'form'      => $form->createView(),
            'folders'   => $this->getGoogleDrive()->getFolders(),
            'files'     => $this->getGoogleDrive()->getFiles($query),
            'usages'    => $this->getGoogleDrive()->getUsage(),
        );

        return $result;
    }

    /**
     * @return \LesPolypodes\SimpleDMSBundle\Service\GoogleDriveService
     */
    protected function getGoogleDrive()
    {
        return $this->get('google_drive');
    }

    /**
     * @Route("/stats", name="_stats")
     * @Template()
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function statsAction(Request $request)
    {
        $date = new \DateTime();
        $date->modify('+1 day');

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

        $response = new JsonResponse($data);
        $response->setExpires($date);
        $response->setETag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);
        $response->headers->set('X-Proudly-Crafted-By', "LesPolypodes.com"); // It's nerdy, I know that.

        return $response;
    }

    /**
     * @Route("/{fileId}", name="_file")
     * @Template()
     *
     * @param $fileId
     *
     * @return Response
     */
    public function fileAction($fileId)
    {
        $resource = $this->getGoogleDrive()->getFileMetadataAndContent($fileId);
        $file = $resource['file'];
        $content = $resource['content'];
        $response = new Response($content, 200);
        $response->headers->set('Cache-Control', '');
        $response->headers->set('Content-Length', $file->fileSize);
        $response->headers->set('Content-Type', $file->mimeType);
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s'));
        $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->originalFilename);
        $response->headers->set('Content-Disposition', $contentDisposition);
        $response->send();

        return $response;
    }
}
