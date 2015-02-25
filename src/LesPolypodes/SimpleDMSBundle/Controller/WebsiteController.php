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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/", name="_index")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->render('@LesPolypodesSimpleDMS/Website/index.html.twig');
    }

    /**
     * @Route("/folders", name="_folders")
     * @Template()
     *
     * @return array
     */
    public function foldersAction()
    {
        $result['folders'] = $this->get('google_drive')->getFolders();

        return $result;
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
        //$result['children'] = $this->get('google_drive')->getChildren($folderId);
        $result['folders'] = $this->get('google_drive')->getFolders($folderId);
        $result['total'] = count($result['list']);

        if ($request->query->has("pageToken")
            && !empty($result['nextPageToken'])
            && $request->query->get("pageToken") == $result['nextPageToken']) {
            $result['has_pagination'] = false;
        }

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
            array('q' => null, 'pageToken' => null, 'filter' => null),
            array(
                'csrf_protection' => false,
            )
        )
            ->add('q', 'text', array('label' => ' ', 'required' => false))
            // these two fields are reset for now, see form.html.twig
            ->add('pageToken', 'hidden', array('label' => ' ', 'required' => false))
            ->add('type', 'hidden', array('label' => ' ', 'required' => false));
        $form->setMethod('GET');
        $form = $form->getForm();

        $data = array("q" => "");
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
        }

        $optParams = new GoogleDriveListParameters($data['q'], $request->get("pageToken"));
        $result = $this->get('google_drive')->getFilesList(false, $optParams, null, $request->get("type"));
        $result['folders'] = $this->get('google_drive')->getFolders();
        $result['form'] = $form->createView();
        $result['pagination'] = $this->get('google_drive')->buildPagination(
            $result['nextPageToken'],
            $optParams,
            $request->get("type"),
            null
        );
        $filters = $this->get('google_drive')->getTypes();
        $result['filters'] = $filters['grouped'];

        return $result;
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
        $resource = $this->get('google_drive')->getFileMetadataAndContent($fileId);
        $file = $resource['file']['file'];
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
