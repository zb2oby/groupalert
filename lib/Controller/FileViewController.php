<?php
namespace OCA\GroupAlert\Controller;

use OCA\GroupAlert\Db\MessageMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IGroupManager;
use OCP\IRequest;

class FileViewController extends Controller {

    /**
     * @var IGroupManager
     */
    private $groupManager;
    /**
     * @var MessageMapper
     */
    private $messageMapper;


    public function __construct($appName,
                                IRequest $request,
                                MessageMapper $messageMapper,
                                IGroupManager $groupManager
    )
    {
        parent::__construct($appName, $request);
        $this->messageMapper = $messageMapper;
        $this->groupManager = $groupManager;
    }


    /**
     * @NoAdminRequired
     * @PublicPage
     * @param $targetDir
     * @return JSONResponse
     */
    public function displayMessage($targetDir) {
        $userGroups = $this->groupManager->getUserGroupIds(\OC::$server->getUserSession()->getUser());
        $messages = $this->messageMapper->findAll();
        $display = 'false';
        foreach ($messages as $message) {
        //$message = $messages[1];
            $folder = $message->getFolder();
            $checked = $message->getChecked();
            $text = $message->getTexte();
            $groupsMsg = explode('|',$message->getGroups());

            $interGroup = array_intersect($userGroups, $groupsMsg);

            if (count($interGroup) !== 0 && $checked == 'true' && $folder == $targetDir) {
                $display = 'true';
                break;
            }
        }
        return new JSONResponse(array(
            'display' => $display,
            'text' => $text
        ));
    }

}

