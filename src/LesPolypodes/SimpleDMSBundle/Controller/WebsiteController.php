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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FileController
 * @package LesPolypodes\SimpleDMSBundle\Controller
 */
class WebsiteController extends Controller
{

    /**
     * @Route("/folders", name="_folders")
     * @Template()
     * @param Request $request
     *
     * @return array
     */
    public function foldersAction(Request $request)
    {
    }

    /**
     * @Route("/folders/{folderId}", name="_folder")
     * @Template()
     * @param Request $request
     * @param string  $folderId
     *
     * @return array|RedirectResponse
     */
    public function folderAction(Request $request, $folderId)
    {
        $result = $this->get('google_drive')->getFile($folderId);
        $result['children'] = $this->get('google_drive')->getChildren($folderId);

       // die(var_dump($result['file']['parents'][0]['id']));

        return $result;
    }

    /**
     * @Route("/files", name="_files")
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

        $pageToken = null;
        $data = array("q" => "");
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            //var_dump('DUDE, YOUR FORM is VALID!');
        }

        $pageToken = $request->get("pageToken"); // not a form field
        $optParams = new GoogleDriveListParameters($data['q'], $pageToken);
        $result = $this->get('google_drive')->getFilesList(false, $optParams);
        $result['folders'] = $this->get('google_drive')->getFilesList(true);
        //var_dump($optParams, $_GET, $result['files']['nextPageToken']);
        $result['form'] = $form->createView();
        $result['pagination'] = $this->buildPagination($optParams, $result['items']['nextPageToken']);
        //die(var_dump($optParams, $_GET, $request->get('pageToken'), $data, $query, $searchTerm, $result['pagination'], $result['files']));
        return $result;
    }

    /**
     * @param GoogleDriveListParameters $optParams
     * @param string                    $nextPageToken
     *
     * @return array
     */
    protected function buildPagination($optParams, $nextPageToken)
    {
        $nextUrl = '';
        if (!empty($nextPageToken)) {
            $nextUrl = sprintf(
                "?q=%s&pageToken=%s",
                $optParams->getSearchTerm(),
                $nextPageToken
            );
        }

        return array(
            'nextUrl' => $nextUrl,
        );
    }

    /**
     * @Route("/files/{fileId}", name="_file")
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
