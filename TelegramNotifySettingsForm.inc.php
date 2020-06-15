<?php

import('lib.pkp.classes.form.Form');

class TelegramNotifySettingsForm extends Form
{

    /** @var int */
    var $_context;

    /** @var object */
    var $_plugin;

    /**
     * Constructor
     * @param $plugin TelegramNotifyPlugin
     * @param $context Context
     */
    function __construct($plugin, $context)
    {
        $this->_context = $context;
        $this->_plugin = $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->addCheck(new FormValidator($this, 'telegramBotToken', 'required', 'plugins.generic.piwik.manager.settings.telegramBotTokenRequired'));
        $this->addCheck(new FormValidator($this, 'telegramStartMessage', 'required', 'plugins.generic.piwik.manager.settings.telegramBotTokenRequired'));

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Initialize form data.
     */
    function initData()
    {
        $this->_data = [
            'telegramBotToken' => $this->_plugin->getSetting($this->_context->getId(), 'telegramBotToken'),
            'telegramStartMessage' => $this->_plugin->getSetting($this->_context->getId(), 'telegramStartMessage'),
        ];
    }

    function getToken()
    {
    }

    /**
     * Assign form data to user-submitted data.
     */
    function readInputData()
    {
        $this->readUserVars(['telegramBotToken']);
        $this->readUserVars(['telegramStartMessage']);
    }

    /**
     * Fetch the form.
     * @copydoc Form::fetch()
     */
    function fetch($request)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request);
    }

    /**
     * Save settings.
     */
    function execute()
    {
        $this->_plugin->updateSetting($this->_context->getId(), 'telegramBotToken', trim($this->getData('telegramBotToken'), "\"\';"), 'string');
        $this->_plugin->updateSetting($this->_context->getId(), 'telegramStartMessage', trim($this->getData('telegramStartMessage'), "\"\';"), 'string');
    }
}

