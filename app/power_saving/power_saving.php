<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2018-2020
	the Initial Developer. All Rights Reserved.

	The Additonal Developer of the App Code is
	Chris Behrends <contact@redbehrend.com>
	Portions created by the Additional Developer are Copyright (C) 2020-2020
	the Additional Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Chris Behrends <contact@redbehrend.com>
*/

//includes
    require_once "root.php";
    require_once "resources/require.php";
	require_once "resources/check_auth.php";
	
//check permissions
	if (!permission_exists('power_saving_view')) {
		echo "Access Denied";
		exit;
	}

	$app_uuid = '7515138b-d6e1-430c-8acb-87fe930ceea8';

	//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get total power saving count
	$sql = "select count(*) from v_default_settings ";
	$sql .= "where app_uuid = :app_uuid ";
	$parameters['app_uuid'] = $app_uuid;
	$database = new database;
	$total_power_saving = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);


//get http post variables and set them to php variables
	if (count($_POST) > 0) {

	//set variables from http values
		$domain_uuid = $_POST["select_domain"];
		$yealink_ps_enabled = $_POST["tinput_yealink_ps_enabled"];
		$yealink_ps_intel_mode = $_POST["tinput_yealink_ps_intel_mode"];
		$yealink_ps_days_all = $_POST["input_yealink_ps_days_all"];
		$yealink_ps_days_all_toggle = $_POST["tinput_yealink_ps_days_all"];
		$yealink_ps_days_workweek = $_POST["input_yealink_ps_days_workweek"];
		$yealink_ps_days_workweek_toggle = $_POST["tinput_yealink_ps_days_workweek"];
		$yealink_ps_day_monday = $_POST["input_yealink_ps_day_monday"];
		$yealink_ps_day_tuesday = $_POST["input_yealink_ps_day_tuesday"];
		$yealink_ps_day_wednesday = $_POST["input_yealink_ps_day_wednesday"];
		$yealink_ps_day_thursday = $_POST["input_yealink_ps_day_thursday"];
		$yealink_ps_day_friday = $_POST["input_yealink_ps_day_friday"];
		$yealink_ps_day_saturday = $_POST["input_yealink_ps_day_saturday"];
		$yealink_ps_day_sunday = $_POST["input_yealink_ps_day_sunday"];
		$yealink_ps_idle_timeout = $_POST["input_yealink_ps_idle_timeout"];
		$yealink_ps_hour_timeout = $_POST["input_yealink_ps_hour_timeout"];
		$yealink_ps_offhour_timeout = $_POST["input_yealink_ps_offhour_timeout"];
		$yealink_ps_led_on = $_POST["input_yealink_ps_led_on"];
		$yealink_ps_led_off = $_POST["input_yealink_ps_led_off"];
	}

