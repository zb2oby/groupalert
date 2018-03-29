<?php
script('groupalert', 'settings-admin');
style('groupalert', 'settings-admin');
?>
<form class="section" id="GA-setMsg-form">
    <h2>
        <label for="GA-setMsg"><?php p($l->t('Group message')); ?></label>
        <label class="label-button" id="GA-labelActiveDisplay" for="GA-setDisplay"></label>
        <input type="checkbox" name="GA-setDisplay" id="GA-setDisplay">
        <label class="label-button" id="GA-preview"><?php p($l->t('Preview')); ?></label>
    </h2>
    <p><?php p($l->t('Message content')); ?> : </p>
    <textarea name="GA-setMsg" id="GA-setMsg" cols="50" rows="4" placeholder="<?php p($l->t('Type your message here')); ?>"></textarea>
    <div id="GA-groups-form">
        <label for="GA-setGroups"><?php p($l->t('Display for the following groups')); ?> : </label>
        <input type="hidden" id="GA-setGroups" name="GA-setGroups" value="<?php p($_['groups']); ?>">
    </div>
    <div id="GA-folder-form">
        <label for="GA-folder"><?php p($l->t("Display into the following folder's view")); ?> : </label>
        <select name="GA-folder" id="GA-folder">
            <option value=""><?php p($l->t("Main file's view")); ?></option>
           <?php foreach ($_['listFolder'] as $folder) { ?>
               <option value="<?php p($folder); ?>"><?php p($folder); ?></option>
           <?php } ?>
        </select>

    </div>
    <ul id="translations">
        <li id="GA-l10n-notification-save"><?php p($l->t('Group-Alert modifications saved')); ?></li>
        <li id="GA-l10n-disable"><?php p($l->t('Disable group message')); ?></li>
        <li id="GA-l10n-enable"><?php p($l->t('Enable group message')); ?></li>
    </ul>
    <input type="hidden" id="GA-appUrl" value="<?php p($_['appUrl']); ?>">
</form>
