<?php
namespace OCA\GroupAlert\Db;

use OCP\AppFramework\Db\Entity;


class Message extends Entity {

    protected $dtMessage;
    protected $title;
    protected $texte;
    protected $checked;
    protected $groups;
    protected $folder;
    protected $lastUpdate;

}
