<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


import('classes.handler.Handler');

class TelegramWebhookHandler extends Handler
{


    public function index($args, $request)
    {


        $t = new TelegramHandler($request->getContext()->getId());

        $update = json_decode(file_get_contents("php://input"), TRUE);
        $message = $update["message"];

        if (strpos($message["text"], "/start") === 0) {
            $t->sendMessage($update["message"]["chat"]["id"], $t->getFirstMessage());
        }

        if ($update["message"]["contact"]) {
            $t->sendMessage($update["message"]["chat"]["id"], 'Send Contact');
//            $t->sendMessage($update["message"]["chat"]["id"], );
            $t->setTelegramChatId($update["message"]["chat"]["id"], intval($update["message"]["contact"]["phone_number"]));
        }


    }

}

class TelegramHandler
{

    var $contextId;

    function __construct($contextId)
    {
        $this->contextId = $contextId;
    }


    public function getTelegramChatId($user_id)
    {
        DAORegistry::getDAO('UserDAO');
        $dao = new UserDAO();
        $user = $dao->getById($user_id);
        return $user->getSetting('telegramChatId');
    }

    public function setTelegramChatId($chatId, $phoneNumber)
    {
        try {
            $user_id = NULL;
            DAORegistry::getDAO('UserDAO');
            $dao = new UserDAO();

            $filteredNumber = intval(substr($phoneNumber, -4));

            $filteredUsers = $dao->retrieve("SELECT user_id, email, phone FROM users WHERE phone LIKE '%$filteredNumber%'")->GetAll();
            foreach ($filteredUsers as $f) {
                if ($this->isEqualPhoneNumber($f['phone'], $phoneNumber) == TRUE) {
                    $user_id = $f['user_id'];
                    break;
                }

            }
            if ($user_id != NULL) {
                $user = $dao->getById($user_id);
                $msg = "";
                $msg .= "Account: " . $user->getFullName() . " ";
                $msg .= $user->getEmail();
                $user->updateSetting('telegramChatId', $chatId);
                $this->sendMessage($chatId, $msg);

            } else {
                $this->sendMessage($chatId, 'Account Not Found');
            }
        } catch (Exception $e) {
            $this->sendMessage($chatId, json_encode($e));
        }

    }

    function isEqualPhoneNumber($phoneA, $phoneB, $substringMinLength = 7)
    {

        $phoneA = preg_replace('/[^0-9]/', '', $phoneA);
        $phoneB = preg_replace('/[^0-9]/', '', $phoneB);

        if ($phoneA == $phoneB) {
            return TRUE;
        }

        // remove "0", "+" from the beginning of the numbers
        if ($phoneA[0] == '0' || $phoneB[0] == '0' ||
            $phoneA[0] == '+' || $phoneB[0] == '+') {
            return $this->isEqualPhoneNumber(ltrim($phoneA, '0+'), ltrim($phoneB, '0+'));
        }

        // change numbers if second is longer
        if (strlen($phoneA) < strlen($phoneB)) {
            return $this->isEqualPhoneNumber($phoneB, $phoneA);
        }

        if (strlen($phoneB) < $substringMinLength) {
            return FALSE;
        }

        // is second number a first number ending
        $position = strrpos($phoneA, $phoneB);
        if ($position !== FALSE && ($position + strlen($phoneB) === strlen($phoneA))) {
            return TRUE;
        }

        return FALSE;
    }

    function sendMessage($chatID, $message)
    {
        $url = "https://api.telegram.org/bot" . $this->getTelegramToken() . "/sendMessage?chat_id=" . $chatID . "&parse_mode=HTML";
        $url = $url . "&text=" . urlencode($message);
        $ch = curl_init();
        $optArray = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
        ];
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getTelegramToken()
    {
        DAORegistry::getDAO('PluginSettingsDAO');
        $dao = new PluginSettingsDAO();
        return $dao->getSetting($this->contextId, 'telegramnotifyplugin', 'telegramBotToken');

    }

    public function getFirstMessage()
    {
        DAORegistry::getDAO('PluginSettingsDAO');
        $dao = new PluginSettingsDAO();
        return $dao->getSetting($this->contextId, 'telegramnotifyplugin', 'telegramStartMessage');

    }

}