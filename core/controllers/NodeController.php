<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

class NodeController extends AppController
{
    public function actionIndex()
    {
	    return $this->render('index');
    }

}
