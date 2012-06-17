<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\ProjectProgress, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ExportController extends Base
{
    /**
     * @Route("/export/contentbooklet.{_format}", requirements={"_method":"GET"}, defaults={"_format": "pdf"})
     */
    public function createProjectAction($_format)
    {
        $this->ensureLoggedIn();

        $project = $this->getProject($this->getFromRequest(new RequestParamater('project')));

        $exportContentBooklet = $this->get('retext.apibundle.export.contentbooklet');
        $html = $exportContentBooklet->exportBookletAsHTML($project);

        if ($_format == 'html') {
            return new Response(
                $html,
                200
            );
        } else {
            $now = new \DateTime();
            $filename = 'ContentBooklet - ' . $project->getName() . '_' . $now->format('Y-m-d_H:i:s');
            $filename = iconv('UTF-8', 'ASCII//TRANSLIT', $filename);
            $filename = preg_replace('/[^\w_-]/i', '', $filename);

            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"'
                )
            );
        }
    }
}
