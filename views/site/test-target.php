<?php

$this->title = 'Test Target';

?>
<table cellspacing="0" cellpadding="5" border="1">
	<tr>
		<td>
			<button onclick="document.location='?make=error&amp;target='+document.getElementById('phone').value">Make Error</button><br>
			<input id="phone" type="phone" name="phone" value="+79200221323"/>
		</td>
		<td><button onclick="document.location='?make=warning&amp;target='+document.getElementById('email').value">Make Warning</button><br>
			<input id="email" type="email" name="email" value="saemon@yandex.ru"/>
		</td>
	</tr>
	<tr>
		<td><textarea id="sms_txt" style="width:400px;height:300px" placeholder="(sms.txt)"><?php if(file_exists(Yii::getAlias('@runtime').'/logs/sms.txt')) echo file_get_contents(Yii::getAlias('@runtime').'/logs/sms.txt'); ?></textarea><br><button onclick="document.location='?make=refresh-'+(new Date())*1">refresh</button></td>
		<td><textarea id="mail_txt" style="width:400px;height:300px" placeholder="(mail.txt)"><?php if(file_exists(Yii::getAlias('@runtime').'/logs/mail.txt')) echo file_get_contents(Yii::getAlias('@runtime').'/logs/mail.txt'); ?></textarea><br><button onclick="document.location='?make=refresh-'+(new Date())*1">refresh</button></td>
	</tr>
</table>
<pre><?php
// print_r($context);
?></pre>