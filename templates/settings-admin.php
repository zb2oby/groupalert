<?php
script('groupalert', 'settings-admin');
style('groupalert', 'settings-admin');
?>
<form class="section" id="GA-setMsg-form">
    <h2>
        <label for="GA-setMsg"><?php p($l->t('Group message')); ?></label>
        <span class="icon-info svg" title="<?php p($l->t('Group alert Allow you to display several messages on specific shared folders for specific groups')); ?>"></span>
    </h2>
    <div class="GA-first-entry">
        <select name="GA-selectList" id="GA-selectList">
            <option value="0" selected disabled><?php p($l->t('Select existing message')); ?></option>
        </select>
        <?php p($l->t('Or')); ?>
        <label class="label-button" id="GA-new"><?php p($l->t('Type new message')); ?></label>
    </div>
    <div class="GA-content-values">
        <div class="GA-content-block">
            <h3 class="block-title"><?php p($l->t("Content")); ?></h3>
            <div>
                <label for="GA-setTitle"><?php p($l->t('Title').'(*)'); ?> : </label>
                <input type="text" name="GA-setTitle" id="GA-setTitle">
                <p><?php p($l->t('Message content').'(*)'); ?> : </p>
                <textarea name="GA-setMsg" id="GA-setMsg" cols="50" rows="4" placeholder="<?php p($l->t('Type your message here')); ?>"></textarea>
            </div>
            <div id="GA-groups-form">
                <label for="GA-setGroups"><?php p($l->t('Display for the following groups').'(*)'); ?> : </label>
                <input type="hidden" id="GA-setGroups" name="GA-setGroups" value="<?php p($_['groups']); ?>">
            </div>
            <div id="GA-folder-form">
                <label for="GA-folder"><?php p($l->t("Display into the following folder's view").'(*)'); ?> : </label>
                <select name="GA-folder" id="GA-folder">
                    <option value="/"><?php p($l->t("Main file's view"));?></option>
                   <?php foreach ($_['sharedGroupFolders'] as $folder => $share) { ?>
                       <option data-groups="<?php p(json_encode($share['sharedWith'])); ?>" value="<?php p($folder); ?>"><?php p($folder); ?>
                           (
                           <?php
                               p($l->t("Shared with"));
                               echo ' : '.implode(', ', $share['sharedWith']);
                           ?>
                           )
                       </option>
                   <?php } ?>
                </select>
            </div>
        </div>
        <div class="GA-buttons GA-content-block">
            <h3 class="block-title"><?php p($l->t("Actions")); ?></h3>
            <div class="GA-delete">
                <label class="label-button" id="GA-delete-button" for="GA-delete"><?php p($l->t('Delete')); ?></label>
                <input type="hidden" id="GA-delete">
            </div>
            <div>
                <label class="label-button" id="GA-preview"><?php p($l->t('Preview')); ?></label>
            </div>
            <div>
                <label class="label-button" id="GA-labelActiveDisplay" for="GA-setDisplay"></label>
                <input type="checkbox" name="GA-setDisplay" id="GA-setDisplay">
            </div>
            <div class="GA-submit">
                <label class="label-button" id="GA-submit"><?php p($l->t('Save')); ?></label>
            </div>
        </div>

    </div>

    <div class="GA-alert-error">
        <div id="GA-error-content"></div>
    </div>
    <div class="GA-message GA-prompt">
        <div class="GA-close" id="GA-close-prompt"><img class="svg" src="../../core/img/actions/close.svg" alt="x"></div>
        <div id="GA-prompt-content"></div>
    </div>

    <p id="bug-link"><?php p($l->t('Something went wrong ?')); ?> <a href="https://bitbucket.org/zb2oby/groupalert/issues" target="_blank"><?php p($l->t('Report a bug')); ?></a> </p>

    <!--TRANSLATIONS FOR JS-->
    <ul id="translations">
        <!--ERRORS-->
        <li class="GA-element" id="GA-l10n-errorContext-update"><?php p($l->t('Unable to update message.')); ?></li>
        <li class="GA-element" id="GA-l10n-errorContext-create"><?php p($l->t('Unable to create message.')); ?></li>
        <li class="GA-element" id="GA-l10n-error-share"><?php p($l->t('One or more of selected groups are not allowed to see the selected folder. Please share this folder with the groups for which you want to display this message')); ?></li>
        <li class="GA-element" id="GA-l10n-error-exist"><?php p($l->t('A message already exists in this folder for one or more of selected group, please check your existing messages')); ?></li>
        <li class="GA-element" id="GA-l10n-required"><?php p($l->t('Red fields are required')); ?></li>
        <!--Notifications (OC.notification)-->
        <li id="GA-l10n-notification-disabled"><?php p($l->t('Group message disabled')); ?></li>
        <li id="GA-l10n-notification-enabled"><?php p($l->t('Group message enabled')); ?></li>
        <li id="GA-l10n-notification-save"><?php p($l->t('Group-Alert modifications saved')); ?></li>
        <li id="GA-l10n-notification-add"><?php p($l->t('Group-Alert message created')); ?></li>
        <li id="GA-l10n-notification-delete"><?php p($l->t('Group-Alert message deleted')); ?></li>
        <!--JS GENERATED VIEWS-->
        <li id="GA-l10n-disable-button"><?php p($l->t('Disable group message')); ?></li>
        <li id="GA-l10n-enable-button"><?php p($l->t('Enable group message')); ?></li>
        <li id="GA-l10n-prompt-delete"><?php p($l->t('Do you really want to delete this message ?')); ?></li>
        <li id="GA-l10n-prompt-save"><?php p($l->t('The message is not enabled. Do not forget to enable it to display it')); ?></li>
        <li id="GA-l10n-confirm"><?php p($l->t('I Confirm')); ?></li>
    </ul>

    <!--HIDDEN VALUES FOR JS-->
    <input type="hidden" id="GA-appUrl" value="<?php p($_['appUrl']); ?>">
    <input type="hidden" id="add-new" value="">
</form>