//process the HTTP POST
	if (count($_POST) > 0 && strlen($_POST["persistformvar"]) == 0) {

	//set update
		//$action = "update";

	//validate the token
		$token = new token;
		if (!$token->validate($_SERVER['PHP_SELF'])) {
			message::add($text['message-invalid_token'],'negative');
			header('Location: power_saving.php');
			exit;
		}


	//check for all required data
		$msg = '';

		if (strlen($yealink_ps_enabled) == 0) { $msg .= "Please provide a valid value for power saving enable.<br>\n"; }
		if (strlen($yealink_ps_intel_mode) == 0) { $msg .= "Please provide a valid value for intel mode.<br>\n"; }
		if (strlen($yealink_ps_days_all) == 0) { $msg .= "Please provide a valid value for all days hours.<br>\n"; }
		if (strlen($yealink_ps_days_all_toggle) == 0) { $msg .= "Please provide a valid value for all days toggle.<br>\n"; }
		if (strlen($yealink_ps_days_workweek) == 0) { $msg .= "Please provide a valid value for workweek toggle.<br>\n"; }
		if (strlen($yealink_ps_days_workweek_toggle) == 0) { $msg .= "Please provide a valid value for workweek hours.<br>\n"; }
		if (strlen($yealink_ps_day_monday) == 0) { $msg .= "Please provide a valid value for monday hours.<br>\n"; }
		if (strlen($yealink_ps_day_tuesday) == 0) { $msg .= "Please provide a valid value for tuesday hours.<br>\n"; }
		if (strlen($yealink_ps_day_wednesday) == 0) { $msg .= "Please provide a valid value for wednesday hours.<br>\n"; }
		if (strlen($yealink_ps_day_thursday) == 0) { $msg .= "Please provide a valid value for thursday hours.<br>\n"; }
		if (strlen($yealink_ps_day_friday) == 0) { $msg .= "Please provide a valid value for friday hours.<br>\n"; }
		if (strlen($yealink_ps_day_saturday) == 0) { $msg .= "Please provide a valid value for saturday hours.<br>\n"; }
		if (strlen($yealink_ps_day_sunday) == 0) { $msg .= "Please provide a valid value for sunday hours.<br>\n"; }
		if (strlen($yealink_ps_led_on) == 0) { $msg .= "Please provide a valid value for sunday hours.<br>\n"; }
		if (strlen($yealink_ps_led_off) == 0) { $msg .= "Please provide a valid value for sunday hours.<br>\n"; }
		//need to add new numeric values here
	//Display Error Messages
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persist_form_var.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

	//All Days toggle
		if ($yealink_ps_days_all_toggle == 0){
			$yealink_ps_days_all_toggle = 'false';
		}else{
			$yealink_ps_days_all_toggle = 'true';
		}

	//Workweek toggle
		if ($yealink_ps_days_workweek_toggle == 0){
			$yealink_ps_days_workweek_toggle = 'false';
		}else{
			$yealink_ps_days_workweek_toggle = 'true';
		}

		$sql = "SELECT default_setting_uuid,default_setting_subcategory,default_setting_value,default_setting_enabled FROM v_default_settings ";
		$sql .= "WHERE app_uuid = :app_uuid ";
		$parameters['app_uuid'] = $app_uuid;
		$database = new database;
		$aresults = $database->select($sql, $parameters, 'all');
		unset($sql, $parameters);

	// Set Array
		$i=0;
		foreach ($aresults as $row) {
		$array["default_settings"][$i]["default_setting_uuid"] = $row["default_setting_uuid"];
		$array["default_settings"][$i]["default_setting_value"] = ${$row["default_setting_subcategory"]};
			if ($row["default_setting_subcategory"] == "yealink_ps_days_all" || $row["default_setting_subcategory"] == "yealink_ps_days_workweek") {
				$array["default_settings"][$i]["default_setting_enabled"] = ${$row["default_setting_subcategory"]."_toggle"};
			}
			$i++;
		}

	//grant temporary permissions
		$p = new permissions;
		$p->add('power_saving_update', 'temp');
	//execute update
		$database = new database;
		$database->app_name = 'power_saving';
		$database->app_uuid = $app_uuid;
		$database->save($array);
		$message = $database->message;
		unset($array);
	//revoke temporary permissions
		$p->delete('power_saving_update', 'temp');
	//set message
		message::add($text['message-update']);
	//redirect the browser
	//	header("Location: power_saving.php");
	//	exit;
	}


//load values into variables for form
if ($_POST["persistformvar"] != "true") {
	$sql = "SELECT default_setting_uuid,app_uuid,default_setting_subcategory,default_setting_name,default_setting_value,default_setting_enabled ";
	$sql .= "FROM v_default_settings WHERE app_uuid = :app_uuid ";
	$parameters['app_uuid'] = $app_uuid;
	$database = new database;
	$result = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);
	
	//create variables for forms as tiny arrays
	foreach ($result as $row) {

		${'var_'.$row['default_setting_subcategory']} = $row['default_setting_value'];
	}

