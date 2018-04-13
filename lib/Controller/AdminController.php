<?php
namespace OCA\GroupAlert\Controller;

use OC\Files\FileInfo;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\Settings\ISettings;

class AdminController extends Controller implements ISettings {



    /** @var IConfig */
    private $config;
    /** @var IGroupManager*/
    protected $groupManager;
    /** @var IUser[] */
    protected $users;
    /** @var string appUrl*/
    protected $appUrl;
    /** @var string json */
    protected $jsonPath;


    /**
     * @param string $appName
     * @param IRequest $request
     * @param IConfig $config
     */
    public function __construct($appName,
                                IRequest $request,
                                IConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->appUrl = \OC::$WEBROOT.\OC::$APPSROOTS[0]['url'].'/'.$this->appName.'/';
        $this->jsonPath =  \OC::$SERVERROOT.\OC::$APPSROOTS[0]['url'].'/'.$this->appName.'/'.'lib/settings.json';
    }

    /**
     * @return string
     */
    public function getSectionID() {
        return 'general';
    }

    /**
     * @return int
     */
    public function getPriority() {
        return 0;
    }

    /**
     * @return array
     */
    public function getJson() {
        $json = $this->jsonPath;

        if (file_exists($json)) {
            $jsonContent = file_get_contents($json);
            $json_data = json_decode($jsonContent, true);

            return $json_data;

        }
    }

    /**
     * @return array
     */
    public function getSharedWithGroupFolders($dir) {
        $files = \OCA\Files\Helper::getFiles($dir);
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

                if (!empty($fileShared)){

                    foreach ($fileShared as $folder) {

                        //two way here because of different array's format of $fileShared due to different method to get it
                        if ($file->isShared()){
                            //only if it is a group sharing
                            if ($fileShared['share_type'] == 1) {
                                $sharedFolders[$file['name']]['sharedWith'][] = $fileShared['share_with'];
                                break;
                            }
                        }else {
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
    public function getPanel() {
        $json_data = $this->getJson();
        $SharedFolders = $this->getSharedWithGroupFolders('/');

        $groups = $json_data['groups'];
        $params = [
            'groups' => str_replace(',', '|', $groups),
            'appUrl' => $this->appUrl,
            'sharedGroupFolders' => $SharedFolders,
        ];
        return new TemplateResponse($this->appName, 'settings-admin', $params, '');

    }

}

