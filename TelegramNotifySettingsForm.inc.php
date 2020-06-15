<?php

/**
 * @file plugins/generic/piwik/PiwikSettingsForm.inc.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PiwikSettingsForm
 * @ingroup plugins_generic_piwik
 *
 * @brief Form for managers to modify Piwik plugin settings
 */


import('lib.pkp.classes.form.Form');

class TelegramNotifySettingsForm extends Form {

    /** @var int */
    var $_contextId;

    /** @var object */
    var $_plugin;

    /**
     * Constructor
     * @param $plugin TelegramNotifyPlugin
     * @param $contextId int
     */
    function __construct($plugin, $contextId) {
        $this->_contextId = $contextId;
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
    function initData() {
        $this->_data = array(
            'telegramBotToken' => $this->_plugin->getSetting($this->_contextId, 'telegramBotToken'),
            'telegramStartMessage' => $this->_plugin->getSetting($this->_contextId, 'telegramStartMessage'),
        );
    }

    function getToken() {}

    /**
     * Assign form data to user-submitted data.
     */
    function readInputData() {
        $this->readUserVars(array('telegramBotToken'));
        $this->readUserVars(array('telegramStartMessage'));
    }

    /**
     * Fetch the form.
     * @copydoc Form::fetch()
     */
    function fetch($request) {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request);
    }

    /**
     * Save settings.
     */
    function execute() {
        $this->_plugin->updateSetting($this->_contextId, 'telegramBotToken', trim($this->getData('telegramBotToken'), "\"\';"), 'string');
        $this->_plugin->updateSetting($this->_contextId, 'telegramStartMessage', trim($this->getData('telegramStartMessage'), "\"\';"), 'string');
    }
}

