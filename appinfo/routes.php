<?php
$app = new \OCA\GroupAlert\AppInfo\Application();
$app->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'admin#insert_message', 'url' => '/create/message', 'verb' => 'POST'),
        array('name' => 'admin#update_message', 'url' => '/update/message', 'verb' => 'POST'),
        array('name' => 'admin#delete_message', 'url' => '/delete/message', 'verb' => 'POST'),
        array('name' => 'admin#display_form', 'url' => '/display/form', 'verb' => 'POST'),
        array('name' => 'admin#update_display', 'url' => '/update/display', 'verb' => 'POST'),
        array('name' => 'fileView#display_message', 'url' => '/display/message', 'verb' => 'POST'),
    )
));
