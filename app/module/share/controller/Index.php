<?php
namespace timetracker\app\module\share\controller;


use chilimatic\lib\di\ClosureFactory;
use timetracker\app\module\main\controller\Application;

class Index extends Application
{
    public function IndexAction()
    {
        $request = ClosureFactory::getInstance()->get('request-handler', []);

        if (!$request->getGet()) {
            $this->errorMessage('No Hash has been sent', _('No hash has been found please check you mail'));
        }



    }
}