<?php

namespace App\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;

class BaseRestController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @param null $data
     * @param null $statusCode
     * @param array $headers
     * @param array $serializedGroups
     * @return View
     */
    protected function view($data = null, $statusCode = null, array $headers = [], $serializedGroups = [])
    {
        $view = View::create($data, $statusCode, $headers);
        if ($serializedGroups) {
            $view->setContext((new Context())->setGroups($serializedGroups));
        }
        $view->setFormat('json');

        return $view;
    }
}
