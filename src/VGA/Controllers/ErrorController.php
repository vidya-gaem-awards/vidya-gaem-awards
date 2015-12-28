<?php
namespace VGA\Controllers;

class IndexController extends BaseController
{
    public function indexAction()
    {
        $tpl = $this->twig->loadTemplate('index.twig');
        echo $tpl->render([
            'title' => 'Home'
        ]);
    }
}
