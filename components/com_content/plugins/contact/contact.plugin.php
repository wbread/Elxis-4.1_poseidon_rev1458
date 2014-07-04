<?php 
/**
* @version		$Id: contact.plugin.php 1399 2013-03-05 17:59:27Z datahell $
* @package		Elxis
* @subpackage	Content Plugins / Contact
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contactPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
    	$regex = "#{contact}(.*?){/contact}#s";
    	if (!$published) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$eURI = eFactory::getURI();
    	$proceed = false;
    	if ($eURI->getComponent() == 'content') {
    		if (!$eURI->isDir()) {
    			$proceed = true;
				$action = $eURI->getRealUriString();
				$parts = preg_split('#\?#', $action, 2);
				$action = $parts[0];
				unset($parts);
			}
   		}

		if (!$proceed) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
		}
		unset($proceed);

		$cfg = array();
		$stdfields = array('phone', 'mobile', 'address', 'city', 'postalcode', 'country', 'website');
		foreach ($stdfields as $stdfield) {
			$rfield = 'req_'.$stdfield;
			$cfg[$stdfield] = (int)$params->get($stdfield, 0);
			$cfg[$rfield] = (int)$params->get($rfield, 0);
		}
		unset($stdfields);

		$cfg['field1'] = eUTF::trim($params->getML('field1', ''));
		$cfg['req_field1'] = (int)$params->get('req_field1', 0);
		$cfg['field2'] = eUTF::trim($params->getML('field2', ''));
		$cfg['req_field2'] = (int)$params->get('req_field2', 0);
		$cfg['field3'] = eUTF::trim($params->getML('field3', ''));
		$cfg['req_field3'] = (int)$params->get('req_field3', 0);

		$proc = false;
		foreach ($matches[0] as $i => $match) {//only the first match will be processed!
			if ($proc == true) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}
			$rcptmail = $matches[1][$i];
			if (!filter_var($rcptmail, FILTER_VALIDATE_EMAIL)) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}

			$response = '';
			if (isset($_POST['sbmcontact'])) {
				$response = $this->processRequest($cfg, $rcptmail, $row->title, $action);
			}

			$html = $this->makeForm($row->id, $cfg, $action);
			$html = $response.$html;
			$row->text = preg_replace("#".$match."#", $html, $row->text);
			$proc = true;
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{contact}recipient_email_address{/contact}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		return array();
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		if ($tabidx <> 1) { return; }
		
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$defmail = $elxis->getConfig('MAIL_EMAIL');

		echo '<table border="0" class="plug_table" dir="'.$eLang->getinfo('DIR').'">'."\n";
		echo '<tr><td class="plug_td150">'.$eLang->get('RCPT_EMAIL')."</td>\n";
		echo '<td><input type="text" name="contact_email" id="contact_email" class="inputbox" size="40" dir="ltr" value="'.$defmail.'" />'."\n";
		echo '&#160; <a href="javascript:void(null);" onclick="addContactCode()" title="'.$eLang->get('ADD').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/contact/includes/link.png" alt="link" border="0" /></a></td></tr>'."\n";
		echo "</table>\n";
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/contact/includes/contact.js'),
			'css' => array()
		);

		return $response;
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/***********************/
	/* MAKE FORM HTML CODE */
	/***********************/
	private function makeForm($id, $cfg, $action) {
		$eLang = eFactory::getLang();

		$pfx = 'art'.$id;
		$v1 = rand(4, 30);
		$v2 = rand(3, 29);
		if ($v1 % 2) {
			$operator = '+';
			$number1 = $v1;
			$number2 = $v2;
			$sum = $number1 + $number2;
		} else {
			$operator = '-';
			if ($v1 == $v2) {
				$number1 = $v1 + rand(6, 21);
				$number2 = $v2;
			} else if ($v1 > $v2) {
				if (($v1 - $v2) < 6) { $v1 = $v1 + rand(5, 20); }
				$number1 = $v1;
				$number2 = $v2;
			} else {
				$number1 = $v1 + rand(5, 20);
				$number2 = $v1;
			}
			$sum = $number1 - $number2;
		}
		eFactory::getSession()->set('captcha_conseccode', $sum);
		unset($v1, $v2, $sum);

		if ($eLang->getinfo('DIR') == 'rtl') {
			$align = 'right';
			$dir = 'rtl';
		} else {
			$align = 'left';
			$dir = 'ltr';
		}

		$label_style = ' style="width:200px; text-align:'.$align.';"';
		if (ELXIS_MOBILE == 1) { $label_style = ''; }

		$js = 'function elxformvalcontactform() {'."\n";
		$js .= "\t".'if (document.getElementById(\''.$pfx.'firstname\').value == \'\') {'."\n";
		$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('FIRSTNAME'));
		$js .= "\t\t".'alert(\''.$ltext.'\'); elxFocus(\''.$pfx.'firstname\'); return false;'."\n";
		$js .= "\t}\n";
		$js .= "\t".'else if (document.getElementById(\''.$pfx.'lastname\').value == \'\') {'."\n";
		$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('LASTNAME'));
		$js .= "\t\t".'alert(\''.$ltext.'\'); elxFocus(\''.$pfx.'lastname\'); return false;'."\n";
		$js .= "\t}\n";

		$out = '<form name="contactform" class="elx_form" method="post" action="'.$action.'" enctype="application/x-www-form-urlencoded" onsubmit="return elxformvalcontactform();">'."\n";
		$out .= '<fieldset class="elx_form_fieldset">'."\n";
		$out .= '<legend class="elx_form_legend">'.$eLang->get('CONTACT')."</legend>\n";
		$out .= '<div class="elx_form_row">'."\n";
		$out .= '<label for="'.$pfx.'firstname" class="elx_form_label"'.$label_style.'>'.$eLang->get('FIRSTNAME')."*</label>\n";
		$out .= '<input type="text" name="firstname" id="'.$pfx.'firstname" value="" title="'.$eLang->get('FIRSTNAME').'" maxlength="60" class="inputbox" dir="'.$dir.'" />'."\n";
		$out .= "</div>\n";
		$out .= '<div class="elx_form_row">'."\n";
		$out .= '<label for="'.$pfx.'lastname" class="elx_form_label"'.$label_style.'>'.$eLang->get('LASTNAME')."*</label>\n";
		$out .= '<input type="text" name="lastname" id="'.$pfx.'lastname" value="" title="'.$eLang->get('LASTNAME').'" maxlength="60" class="inputbox" dir="'.$dir.'" />'."\n";
		$out .= "</div>\n";
		if ($cfg['address'] == 1) {
			$reqmark = '';
			if ($cfg['req_address'] == 1) {
				$js .= "\t".'else if (document.getElementById(\''.$pfx.'address\').value == \'\') {'."\n";
				$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ADDRESS'));
				$js .= "\t\t".'alert(\''.$ltext.'\'); elxFocus(\''.$pfx.'address\'); return false;'."\n";
				$js .= "\t}\n";
				$reqmark = '*';
			}
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'address" class="elx_form_label"'.$label_style.'>'.$eLang->get('ADDRESS').$reqmark."</label>\n";
			$out .= '<input type="text" name="address" id="'.$pfx.'address" value="" title="'.$eLang->get('ADDRESS').'" size="35" class="inputbox" maxlength="120" dir="'.$dir.'" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['city'] == 1) {
			$reqmark = '';
			if ($cfg['req_city'] == 1) {
				$js .= "\t".'else if (document.getElementById(\''.$pfx.'city\').value == \'\') {'."\n";
				$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CITY'));
				$js .= "\t\t".'alert(\''.$ltext.'\'); elxFocus(\''.$pfx.'city\'); return false;'."\n";
				$js .= "\t}\n";
				$reqmark = '*';
			}
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'city" class="elx_form_label"'.$label_style.'>'.$eLang->get('CITY').$reqmark."</label>\n";
			$out .= '<input type="text" name="city" id="'.$pfx.'city" value="" title="'.$eLang->get('CITY').'" class="inputbox" dir="'.$dir.'" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['postalcode'] == 1) {
			$reqmark = '';
			if ($cfg['req_postalcode'] == 1) {
				$reqmark = '*';
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'postalcode\', false)) {'."\n";
			} else {
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'postalcode\', true)) {'."\n";
			}
			$js .= "\t\t".'alert(\''.$eLang->get('INVALID_NUMBER').'\'); elxFocus(\''.$pfx.'postalcode\'); return false;'."\n";
			$js .= "\t}\n";

			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'postalcode" class="elx_form_label"'.$label_style.'>'.$eLang->get('POSTAL_CODE').$reqmark."</label>\n";
			$out .= '<input type="text" name="postalcode" id="'.$pfx.'postalcode" value="" title="'.$eLang->get('POSTAL_CODE').'" size="10" maxlength="10" class="inputbox" dir="ltr" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['country'] == 1) {
			$reqmark = '';
			if ($cfg['req_country'] == 1) { $reqmark = '*'; }

			$lng = $eLang->getinfo('LANGUAGE');
			$reg = $eLang->getinfo('REGION');
			if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
				$ldir = $dir;
				include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
			} else {
				$ldir = 'ltr';
				include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
			}

			$sel = (isset($countries[$reg])) ? $reg : 'US';
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'country" class="elx_form_label"'.$label_style.'>'.$eLang->get('COUNTRY').$reqmark."</label>\n";
			$out .= '<select name="country" id="'.$pfx.'country" title="'.$eLang->get('COUNTRY').'" class="selectbox" dir="'.$ldir.'">'."\n";
			foreach ($countries as $key => $name) {
				$extra = ($sel == $key) ? ' selected="selected"' : '';
				$out .= '<option value="'.$key.'"'.$extra.'>'.$name.'</option>'."\n";
			}
			$out .= "</select>\n</div>\n";
		}

		if ($cfg['phone'] == 1) {
			$reqmark = '';
			if ($cfg['req_phone'] == 1) {
				$reqmark = '*';
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'phone\', false)) {'."\n";
			} else {
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'phone\', true)) {'."\n";
			}
			$js .= "\t\t".'alert(\''.$eLang->get('INVALID_NUMBER').'\'); elxFocus(\''.$pfx.'phone\'); return false;'."\n";
			$js .= "\t}\n";

			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'phone" class="elx_form_label"'.$label_style.'>'.$eLang->get('TELEPHONE').$reqmark."</label>\n";
			$out .= '<input type="text" name="phone" id="'.$pfx.'phone" value="" title="'.$eLang->get('TELEPHONE').'"  maxlength="40" class="inputbox" dir="ltr" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['mobile'] == 1) {
			$reqmark = '';
			if ($cfg['req_mobile'] == 1) {
				$reqmark = '*';
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'mobile\', false)) {'."\n";
			} else {
				$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'mobile\', true)) {'."\n";
			}
			$js .= "\t\t".'alert(\''.$eLang->get('INVALID_NUMBER').'\'); elxFocus(\''.$pfx.'mobile\'); return false;'."\n";
			$js .= "\t}\n";

			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'mobile" class="elx_form_label"'.$label_style.'>'.$eLang->get('MOBILE').$reqmark."</label>\n";
			$out .= '<input type="text" name="mobile" id="'.$pfx.'mobile" value="" title="'.$eLang->get('MOBILE').'"  maxlength="40" class="inputbox" dir="ltr" />'."\n";
			$out .= "</div>\n";
		}

		$js .= "\t".'else if (!elxValidateEmailBox(\''.$pfx.'email\', false)) {'."\n";
		$js .= "\t\t".'alert(\''.$eLang->get('INVALIDEMAIL').'\'); elxFocus(\''.$pfx.'email\'); return false;'."\n";
		$js .= "\t}\n";

		$out .= '<div class="elx_form_row">'."\n";
		$out .= '<label for="'.$pfx.'email" class="elx_form_label"'.$label_style.'>'.$eLang->get('EMAIL')."*</label>\n";
		$out .= '<input type="text" name="email" id="'.$pfx.'email" value="" title="'.$eLang->get('EMAIL').'" size="30" class="inputbox" dir="ltr" />'."\n";
		$out .= "</div>\n";

		if ($cfg['website'] == 1) {
			$reqmark = '';
			if ($cfg['req_website'] == 1) {
				$reqmark = '*';
				$js .= "\t".'else if (!elxValidateURLBox(\''.$pfx.'website\', false)) {'."\n";
			} else {
				$js .= "\t".'else if (!elxValidateURLBox(\''.$pfx.'website\', true)) {'."\n";
			}
			$js .= "\t\t".'alert(\''.$eLang->get('INVALID_URL_ADDR').'\'); elxFocus(\''.$pfx.'website\'); return false;'."\n";
			$js .= "\t}\n";

			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'website" class="elx_form_label"'.$label_style.'>'.$eLang->get('WEBSITE').$reqmark."</label>\n";
			$out .= '<input type="text" name="website" id="'.$pfx.'website" value="" title="'.$eLang->get('WEBSITE').'" size="35" maxlength="120" class="inputbox" dir="ltr" />'."\n";
			$out .= "</div>\n";
		}

		if ($cfg['field1'] != '') {
			$reqmark = '';
			if ($cfg['req_field1'] == 1) {
				$js .= "\t".'else if (document.getElementById(\''.$pfx.'field1\').value == \'\') {'."\n";
				$js .= "\t\t".'alert(\''.$eLang->get('REQFIELDEMPTY').'\'); elxFocus(\''.$pfx.'field1\'); return false;'."\n";
				$js .= "\t}\n";
				$reqmark = '*';
			}
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'field1" class="elx_form_label"'.$label_style.'>'.$cfg['field1'].$reqmark."</label>\n";
			$out .= '<input type="text" name="field1" id="'.$pfx.'field1" value="" class="inputbox" dir="'.$dir.'" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['field2'] != '') {
			$reqmark = '';
			if ($cfg['req_field2'] == 1) {
				$js .= "\t".'else if (document.getElementById(\''.$pfx.'field2\').value == \'\') {'."\n";
				$js .= "\t\t".'alert(\''.$eLang->get('REQFIELDEMPTY').'\'); elxFocus(\''.$pfx.'field2\'); return false;'."\n";
				$js .= "\t}\n";
				$reqmark = '*';
			}
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'field2" class="elx_form_label"'.$label_style.'>'.$cfg['field2'].$reqmark."</label>\n";
			$out .= '<input type="text" name="field2" id="'.$pfx.'field2" value="" class="inputbox" dir="'.$dir.'" />'."\n";
			$out .= "</div>\n";
		}
		if ($cfg['field3'] != '') {
			$reqmark = '';
			if ($cfg['req_field3'] == 1) {
				$js .= "\t".'else if (document.getElementById(\''.$pfx.'field3\').value == \'\') {'."\n";
				$js .= "\t\t".'alert(\''.$eLang->get('REQFIELDEMPTY').'\'); elxFocus(\''.$pfx.'field3\'); return false;'."\n";
				$js .= "\t}\n";
				$reqmark = '*';
			}
			$out .= '<div class="elx_form_row">'."\n";
			$out .= '<label for="'.$pfx.'field3" class="elx_form_label"'.$label_style.'>'.$cfg['field3'].$reqmark."</label>\n";
			$out .= '<input type="text" name="field3" id="'.$pfx.'field3" value="" class="inputbox" dir="'.$dir.'" />'."\n";
			$out .= "</div>\n";
		}

		$js .= "\t".'else if (document.getElementById(\''.$pfx.'message\').value == \'\') {'."\n";
		$js .= "\t\t".'alert(\''.$eLang->get('MUST_WRITE_MESSAGE').'\'); elxFocus(\''.$pfx.'message\'); return false;'."\n";
		$js .= "\t}\n";
		$js .= "\t".'else if (!elxValidateNumericBox(\''.$pfx.'conseccode\', false)) {'."\n";
		$js .= "\t\t".'alert(\''.$eLang->get('INVALID_NUMBER').'\'); elxFocus(\''.$pfx.'conseccode\'); return false;'."\n";
		$js .= "\t}\n";
		$js .= "\t else { return true; }\n";
		$js .= "}\n";

		eFactory::getDocument()->addScript($js);

		$out .= '<div class="elx_form_row">'."\n";
		$out .= '<label for="'.$pfx.'message" class="elx_form_label"'.$label_style.'>'.$eLang->get('MESSAGE')."*</label>\n";
		$out .= '<textarea name="message" id="'.$pfx.'message" rows="4" cols="40" class="textbox" dir="'.$dir.'">'."</textarea>\n";
		$out .= "</div>\n";

		$out .= '<div class="elx_form_row">'."\n";
		$out .= '<label for="'.$pfx.'conseccode" class="elx_form_label"'.$label_style.'>'.$eLang->get('SECURITY_CODE')."*</label>\n";
		$out .= '<span dir="ltr">'.$number1.' '.$operator.' '.$number2.' =</span>'."\n";
		$out .= '<input type="text" name="conseccode" id="'.$pfx.'conseccode" value="" title="'.$eLang->get('SECURITY_CODE').'" size="5" maxlength="5" class="inputbox" dir="ltr" />'."\n";
		$out .= "</div>\n";
		$out .= '<div class="elx_form_row">'."\n";
		if (ELXIS_MOBILE == 1) {
			$out .= '<div class="elx_form_field_box">'."\n";
			$out .= '<button type="submit" name="sbmcontact" id="'.$pfx.'sbmcontact" title="'.$eLang->get('SUBMIT').'" class="elxbutton-save" dir="ltr">'.$eLang->get('SUBMIT').'</button><br />'."\n";
			$out .= '<span class="elx_form_tip">'.$eLang->get('FIELDSASTERREQ')."</span>\n</div>\n";
		} else {
			$out .= '<div class="elx_form_nolabel" style="width:200px;">&#160;</div>'."\n";
			$out .= '<div class="elx_form_field_box" style="margin-'.$align.':200px;">'."\n";
			$out .= '<button type="submit" name="sbmcontact" id="'.$pfx.'sbmcontact" title="'.$eLang->get('SUBMIT').'" class="elxbutton-save" dir="ltr">'.$eLang->get('SUBMIT').'</button><br />'."\n";
			$out .= '<span class="elx_form_tip">'.$eLang->get('FIELDSASTERREQ')."</span>\n</div>\n";
			$out .= '<div style="clear:both;"></div>'."\n";
		}
		$out .= "</div>\n";
		$out .= "</fieldset>\n";
		$out .= "</form>\n";

		return $out;
	}


	/***************************/
	/* PROCESS FORM SUBMISSION */
	/***************************/
	private function processRequest($cfg, $rcptemail, $pagetitle, $pageurl) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$sess_captcha = trim(eFactory::getSession()->get('captcha_conseccode'));
		$seccode = trim(filter_input(INPUT_POST, 'conseccode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($sess_captcha == '') || ($seccode == '') || ($seccode != $sess_captcha)) {
			return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('INVALIDSECCODE')."</div>\n";
		}

		$text = '';
		$firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($firstname) == '') {
			$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('FIRSTNAME'));
			return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
		}
		$text .= $eLang->get('FIRSTNAME').": \t\t\t".$firstname."\n";
		$lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($lastname) == '') {
			$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('LASTNAME'));
			return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
		}
		$text .= $eLang->get('LASTNAME').": \t\t\t".$lastname."\n";

		if ($cfg['address'] == 1) {
			$val = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_address'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ADDRESS'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			$text .= $eLang->get('ADDRESS').": \t\t\t".$val."\n";
		}

		if ($cfg['city'] == 1) {
			$val = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_city'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CITY'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			$text .= $eLang->get('CITY').": \t\t\t".$val."\n";
		}

		if ($cfg['postalcode'] == 1) {
			$val = isset($_POST['postalcode']) ? (int)$_POST['postalcode'] : 0;
			if ($cfg['req_postalcode'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('POSTAL_CODE'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			$text .= $eLang->get('POSTAL_CODE').": \t\t\t".$val."\n";
		}

		if ($cfg['country'] == 1) {
			$val = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($val != '') {
				$lng = $eLang->getinfo('LANGUAGE');
				if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
				} else {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
				}
				if (isset($countries[$val])) {
					$val = $countries[$val].' ('.$val.')';
				} else {
					$val = '';
				}
				unset($countries);
			}

			if ($cfg['req_country'] == 1) {
				if ($val == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('COUNTRY'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}

			$text .= $eLang->get('COUNTRY').": \t\t\t".$val."\n";
		}

		if ($cfg['phone'] == 1) {
			$val = isset($_POST['phone']) ? (int)$_POST['phone'] : 0;
			if ($cfg['req_phone'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TELEPHONE'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			$text .= $eLang->get('TELEPHONE').": \t\t\t".$val."\n";
		}

		if ($cfg['mobile'] == 1) {
			$val = isset($_POST['mobile']) ? (int)$_POST['mobile'] : 0;
			if ($cfg['req_mobile'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MOBILE'));
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			$text .= $eLang->get('MOBILE').": \t\t\t".$val."\n";
		}

		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		if (($email == '') || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('INVALIDEMAIL')."</div>\n";
		}
		$text .= $eLang->get('EMAIL').": \t\t\t".$email."\n";

		if ($cfg['website'] == 1) {
			$val = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
			if (!filter_var($val, FILTER_VALIDATE_URL)) { $val = ''; }
			if ($cfg['req_website'] == 1) {
				if ($val == '') {
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('INVALID_URL_ADDR')."</div>\n";
				}
			}
			$text .= $eLang->get('WEBSITE').": \t\t\t".$val."\n";
		}

		if ($cfg['field1'] != '') {
			$val = filter_input(INPUT_POST, 'field1', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_field1'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $cfg['field1']);
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			$text .= $cfg['field1'].": \t\t\t".$val."\n";
		}

		if ($cfg['field2'] != '') {
			$val = filter_input(INPUT_POST, 'field2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_field2'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $cfg['field2']);
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			$text .= $cfg['field2'].": \t\t\t".$val."\n";
		}

		if ($cfg['field3'] != '') {
			$val = filter_input(INPUT_POST, 'field3', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_field3'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $cfg['field3']);
					return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			$text .= $cfg['field3'].": \t\t\t".$val."\n";
		}

		$val = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($val) == '') {
			return '<div class="elx_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('MUST_WRITE_MESSAGE')."</div>\n";
		}

		$ipaddr = $elxis->obj('ip')->clientIP(false, false);

		$text .= $eLang->get('IP_ADDRESS').": \t\t\t".$ipaddr."\n";
		$text .= $eLang->get('MESSAGE').":\n";
		$text .= $val."\n";

		$subject = $eLang->get('CONTACT_INQ');

		$body = $eLang->get('HI')."\n";
		$body .= $eLang->get('CFORM_SUBMIT')."\n";
		$body .= $eLang->get('INFO_FOLLOW')."\n\n";
		$body .= $eLang->get('PAGE').': '.$pagetitle."\n";
		$body .= $pageurl."\n\n\n";
		$body .= $text."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= 'Powered by Elxis v'.$elxis->getVersion().' '.$elxis->fromVersion('CODENAME');

		$from = $email.','.$firstname.' '.$lastname;
		$ok = $elxis->sendmail($subject, $body, '', null, 'plain', $rcptemail, null, null, $from);
		if ($ok) {
			return '<div class="elx_success">'.$eLang->get('MSG_SENT_REPLY_THANKS').'</div>';
		} else {
			return '<div class="elx_error">'.$eLang->get('SORRY_SEND_FAILED').'</div>';
		}
	}

}

?>