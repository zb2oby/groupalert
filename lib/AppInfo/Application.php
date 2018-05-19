<?php
namespace OCA\GroupAlert\AppInfo;


use OCP\AppFramework\App;
use OCA\GroupAlert\Controller\AdminController;
use OCP\IContainer;
use OCA\GroupAlert\Db\MessageMapper;
use OCA\GroupAlert\Controller\FileViewController;

class Application extends App {

    /**
     * @param array $urlParams
     */
    public function __construct(array $urlParams = []) {
        parent::__construct('groupalert', $urlParams);
        $container = $this->getContainer();
        $server = $container->getServer();

        $container->registerService('AdminController', function(IContainer $c){
            return new AdminController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('MessageMapper'),
                $c->query('ServerContainer')->getURLGenerator()

            );
        });

        $container->registerService('MessageMapper', function (IContainer $c) use ($server) {
            return new MessageMapper(
                $server->getDatabaseConnection()
            );
        });

        $container->registerService('FileViewController', function (IContainer $c) use ($server) {
            return new FileViewController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('MessageMapper'),
                $c->query('GroupManager')

            );
        });

    }


}