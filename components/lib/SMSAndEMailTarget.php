<?php

namespace app\components\lib;

use Yii;
use yii\log\Target;
use yii\log\Logger;
use yii\helpers\VarDumper;

class SMSAndEMailTarget extends Target 
{
	public $exportInterval = 1; // переопределим для удобства
	
	// для отладки из отладочной страницы
	static public $test_email;
	static public $test_phone;

	public function init()
    {
    }

	public function export() 
	{
		$to_email_messages = array();
		
		foreach ($this->messages as $message) { 			
error_log(json_encode($message));
			
			// ́warning-сообщения мы потом письмом пошлём
			if ($message[1] == Logger::LEVEL_WARNING /* or $message[1] == Logger::LEVEL_ERROR */) {
				$to_email_messages[] = $message;
				continue;
			}
			
			if ($message[1] == Logger::LEVEL_ERROR) {

				// для каждой ошибки отдельная sms-ка
				$this->sendToSMS(array($message));
			}
		}
		
		// теперь отправляем письмом всё остальное
		$this->sendToEmail($to_email_messages);
	}
	
	public function sendToSMS($messages) 
	{
		$phone = isset($this->phone) ? $this->phone : '79182010059';
		
		if (isset(SMSAndEMailTarget::$test_phone))
 			$phone = SMSAndEMailTarget::$test_phone; // из-за этой строки скрипт отваливается:(
		

		foreach ($messages as $message) {
			$sms_text = $this->_formatMessage($message, true);
			
			/* Send SMS */
			
			error_log(file_put_contents(Yii::getAlias('@runtime').'/logs/sms.txt', "$sms_text\n", FILE_APPEND));
		}
	}
	
	public function sendToEmail($messages) 
	{
		if (empty($messages)) 
			return;

		// сформируем содержимое письма
		$mail_body = '';
		foreach ($messages as $message) {
			$mail_body .= $this->_formatMessage($message)."\n\n";

			file_put_contents(Yii::getAlias('@runtime').'/logs/mail.txt', $this->_formatMessage($message)."\n", FILE_APPEND);
		}

		$mail_subject = empty($this->subject) ? 'Error or Wanings' : $this->subject;
		$mail_from = Yii::$app->params['adminEmail'];
		$mail_to = empty($this->to) ? Yii::$app->params['adminEmail'] : $this->to;

		// если указан отладочная электропочта, то используем её
		if (!empty(SMSAndEMailTarget::$test_email)) {
			$mail_to = SMSAndEMailTarget::$test_email;
		}

		// не отправляет у меня
		/* Yii::$app->mailer->compose()
			->setFrom($email_from)
			->setTo($email_to)
			->setSubject($email_subject)
			->setTextBody($email_body)
			->send(); */
			
		mail(
			$mail_to, 
			$mail_subject, 
			$mail_body, 
			"From: $mail_from\nContent-Language: ru\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit", 
			" -f $mail_from"
		);
error_log(__FILE__.':'.__LINE__);
	}
	
	// форматирует сообщение в строку
	public function _formatMessage($message, $without_stack_trace = false) 
	{
		// распакуем сообщение
		list($text, $level, $category, $timestamp) = $message;
	
		if (!is_string($text)) {
			// exceptions may not be serializable if in the call stack somewhere is a Closure
			if ($text instanceof \Throwable || $text instanceof \Exception) {
				$text = (string) $text;
			} else {
				$text = VarDumper::export($text);
			}
		}
		
		// в некоторый сообщениях стек вызовов прям в тексте сообщения есть
		if (strpos('Stack trace:', $text) === false and $without_stack_trace == false and !empty($message[4]))
			$text .= "\n\nStack trace:\n".print_r($message[4], true);
		
		if ($without_stack_trace)
			$text = preg_replace('~Stack trace:.*~ms', '', $text);
			
		return "[$category:".Logger::getLevelName($level).']['.date('Y-m-d H:i:s', $timestamp).'] '.$text;
	}
}