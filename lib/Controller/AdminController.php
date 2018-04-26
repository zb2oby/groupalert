<?php
namespace OCA\GroupAlert\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Settings\ISettings;
use OCA\GroupAlert\Db\MessageMapper;
use OCA\GroupAlert\Db\Message;
use OCP\IURLGenerator;
use OCP\AppFramework\Http\JSONResponse;

class AdminController extends Controller implements ISettings
{

    /**
     * @var IGroupManager
     */
    protected $groupManager;
    /**
     * @var string appUrl
     */
    protected $appUrl;
    /**
     * @var MessageMapper
     */
    private $messageMapper;
    /**
     * @var IURLGenerator
     */
    private $urlGenerator;


    /**
     * @param string $appName
     * @param IRequest $request
     */
    public function __construct($appName,
                                IRequest $request,
                                MessageMapper $messageMapper,
                                IURLGenerator $urlGenerator
    )
    {
        parent::__construct($appName, $request);
        $this->messageMapper = $messageMapper;
        $this->urlGenerator = $urlGenerator;
        $this->appUrl = \OC::$WEBROOT . \OC::$APPSROOTS[0]['url'] . '/' . $this->appName . '/';
    }

    /**
     * @return string
     */
    public function getSectionID()
    {
        return 'general';
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * @param $dir
     * @param array $results
     * @return array
     */
    public function getDirContents($dir, &$results = array()) {
        //from specified $dir recursively get content from specified $dir
        $files = \OCA\Files\Helper::getFiles($dir);

        foreach($files as $key => $value){
            $path = $dir.DIRECTORY_SEPARATOR.$value['name'];
            if(!$value['type'] == 'dir') {
                $results[] = $value;
            } else {
                $this->getDirContents($path, $results);
                $results[] = $value;
            }
        }
        //return all files contained in specified $dir
        return $results;
    }

    /**
     * @param $dir
     * @return array
     */
    public function getSharedWithGroupFolders($dir)
    {
        $files = $this->getDirContents($dir);
        $sharedFolders = [];

        foreach ($files as $file) {

            //if it is shared from another user
            if ($file->isShared()) {
                $fileShared = \OC\Share\Share::getItemSharedWithBySource('folder', $file['fileid']);
            }
            //if it is shared from admin to others
            else {
                $fileShared = \OC\Share\Share::getItemShared('folder', $file['fileid']);
            }


            //only if it is a folder
            if ($file['type'] === 'dir') {

                if (!empty($fileShared)) {

                    foreach ($fileShared as $folder) {

                        //two way here because of different array's format of $fileShared due to different method to get it
                        if ($file->isShared()) {
                            //only if it is a group sharing (sharetype = 1)
                            if ($fileShared['share_type'] == 1) {
                                $sharedFolders[$file['name']]['sharedWith'][] = $fileShared['share_with'];
                                break;
                            }
                        } else {
                            //only if it is a group sharing
                            if ($folder['share_type'] == 1) {
                                $sharedFolders[$file['name']]['sharedWith'][] = $folder['share_with'];
                            }
                        }
                    }
                }
            }

        }
        return $sharedFolders;
    }


    /**
     * @return TemplateResponse
     */
    public function getPanel()
    {
        $SharedFolders = $this->getSharedWithGroupFolders('');
        $previousList = $this->messageMapper->findAll();

        $params = [
            'appUrl' => $this->appUrl,
            'sharedGroupFolders' => $SharedFolders,
            'urlGenerator' => $this->urlGenerator,
            'previousList' => $previousList
        ];
        return new TemplateResponse($this->appName, 'settings-admin', $params, '');

    }

    /**
     * @NoCSRFRequired
     * @param string $title
     * @param string $texte
     * @param string $checked
     * @param string groups
     * @param string $folder
     * @param string $sharedWith
     * @return JSONResponse
     */
    public function insertMessage($title, $texte, $checked, $groups, $folder, $sharedWith)
    {
        $message = new Message();
        $message->setDtMessage(strtotime(date('Y-m-d H:i:s')));
        $message->setLastUpdate(strtotime(date('Y-m-d H:i:s')));
        $message->setTitle($title);
        $message->setTexte($texte);
        $message->setChecked($checked);
        $message->setGroups($groups);
        $message->setFolder($folder);

        $error = '';
        //COMPARE EXISTING DATA TO AVOID DUPLICATE ENTRIES
        $msgByFolder = $this->messageMapper->findByFolder($folder); //return msg id
        foreach ($msgByFolder as $msg) {
            $interGroup = array_intersect(explode('|', $msg->getGroups()), explode('|', $groups));
            if (count($interGroup) != 0){
                $error = 'exist';
                break;
            }
        }
        //COMPARE IF SELECTED GROUPS AND FOLDERS SHARED GROUP MATCH
        if ($folder !== '/') {
            $interShare = array_diff(explode('|', $groups), explode('|', $sharedWith));
            if (!empty($interShare)) {
                $error = 'share';
            }
        }

        //CREATE ENTRY
        if ($error === '') {
            //create entry
            $this->messageMapper->insert($message);
            $id = $message->getId();
            $date = date('d/m/Y', $message->getDtMessage());
            $lastUpdate = date('d/m/Y H:i', $message->getLastUpdate());
            $folder = $message->getFolder();
        }
        return new JSONResponse(array(
            'id' => $id,
            'date' => $date,
            'lastUpdate' => $lastUpdate,
            'folder' => $folder,
            'title' => $title,
            'type' => 'create',
            'error' => $error
        ));
    }


    /**
     * @NoCSRFRequired
     * @param int $id
     * @param string $title
     * @param string $texte
     * @param string $checked
     * @param string $groups
     * @param string $folder
     * @param string $sharedWith
     * @return JSONResponse
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function updateMessage($id, $title, $texte, $checked, $groups, $folder, $sharedWith)
    {

        $message = $this->messageMapper->findById($id);
        $message->setTitle($title);
        $message->setTexte($texte);
        $message->setChecked($checked);
        $message->setGroups($groups);
        $message->setFolder($folder);
        $message->setLastUpdate(strtotime(date('Y-m-d H:i:s')));

        $error = '';

        //COMPARE EXISTING DATA TO AVOID DUPLICATE ENTRIES
        $msgByFolder = $this->messageMapper->findByFolder($folder); //return msg id
        foreach ($msgByFolder as $msg) {
            $interGroup = array_intersect(explode('|', $msg->getGroups()), explode('|', $groups));
            if ($msg->getId() != $message->getId() && count($interGroup) != 0){
                $error = 'exist';
                break;
            }
        }
        //COMPARE IF SELECTED GROUPS AND FOLDERS SHARED GROUP MATCH
        if ($folder !== '/') {
            $interShare = array_diff(explode('|', $groups), explode('|', $sharedWith));
            if (!empty($interShare)) {
                $error = 'share';
            }
        }

        if ($error === '') {
            //UPDATE ENTRY
            $this->messageMapper->update($message);
            $id = $message->getId();
            $date = date('d/m/Y', $message->getDtMessage());
            $lastUpdate = date('d/m/Y H:i', $message->getLastUpdate());
            $folder = $message->getFolder();
        }

        return new JSONResponse(array(
            'id' => $id,
            'date' => $date,
            'lastUpdate' => $lastUpdate,
            'title' => $title,
            'folder' => $folder,
            'type' => 'update',
            'error' => $error
        ));
    }

    /**
     * @param $id
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function deleteMessage($id) {
        $message = $this->messageMapper->findById($id);
        $this->messageMapper->delete($message);
    }

    /**
     * @param $id
     * @return JSONResponse
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function displayForm($id) {
        $message = $this->messageMapper->findById($id);
        $id = $message->getId();
        $texte = $message->getTexte();
        $title = $message->getTitle();
        $folder = $message->getFolder();
        $groups = $message->getGroups();
        $checked = $message->getChecked();
        $lastUdate = $message->getLastUpdate();
        $date = $message->getDtMessage();
        return new JSONResponse(array(
            'id' => $id,
            'date' => date('d/m/Y',$date),
            'lastUpdate' => date('d/m/Y H:i',$lastUdate),
            'title' => $title,
            'folder' => $folder,
            'groups' => $groups,
            'texte' => $texte,
            'checked' => $checked
        ));
    }

    /**
     * @param $id
     * @param $checked
     * @return JSONResponse
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function updateDisplay($id, $checked) {
        $message = $this->messageMapper->findById($id);
        $message->setChecked($checked);
        $message->setLastUpdate(strtotime(date('Y-m-d H:i:s')));
        $this->messageMapper->update($message);
        $date = date('d/m/Y', $message->getDtMessage());
        $lastUpdate = date('d/m/Y H:i', $message->getLastUpdate());
        return new JSONResponse(array(
            'date' => $date,
            'lastUpdate' => $lastUpdate
        ));
    }


}
