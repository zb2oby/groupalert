<?php
script('groupalert', 'settings-admin');
style('groupalert', 'settings-admin');
?>
<form class="section" id="GA-setMsg-form">
    <h2>
        <label for="GA-setMsg"><?php p($l->t('Group message')); ?></label>
        <label id="GA-labelActiveDisplay" for="GA-setDisplay"></label>
        <input type="checkbox" name="GA-setDisplay" id="GA-setDisplay">
    </h2>
    <p><?php p($l->t('Message content')); ?> : </p>
    <textarea name="GA-setMsg" id="GA-setMsg" cols="50" rows="4" placeholder="<?php p($l->t('Type your message here')); ?>"></textarea>
    <div id="GA-groups-form">
        <label for="GA-setGroups"><?php p($l->t('Display for the following groups')); ?> : </label>
        <input type="hidden" id="GA-setGroups" name="GA-setGroups" value="<?php p($_['groups']); ?>">
    </div>
    <ul id="translations">
        <li id="GA-l10n-disable"><?php p($l->t('Disable group message')); ?></li>
        <li id="GA-l10n-enable"><?php p($l->t('Enable group message')); ?></li>
    </ul>
    <input type="hidden" id="GA-appUrl" value="<?php p($_['appUrl']); ?>">
</form>
