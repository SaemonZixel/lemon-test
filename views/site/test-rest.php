<?php

$this->title = 'Test REST';
$this->registerJs(<<<EOT_JS_CODE
	function login_form_onsubmit(form) {
		try {
		xhr({
			login: form.login.value,
			password: form.password.value
		},
		'/auth', 'POST', function(){
			if (check_error_in_resp(this)) return;
			
			document.getElementById('token').innerHTML = JSON.parse(this.responseText);
			document.getElementById('get-token-btn').style.display = 'none';
			
			comp_refresh();
		});
		} catch(ex) { console.log(ex); } 
		
		return false;
	}
	
	// запрашивает список всех компаний с сервера и заново заполняет таблицу
	function comp_refresh() {
		xhr({}, '/company/list', 'GET', function(){
			if (check_error_in_resp(this)) return;
			
			var resp = JSON.parse(this.responseText);
			
			var html = [];
			
			// срендерим каждую получиную запись в html
			for (var i = 0; i < resp.length; i++) {
				var comp_id = resp[i].comp_id;
				html.push('<tr><td>'+comp_id+'</td>'+
				'<td><input name="comp_name" value="'+resp[i].comp_name.replace(/"/g,'&quot;')+'"></td>'+
				'<td><input name="comp_addr" value="'+resp[i].comp_addr.replace(/"/g,'&quot;')+'"></td>'+
				'<td><input name="comp_phone" value="'+resp[i].comp_phone+'"></td>'+
				'<td><input name="comp_email" value="'+resp[i].comp_email+'"></td>'+
				'<td><button onclick="comp_update(this, '+comp_id+');">Save</button>'+
				'<button onclick="comp_delete(this, '+comp_id+');">Delete</button></td></tr>');
			}
			
			// и добавим строку для создания нового элемента
			html.push('<tr><td>&nbsp;</td>'+
				'<td><input name="comp_name" value=""/></td>'+
				'<td><input name="comp_addr" value=""/></td>'+
				'<td><input name="comp_phone" value=""/></td>'+
				'<td><input name="comp_site" value=""/></td>'+
				'<td><button onclick="comp_insert(this);">Add</button></td></tr>');
				
			document.getElementById('comps').innerHTML = html.join('');
		});
	}
	
	function comp_update(button, comp_id) {
		var inputs = button.parentNode.parentNode.getElementsByTagName('INPUT');
		var comp = {
			comp_name: inputs[0].value,
			comp_addr: inputs[1].value,
			comp_phone: inputs[2].value,
			comp_email: inputs[3].value
		};
		button.setAttribute('disabled', 'disabled');
		button.innerHTML = 'saving...';
		xhr(comp, '/company/update/'+comp_id, 'POST', function(){
			button.removeAttribute('disabled', 'disabled');
			button.innerHTML = 'save';
		
			if (check_error_in_resp(this)) return;
			
			var resp = JSON.parse(this.responseText);
			
			comp_refresh();
		});
	}
	
	function comp_delete(button, comp_id) {
		button.setAttribute('disabled', 'disabled');
		button.innerHTML = 'deleting...';
		xhr({}, '/company/delete/'+comp_id, 'DELETE', function(){
			button.removeAttribute('disabled');
			button.innerHTML = 'delete';
			
			if (check_error_in_resp(this)) return;
			
			var resp = JSON.parse(this.responseText);
			
			comp_refresh();
		});
	}
	
	function comp_insert(button) {
		var inputs = button.parentNode.parentNode.getElementsByTagName('INPUT');
		var comp = {
			comp_name: inputs[0].value,
			comp_addr: inputs[1].value,
			comp_phone: inputs[2].value,
			comp_email: inputs[3].value
		};
		button.setAttribute('disabled', 'disabled');
		button.innerHTML = 'inserting...';
		xhr(comp, '/company/create/', 'POST', function(){
			button.removeAttribute('disabled');
			button.innerHTML = 'insert';
			
			if (check_error_in_resp(this)) return;
			
			var resp = JSON.parse(this.responseText);
		
			comp_refresh();
		});
	}
	
	function check_error_in_resp(req) {
		if(req.status > 299) {
			try {
				var resp = JSON.parse(req.responseText);

				// пришли ошибки валидации?
				if(resp.length) {
					var msg = '';
					for(var i=0; i<resp.length; i++)
						msg += resp[i].message+'\\n';
					alert(msg);
					return true;
				}

				// Exception?
				if('stack-trace' in resp) {
					alert(resp.message);
					return true;
				}
		
				alert(resp.name+'\\n'+resp.message);
			} catch(ex) {
				alert(req.responseText || req.statusText);
			}
			return true;
		}
		
		return false;
	}
	
	function xhr(data, url, method, callback) {
			var data_encoded = [];
			if(data.length) data_encoded.push(data); // просто строки пока что
			else for(var f in data) data_encoded.push(f+'='+encodeURIComponent(data[f]));
			
			var xhr = new XMLHttpRequest();
			xhr.open(method || 'POST', url || '/', true); 
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.setRequestHeader('Accept', 'application/json');
			xhr.setRequestHeader('Authorization', 'Bearer '+document.getElementById('token').innerHTML);
			xhr.onreadystatechange = function(){ 
				if(xhr.readyState != 4) return;
				console.log(xhr.responseText);
				callback.call(xhr);
			};
			xhr.send(data_encoded.join('&'));
			return xhr;
	}
EOT_JS_CODE
, $this::POS_HEAD);
?>
<div style="display:inline-block;background:#eee">
	<form action="" method="" onsubmit="return login_form_onsubmit(this);">
		<fieldset style="border:none">
			<input type="text" name="login" value="demo"/>
		</fieldset>
		<fieldset style="border:none">
			<input type="password" name="password" value="demo"/>
		</fieldset>
		<fieldset style="border:none">
		<span id="token"></span><button id="get-token-btn" type="submit">Auth</button>
		</fieldset>
	</form>
</div>
<p>
</p>
<table cellspacing="0" cellpadding="5" border="1">
	<thead>
		<tr>
			<th>comp_id</th>
			<th>comp_name</th>
			<th>comp_addr</th>
			<th>comp_phone</th>
			<th>comp_email</th>
			<th>(actions)</th>
		</tr>
	</thead>
	<tbody id="comps">
		<!-- <tr><?php foreach ($comps as $comp) { ?>
			<td><?php echo $comp['comp_id']; ?></td>
			<td><input name="comp_name" value="<?php echo htmlspecialchars($comp['comp_name']); ?>"/></td>
			<td><input name="comp_addr" value="<?php echo htmlspecialchars($comp['comp_addr']); ?>"/></td>
			<td><input name="comp_phone" value="<?php echo htmlspecialchars($comp['comp_phone']); ?>"/></td>
			<td><input name="comp_email" value="<?php echo htmlspecialchars($comp['comp_email']); ?>"/></td>
			<td><button onclick="comp_update(this, <?php echo $comp['comp_id']; ?>);">Save</button>
			<button onclick="comp_delete(this, <?php echo $comp['comp_id']; ?>);">Delete</button>
			</td>
			<?php } ?>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input name="comp_name" value=""/></td>
		<td><input name="comp_addr" value=""/></td>
		<td><input name="comp_phone" value=""/></td>
		<td><input name="comp_site" value=""/></td>
		<td><button onclick="comp_insert(this);">Add</button></td>
		</tr> -->
	</tbody>
</table>