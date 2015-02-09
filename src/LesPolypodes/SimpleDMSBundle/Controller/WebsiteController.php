<?php

namespace LesPolypodes\SimpleDMSBundle\Controller;

use LesPolypodes\SimpleDMSBundle\Service\GoogleDriveListParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class WebsiteController extends BaseController
{

    /**
     * @Route("/", name="_files")
     * @Template()
     * @param Request $request
     *
     * @return array
     */
    public function filesAction(Request $request)
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
        $searchTerm = $pageToken = $query = null;
        $data = array("q" => "");
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $data['q'] = $searchTerm = str_replace("'", "\\'", $data['q']);
            //var_dump('DUDE, YOUR FORM is VALID!');
        }

        $query = sprintf("title contains '%s'", $data['q']);
        $query .= sprintf(" or fullText contains '%s'", $data['q']);
        $pageToken = $request->get("pageToken"); // not a form field
        $optParams = new GoogleDriveListParameters($query, $pageToken);

        $result = $this->getList($optParams);
        //var_dump($optParams, $_GET, $result['files']['nextPageToken']);
        $result['form'] = $form->createView();
        $result['pagination'] = $this->buildPagination($optParams, $searchTerm, $data, $result);
        //die(var_dump($optParams, $_GET, $request->get('pageToken'), $data, $query, $searchTerm, $result['pagination'], $result['files']));

        return $result;
    }

    /**
     * @param GoogleDriveListParameters $optParams
     * @param string                    $searchTerm
     * @param array                     $formData
     * @param array                     $apiResponse
     *
     * @return array
     */
    protected function buildPagination($optParams, $searchTerm, $formData, $apiResponse)
    {
        $nextUrl = sprintf(
            "?q=%s&pageToken=%s",
            $searchTerm,
            $apiResponse['files']['nextPageToken'] // 'nextPageToken' value become next *current*
        );

        return array(
            'nextUrl' => $nextUrl,
        );
    }

    /**
     * @Route("/{fileId}", name="_file")
     * @Template()
     *
     * It forces a download response
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
