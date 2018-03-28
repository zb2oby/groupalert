<?php
namespace OCA\GroupAlert\Controller;

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
     * @return TemplateResponse
     */
    public function getPanel() {
        $json_data = $this->getJson();

        $groups = $json_data['groups'];
        $params = [
            'groups' => str_replace(',', '|', $groups),
            'appUrl' => $this->appUrl,
        ];
        return new TemplateResponse($this->appName, 'settings-admin', $params, '');

    }


}

