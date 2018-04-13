<?php
require_once '../../../lib/base.php';
$json = OC::$APPSROOTS[0]['path'].'/groupalert/lib/settings.json';
if (isset($_GET['getUserInfo']) && $_GET['getUserInfo'] == 'groups'){
    $user = \OC::$server->getUserSession()->getUser();
    $userGroups = \OC::$server->getGroupManager()->getUserGroupIds($user);

    $response = [];
    foreach ($userGroups as $group) {
        $response['groups'][] = $group;
    }
    echo json_encode($response['groups']);
}else{

    if (file_exists($json)) {
        //read json file
        $jsonContent = file_get_contents($json);
        $json_data = json_decode($jsonContent, true);

        //considered like authorized fields to be written in json
        $fields = ['title', 'texte', 'checked', 'groups', 'folder', 'date'];

    //CREATE / UPDATE / DELETE

        $response = [];
        //ID IS DEFINED : UPDATE OR DELETE
        if (isset($_GET['id'])) {
            $entryId = htmlentities($_GET['id']);
            foreach ($json_data as $key => $entry) {
                if ($json_data[$key]['id'] == $entryId) {
                    //DELETE ENTRY
                    if (isset($_GET['delete'])) {
                        unset($json_data[$key]);
                        //reorder data in json file
                        $json_data = array_values($json_data);
                        //In any case, insert the data into the json (keep the order after a delete)
                        file_put_contents($json, json_encode($json_data));
                        $response['type'] = 'delete';
                    }else{
                        //COMPARE OTHERS DATA TO AVOID DUPLICATE ENTRIES
                        $inter = false;

                        //verify folders and groups in others data
                        foreach ($json_data as $others => $otherEntry){

                            if($json_data[$others]['id'] != $entryId){
                                $interGroup = array_intersect(explode('|',$json_data[$others]['groups']), explode('|', $_GET['groups']));
                                $interFolder = array_key_exists('folder', array_intersect($json_data[$others], $_GET));

                                if (count($interGroup) != 0 && $interFolder) {
                                    $inter = true;
                                    $error = 'exist';

                                    break;
                                }

                            }
                        }

                        //COMPARE IF SELECTED GROUPS AND FOLDERS SHARED GROUP MATCH
                        if (isset($_GET['sharedWith']) && $_GET['folder'] !== '/') {
                            $interShare = array_diff(explode('|', $_GET['groups']), explode('|', $_GET['sharedWith']));
                            if (!empty($interShare)) {
                                $inter = true;
                                $error = 'share';
                            }
                        }

                        if ($inter){
                            $response['error'] = $error;
                        }else{
                            //UPDATE ENTRY
                            foreach ($fields as $field) {
                                if (isset($_GET[$field]) && !empty($_GET[$field] && $_GET[$field] != '')) {
                                    if ($field == 'date'){
                                        $date = date_parse($_GET[$field]);
                                        if (checkdate($date['month'], $date['day'], $date['year'])) {
                                            $json_data[$key][$field] = strtotime($_GET[$field]);
                                        }
                                    }elseif(is_string($_GET[$field])) {
                                        $json_data[$key][$field] = htmlentities($_GET[$field]);
                                    }

                                }
                            }

                            $response['date'] = date('d/m/Y', $json_data[$key]['date']);
                            file_put_contents($json, json_encode($json_data));
                        }
                        $response['type'] = 'update';
                    }
                }
            }

        }else {

            //COMPARE EXISTING DATA TO AVOID DUPLICATE ENTRIES
            foreach ($json_data as $key => $entry) {
                $inter = array_intersect($json_data[$key], $_GET);
                $interGroup = array_intersect(explode('|',$json_data[$key]['groups']), explode('|', $_GET['groups']));

                if (array_key_exists('folder', $inter) && ( count($interGroup) != 0 ) ){
                    $response['error'] = 'exist';
                }

            }
            //CREATE ENTRY
            if (!isset($response['error'])){

                $key = count($json_data);
                $lastId = intval($json_data[$key-1]['id']);
                foreach ($fields as $field) {
                    if (isset($_GET[$field]) && !empty($_GET[$field] && $_GET[$field] != '')) {
                        //fill input fields
                        $json_data[$key][$field] = htmlentities($_GET[$field]);

                    }
                }

                //create entry
                $entryId = intval($lastId + 1);
                $date = strtotime(date('Y-m-d'));
                //fill default fields
                $json_data[$key]['id'] = $entryId;
                $json_data[$key]['date'] = $date;
                file_put_contents($json, json_encode($json_data));
                $response['id'] = $entryId;
                $response['date'] = date('d/m/Y');
            }
            $response['type'] = 'create';
        }

        //return response to ajax callback
        echo json_encode($response);
    }
}

