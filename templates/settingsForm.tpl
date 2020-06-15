{**
 * plugins/generic/telegramnotify/templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Piwik plugin settings
 *
 *}
<div id="telegramNotify">
    <div id="description">{translate key="plugins.generic.telegramnotify.manager.settings.description"}</div>

    <div class="separator"></div>

    <br />

    <script>
        $(function() {ldelim}
            // Attach the form handler.
            $('#telegramNotifyForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
            {rdelim});
    </script>
    <form class="pkp_form" id="telegramNotifyForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
        {csrf}

        {fbvFormArea id="telegramNotifyFormArea"}
        {fbvFormSection for="telegramBotToken" title="plugins.generic.telegramnotify.manager.settings.telegramnotifyToken" description="plugins.generic.telegramnotify.manager.settings.telegramnotifyTokenInstructions"}
        {fbvElement type="text" id="telegramBotToken" name="telegramBotToken" value=$telegramBotToken label="plugins.generic.telegramnotify.manager.settings.telegramnotifyTokenRequired" required=true}
        {/fbvFormSection}

        {fbvFormSection for="telegramStartMessage" title="plugins.generic.telegramnotify.manager.settings.telegramnotifyFirstMessage" description="plugins.generic.telegramnotify.manager.settings.telegramnotifyFirstMessageInstructions"}
        {fbvElement type="text" id="telegramStartMessage" name="telegramStartMessage" value=$telegramStartMessage label="plugins.generic.telegramnotify.manager.settings.telegramnotifyFirstMessageRequired" required=true}
        {/fbvFormSection}

        {/fbvFormArea}

        {fbvFormButtons}
    </form>

    <p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
