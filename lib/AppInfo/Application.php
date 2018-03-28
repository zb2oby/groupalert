<?php
namespace OCA\GroupAlert\AppInfo;


use OCP\AppFramework\App;
use OCA\GroupAlert\Controller\AlertController;


class Application extends App {

    /**
     * @param array $urlParams
     */
    public function __construct(array $urlParams = []) {
        parent::__construct('groupalert', $urlParams);
        $container = $this->getContainer();

        $container->registerService('AlertController', function($c){
            return new AlertController(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

    }


}