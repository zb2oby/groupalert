<?php
namespace OCA\GroupAlert;

use OCP\Util;

Util::addStyle('groupalert', 'alert-modal');


// only load when the file app displays
$eventDispatcher = \OC::$server->getEventDispatcher();

$eventDispatcher->addListener(
    'OCA\Files::loadAdditionalScripts',
    function() {
        $userSession = \OC::$server->getUserSession();

        if ($userSession->isLoggedIn()) {
            Util::addScript('groupalert', 'alert-modal');
        }
    }

);
