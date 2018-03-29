<?php
namespace OCA\GroupAlert;

use OCP\Util;
use OCP\Files\Folder;
Util::addStyle('groupalert', 'alert-modal');


// only load when the file app displays and if user is in groups defined by application
$eventDispatcher = \OC::$server->getEventDispatcher();

$eventDispatcher->addListener(
    'OCA\Files::loadAdditionalScripts',
    function() {
        $userSession = \OC::$server->getUserSession();

        if ($userSession->isLoggedIn()) {

            //Check settings and userGroup
            $json = \OC::$APPSROOTS[0]['path'].'/groupalert/lib/settings.json';
            if (file_exists($json)) {
                $jsonContent = file_get_contents($json);
                $json_data = json_decode($jsonContent, true);
            }
            $GAGroups = explode(',', $json_data['groups']);

            $user = \OC::$server->getUserSession()->getUser();
            $userGroups = \OC::$server->getGroupManager()->getUserGroupIds($user);

            //$compareGroups = array_intersect($GAGroups, $userGroups);
            $display = false;
            foreach ($GAGroups as $GAgroup) {
                foreach ($userGroups as $userGroup) {
                    if ($GAgroup === $userGroup) {
                        $display = true;
                        break;
                    }
                }
            }
            if ($display) {
                Util::addScript( 'groupalert', 'alert-modal');
            }
        }
    }

);
