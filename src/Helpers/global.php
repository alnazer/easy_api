<?php

use Alnazer\Easyapi\System\Application;
use Alnazer\Easyapi\System\Response;
use Alnazer\Easyapi\System\Request;
use Alnazer\Easyapi\system\Security;

function pd(...$data){
        echo json_encode($data);
        die;
    }

    /**
     * @return Response
     */
    function response(): Response
    {
        return Application::$app->response;
    }

    function request(): Request
    {
        return Application::$app->request;
    }

    function security(): Security
    {
        return Application::$app->security;
    }