//create token
$object = new token;
$token = $object->create($_SERVER['PHP_SELF']);
	
}

//include the header
$document['title'] = $text['title-power_saving'];
require_once "resources/header.php";
echo '<link rel="stylesheet" type="text/css" href="/app/power_saving/nouislider.css">';
echo '<script src="/app/power_saving/nouislider.js"></script>';
echo '<script src="/app/power_saving/wNumb.min.js"></script>';

//show the content
//start the page form
echo "<form method='post' name='frm' id='frm'>";
//start the action bar
echo "<div class='action_bar' id='action_bar'>";
echo "	<div class='heading'>";
echo "<b>".$text['title-power_saving']."</b>";
echo "	</div>\n";
echo "	<div class='actions'>";
//select global or domain
		echo 		"<form name='frm' id='frm' class='inline' method='post'>";
		echo "		".$text['label-domain'];
		echo "		<select class='formfld' name='select_domain' style='margin-right: 20px; margin-top: 4px;'>";
		echo 			"<option value='global'>Global</option>";
		echo 		"</select>";


//load defaults button
echo button::create(['type'=>'button','label'=>$text['button-reset'],'icon'=>$_SESSION['theme']['button_icon_reset'],'style'=>'margin-right: 15px;','onclick'=>'defaults()']);
//reload button
echo button::create(['type'=>'button','label'=>$text['button-refresh'],'icon'=>$_SESSION['theme']['button_icon_refresh'],'style'=>'margin-right: 15px;','link'=>'power_saving.php']);
//save button
echo button::create(['type'=>'button','label'=>$text['button-save'],'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save','collapse'=>'never','onclick'=>'submit_form();']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";

//Start of Body Table
echo "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
";
echo "
<tr>
	<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
		<div class='labelcontainer'>
		</div>
	</td>
	<td width='70%' class='vtable' align='left'>
		<div class='togglecontainer'>
		</div>
		<div class='descriptioncontainer'>
		</div>
	</td>
</tr>
";
echo "
<tr>
	<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
		<div class='labelcontainer'>
			".$text['label-intro']."
		</div>
	</td>
	<td width='70%' class='vtable' align='left'>
		<div class='togglecontainer'>
		</div>
		<div class='descriptioncontainer'>
			".$text['description-intro']."
		</div>
	</td>
</tr>
";
echo "
	<tr>
		<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-enable']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='togglecontainer'>
				<div id='toggle_id_yealink_ps_enabled' class='toggle'></div>
				<input class='formfld' type='hidden' id='tinput_yealink_ps_enabled' name='tinput_yealink_ps_enabled' value='' min='0' max='1' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-enable']."
			</div>
		</td>
	</tr>
	";

echo "
	<tr>
		<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-intel_mode']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='togglecontainer'>
				<div id='toggle_id_yealink_ps_intel_mode' class='toggle'></div>
				<input class='formfld' type='hidden' id='tinput_yealink_ps_intel_mode' name='tinput_yealink_ps_intel_mode' value='' min='0' max='1' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-intel_mode']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-alldays']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_days_all' class='slider'></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_days_all' name='input_yealink_ps_days_all' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-alldays']."
			</div>
			<div class='togglecontainer'>
				<div id='toggle_id_yealink_ps_days_all' class='toggle'></div>
				<input class='formfld' type='hidden' id='tinput_yealink_ps_days_all' name='tinput_yealink_ps_days_all' value='' min='0' max='1' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-toggleall']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' valign='top' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-workweek']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_days_workweek' class='slider'></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_days_workweek' name='input_yealink_ps_days_workweek' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-workweek']."
			</div>
			<div class='togglecontainer'>
				<div id='toggle_id_yealink_ps_days_workweek' class='toggle'></div>
				<input class='formfld' type='hidden' id='tinput_yealink_ps_days_workweek' name='tinput_yealink_ps_days_workweek' value=''  min='0' max='1' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-toggleworkweek']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-monday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_monday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_monday' name='input_yealink_ps_day_monday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-monday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-tuesday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_tuesday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_tuesday' name='input_yealink_ps_day_tuesday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-tuesday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-wednesday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_wednesday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_wednesday' name='input_yealink_ps_day_wednesday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-wednesday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-thursday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_thursday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_thursday' name='input_yealink_ps_day_thursday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-thursday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-friday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_friday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_friday' name='input_yealink_ps_day_friday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-friday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-saturday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_saturday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_saturday' name='input_yealink_ps_day_saturday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-saturday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-sunday']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_day_sunday' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_day_sunday' name='input_yealink_ps_day_sunday' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-sunday']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-idle_timeout']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_idle_timeout' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_idle_timeout' name='input_yealink_ps_idle_timeout' value='' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-idle_timeout']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-hour_timeout']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_hour_timeout' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_hour_timeout' name='input_yealink_ps_hour_timeout' value='' min='1' max='960' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-hour_timeout']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-offhour_timeout']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_offhour_timeout' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_offhour_timeout' name='input_yealink_ps_offhour_timeout' value='' min='1' max='10' maxlength='4'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-offhour_timeout']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-led_on']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_led_on' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_led_on' name='input_yealink_ps_led_on' value='' min='0' max='10000' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-led_on']."
			</div>
		</td>
	</tr>
