<?php


import('lib.pkp.classes.plugins.GenericPlugin');

class TelegramNotifyPlugin extends GenericPlugin
{

    public function register($category, $path, $mainContextId = NULL)
    {

        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return TRUE;
        if ($success && $this->getEnabled()) {

            HookRegistry::register('Mail::send', [$this, 'sendToTelegram']);
            HookRegistry::register('LoadHandler', [$this, 'setWebhookHandler']);

            $this->_registerTemplateResource();
        }
        return $success;

    }

    /**
     * Add a settings action to the plugin's entry in the
     * plugins list.
     *
     * @param Request $request
     * @param array $actionArgs
     * @return array
     */
    public function getActions($request, $actionArgs)
    {

        // Get the existing actions
        $actions = parent::getActions($request, $actionArgs);

        // Only add the settings action when the plugin is enabled
        if (!$this->getEnabled()) {
            return $actions;
        }

        // Create a LinkAction that will make a request to the
        // plugin's `manage` method with the `settings` verb.
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $linkAction = new LinkAction(
            'settings',
            new AjaxModal(
                $router->url(
                    $request,
                    NULL,
                    NULL,
                    'manage',
                    NULL,
                    [
                        'verb' => 'settings',
                        'plugin' => $this->getName(),
                        'category' => 'generic',
                    ]
                ),
                $this->getDisplayName()
            ),
            __('manager.plugins.settings'),
            NULL
        );

        // Add the LinkAction to the existing actions.
        // Make it the first action to be consistent with
        // other plugins.
        array_unshift($actions, $linkAction);

        return $actions;
    }


    public function getDisplayName()
    {
        return __('plugins.generic.telegramnotify.displayName');
    }

    public function setWebhookHandler($hookName, $params)
    {
        $page = $params[0];
        if ($page === 'telegram-webhook') {

            $this->import('TelegramWebhookHandler');
            define('HANDLER_CLASS', 'TelegramWebhookHandler');
            return TRUE;
        }
        return FALSE;
    }

    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();

                AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON,  LOCALE_COMPONENT_PKP_MANAGER);
                $templateMgr = TemplateManager::getManager($request);

                $this->import('TelegramNotifySettingsForm');
                $form = new TelegramNotifySettingsForm($this, $context->getId());

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(TRUE);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(TRUE, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

    public function getDescription()
    {
        return 'Send Notification to User Telegram Account.';
    }


    function sendToTelegram($hookName, $args)
    {
        $this->import('TelegramWebhookHandler');

        $t = new TelegramHandler($this->getCurrentContextId());
        foreach ($this->parseRecipients($args[0]->getRecipients()) as $u) {
            $msg = "";
            $msg .= "<strong>" . $args[0]->getSubject() . "</strong>\n\n";
            $msg .= "" . strip_tags($args[0]->getBody());
            $a = $t->sendMessage($t->getTelegramChatId($u['user_id']), $msg);

        }


    }

    public function parseRecipients($recipients)
    {
        $targetUsers = [];
        foreach ($recipients as $r) {
            $targetUsers[] = $this->getUserData($r['email']);
        }

        return $targetUsers;

    }


    function getUserData($userMail)
    {
        $dao = new UserDAO();
        $user = $dao->getUserByEmail($userMail);
        return [
            'user_id' => $user->getId(),
            'name' => $user->getFullName(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
        ];
    }


}