";

echo "
	<tr>
		<td width='30%' class='vncellreq' align='left' nowrap>
			<div class='labelcontainer'>
				".$text['label-led_off']."
			</div>
		</td>
		<td width='70%' class='vtable' align='left'>
			<div class='slidercontainer'>
				<div id='id_yealink_ps_led_off' class=''></div>
				<input class='formfld' type='hidden' id='input_yealink_ps_led_off' name='input_yealink_ps_led_off' value='' min='0' max='10000' maxlength='6'>
			</div>
			<div class='descriptioncontainer'>
				".$text['description-led_off']."
			</div>
		</td>
	</tr>
";
echo "
	</table>
	<input type='hidden' name='".$token['name']."' value='".$token['hash']."'>
</form>";

//hide password fields before submit
echo "
<script>
	function submit_form() {
		hide_password_fields();
		$('form#frm').submit();
	}
";
//For each rows create sliders/toggles
foreach ($result as $row) {
	//If values are text create slider
	if ($row["default_setting_name"] == "text") {
		//If it's one of the two override values create and override toggle
		if($row['default_setting_subcategory'] == "yealink_ps_days_all" || $row['default_setting_subcategory'] == "yealink_ps_days_workweek"){
			$start = $row['default_setting_enabled'] == "true" ? "1" : "0";
			//Create slider
			echo "
				var slider_".$row['default_setting_subcategory']." = document.getElementById(\"id_".$row['default_setting_subcategory']."\");

				noUiSlider.create(slider_".$row['default_setting_subcategory'].", {
					start: [".$row['default_setting_value']."],
					connect: true,
					margin: 1,
					tooltips: [wNumb({decimals: 0}),wNumb({decimals: 0})],
					step: 1,
					range: {
						\"min\": 0,
						\"max\": 23
					},
					format: wNumb({
						decimals: 0
					})
				});
				// If slider updates
				slider_".$row['default_setting_subcategory'].".noUiSlider.on('update', function (values) {
				input_".$row['default_setting_subcategory'].".value = values;
				});
				
				//Create toggle
				var toggle_".$row['default_setting_subcategory']." = document.getElementById(\"toggle_id_".$row['default_setting_subcategory']."\");

				noUiSlider.create(toggle_".$row['default_setting_subcategory'].", {
					start: ".$start.",
					connect: 'lower',
					margin: 1,
					range: {
						'min': [0, 1],
						'max': 1
					},
					format: wNumb({
						decimals: 0
					})
				});
				
				//If toggle updates
				toggle_".$row['default_setting_subcategory'].".noUiSlider.on('update', function (values) {
					tinput_".$row['default_setting_subcategory'].".value = values;
				});

				";
		// If not a override value create a slider
		}else{
			echo "
				var slider_".$row['default_setting_subcategory']." = document.getElementById(\"id_".$row['default_setting_subcategory']."\");
				noUiSlider.create(slider_".$row['default_setting_subcategory'].", {
					start: [".$row['default_setting_value']."],
					connect: true,
					margin: 1,
					tooltips: [wNumb({decimals: 0}),wNumb({decimals: 0})],
					step: 1,
					range: {
						\"min\": 0,
						\"max\": 23
					},
					format: wNumb({
						decimals: 0
					})
				});

				// If slider updates
				slider_".$row['default_setting_subcategory'].".noUiSlider.on('update', function (values) {
					input_".$row['default_setting_subcategory'].".value = values;
				});
				";
		}

	//If is  numeric value
	}elseif ($row["default_setting_name"] == "numeric") {
		// If is a toggle value
		if($row['default_setting_subcategory'] == "yealink_ps_intel_mode" || $row['default_setting_subcategory'] == "yealink_ps_enabled"){
			//Create toggle
			echo "
			var toggle_".$row['default_setting_subcategory']." = document.getElementById(\"toggle_id_".$row['default_setting_subcategory']."\");

			noUiSlider.create(toggle_".$row['default_setting_subcategory'].", {
				start: ".$row['default_setting_value'].",
				connect: 'lower',
				margin: 1,
				range: {
					'min': [0, 1],
					'max': 1
				},
				format: wNumb({
					decimals: 0
				})
			});
			
			//If toggle updates
			toggle_".$row['default_setting_subcategory'].".noUiSlider.on('update', function (values) {
				tinput_".$row['default_setting_subcategory'].".value = values;
			});
			";
		//If is a numeric value but not toggle value (not 0/1)
		}else{
			echo "
			var sslider_".$row['default_setting_subcategory']." = document.getElementById(\"id_".$row['default_setting_subcategory']."\");

			noUiSlider.create(sslider_".$row['default_setting_subcategory'].", {
				start: ".$row['default_setting_value'].",
				step: 1,
				margin: 1,
				tooltips: [wNumb({decimals: 0})],";
				if($row['default_setting_subcategory'] == 'yealink_ps_hour_timeout'){
					echo "
					range: {
						'min': 2,
						'max': 960
					},
					";
				}elseif($row['default_setting_subcategory'] == 'yealink_ps_offhour_timeout'){
					echo "
					range: {
						'min': 2,
						'max': 10
					},
					";
				}elseif($row['default_setting_subcategory'] == 'yealink_ps_idle_timeout'){
					echo "
					range: {
						'min': 2,
						'max': 30
					},
					";
				}elseif($row['default_setting_subcategory'] == 'yealink_ps_led_on' || $row['default_setting_subcategory'] == 'yealink_ps_led_off'){
					echo "
					range: {
						'min': 100,
						'max': 10000
					},
					";

				}
			echo "
				format: wNumb({
				decimals: 0
				})
			});

			// If slider updates
			sslider_".$row['default_setting_subcategory'].".noUiSlider.on('update', function (values) {
			input_".$row['default_setting_subcategory'].".value = values;
		});
			";
		}

	}
}
//Disable/Enable sliders if one of the overrides is selected.
echo "
toggle_yealink_ps_days_all.noUiSlider.on('update', function (values, handle) {
	if (values[handle] === '1') {
		id_yealink_ps_days_workweek.setAttribute('disabled', true);
		toggle_id_yealink_ps_days_workweek.noUiSlider.set(0);
		toggle_id_yealink_ps_days_workweek.setAttribute('disabled', true);
		id_yealink_ps_day_monday.setAttribute('disabled', true);
		id_yealink_ps_day_tuesday.setAttribute('disabled', true);
		id_yealink_ps_day_wednesday.setAttribute('disabled', true);
		id_yealink_ps_day_thursday.setAttribute('disabled', true);
		id_yealink_ps_day_friday.setAttribute('disabled', true);
		id_yealink_ps_day_saturday.setAttribute('disabled', true);
		id_yealink_ps_day_sunday.setAttribute('disabled', true);
	} else {
		id_yealink_ps_days_workweek.removeAttribute('disabled');
		toggle_id_yealink_ps_days_workweek.removeAttribute('disabled');
		id_yealink_ps_day_monday.removeAttribute('disabled');
		id_yealink_ps_day_tuesday.removeAttribute('disabled');
		id_yealink_ps_day_wednesday.removeAttribute('disabled');
		id_yealink_ps_day_thursday.removeAttribute('disabled');
		id_yealink_ps_day_friday.removeAttribute('disabled');
		id_yealink_ps_day_saturday.removeAttribute('disabled');
		id_yealink_ps_day_sunday.removeAttribute('disabled');
	}
});
toggle_yealink_ps_days_workweek.noUiSlider.on('update', function (values, handle) {
	if (values[handle] === '1') {
		id_yealink_ps_day_monday.setAttribute('disabled', true);
		id_yealink_ps_day_tuesday.setAttribute('disabled', true);
		id_yealink_ps_day_wednesday.setAttribute('disabled', true);
		id_yealink_ps_day_thursday.setAttribute('disabled', true);
		id_yealink_ps_day_friday.setAttribute('disabled', true);
		id_yealink_ps_day_saturday.setAttribute('disabled', true);
		id_yealink_ps_day_sunday.setAttribute('disabled', true);
	} else {
		id_yealink_ps_day_monday.removeAttribute('disabled');
		id_yealink_ps_day_tuesday.removeAttribute('disabled');
		id_yealink_ps_day_wednesday.removeAttribute('disabled');
		id_yealink_ps_day_thursday.removeAttribute('disabled');
		id_yealink_ps_day_friday.removeAttribute('disabled');
		id_yealink_ps_day_saturday.removeAttribute('disabled');
		id_yealink_ps_day_sunday.removeAttribute('disabled');
	}
});
";
//Set defaults if the defaults button is pushed.
echo "
	function defaults() {
		if(confirm(\"Are you sure you want to restore defaults?\")){
			toggle_id_yealink_ps_enabled.noUiSlider.set(1);
			toggle_id_yealink_ps_intel_mode.noUiSlider.set(0);
			toggle_id_yealink_ps_days_all.noUiSlider.set(0);
			toggle_id_yealink_ps_days_workweek.noUiSlider.set(0);
			id_yealink_ps_days_all.noUiSlider.set([8,18]);
			id_yealink_ps_days_workweek.noUiSlider.set([8,18]);
			id_yealink_ps_day_monday.noUiSlider.set([8,18]);
			id_yealink_ps_day_tuesday.noUiSlider.set([8,18]);
			id_yealink_ps_day_wednesday.noUiSlider.set([8,18]);
			id_yealink_ps_day_thursday.noUiSlider.set([8,18]);
			id_yealink_ps_day_friday.noUiSlider.set([8,18]);
			id_yealink_ps_day_saturday.noUiSlider.set([8,18]);
			id_yealink_ps_day_sunday.noUiSlider.set([8,18]);
			id_yealink_ps_idle_timeout.noUiSlider.set(10);
			id_yealink_ps_hour_timeout.noUiSlider.set(960);
			id_yealink_ps_offhour_timeout.noUiSlider.set(10);
			id_yealink_ps_led_on.noUiSlider.set(500);
			id_yealink_ps_led_off.noUiSlider.set(3000);
		}
	};
";
echo "</script>";

//include the footer
	require_once "resources/footer.php";
?>