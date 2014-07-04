<?php 
/**
* @version		4.0
* @package		Elxis CMS
* @author		Elxis Team ( http://www.elxis.org )
* @copyright	(c) 2006-2012 Is Open Source (http://www.isopensource.com). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
== Elxis languages database ==

Each entry is an array having as key the shortest possible language subtag based on the BCP47 standard
(IANA language subtag registry: http://www.iana.org/assignments/language-subtag-registry )

SUBTAG => array (
	* LANGUAGE => {2*3ALPHA} shortest ISO 639 code (ISO 639-1 OR 639-2)
	* SCRIPT => {4ALPHA} ISO 15924 code
	* REGION => {2ALPHA} ISO 3166-1 code =OR= {3DIGIT} UN M.49 code
	* NAME => Native name of language subtag
	* NAME_ENG => Language's name in English (en-GB)
)

== ONLINE SOURCES ==
IANA LANGUAGE SUBTAG REGISTRY: 	http://www.iana.org/assignments/language-subtag-registry
RFC5646: 						http://tools.ietf.org/html/rfc5646
ISO 639-1 and -2: 				http://www.w3.org/WAI/ER/IG/ert/iso639.htm
ISO 639-1 Wikipedia:            http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
ISO 3166-1 Wikipedia:           http://en.wikipedia.org/wiki/ISO_3166-1
ISO 15924:						http://unicode.org/iso15924/iso15924-codes.html
Native languages names:			http://www.omniglot.com/language/names.htm
Country codes:                  http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$langdb = array (
	'aa' => array('LANGUAGE' => 'aa', 'REGION' => 'ET', 'DIR' => 'ltr', 'NAME' => 'ʿAfár', 'NAME_ENG' => 'Afar'),
	'ab' => array('LANGUAGE' => 'ab', 'REGION' => 'GE', 'DIR' => 'ltr', 'NAME' => 'Аҧсуа', 'NAME_ENG' => 'Abkhaz'),
	'ac' => array('LANGUAGE' => 'ar', 'REGION' => 'MA', 'DIR' => 'rtl', 'NAME' => 'العربية MA', 'NAME_ENG' => 'Arabic (Morocco)'),
	'af' => array('LANGUAGE' => 'af', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'ʿAfrikaans', 'NAME_ENG' => 'Afrikaans'), 
	'ah' => array('LANGUAGE' => 'ar', 'REGION' => 'BH', 'DIR' => 'rtl', 'NAME' => 'العربية BH', 'NAME_ENG' => 'Arabic (Bahrain)'), 
	'aj' => array('LANGUAGE' => 'ar', 'REGION' => 'JO', 'DIR' => 'rtl', 'NAME' => 'العربية JO', 'NAME_ENG' => 'Arabic (Jordan)'), 
	'al' => array('LANGUAGE' => 'ar', 'REGION' => 'LB', 'DIR' => 'rtl', 'NAME' => 'العربية LB', 'NAME_ENG' => 'Arabic (Lebanon)'),
	'am' => array('LANGUAGE' => 'am', 'REGION' => 'ET', 'DIR' => 'ltr', 'NAME' => 'ኣማርኛ', 'NAME_ENG' => 'Amharic'),
	'ap' => array('LANGUAGE' => 'ar', 'REGION' => 'EG', 'DIR' => 'rtl', 'NAME' => 'العربية EG', 'NAME_ENG' => 'Arabic (Egypt)'), 
	'aq' => array('LANGUAGE' => 'ar', 'REGION' => 'QA', 'DIR' => 'rtl', 'NAME' => 'العربية QA', 'NAME_ENG' => 'Arabic (Qatar)'), 
	'ar' => array('LANGUAGE' => 'ar', 'REGION' => 'AE', 'DIR' => 'rtl', 'NAME' => 'العربية AE', 'NAME_ENG' => 'Arabic'),
	'at' => array('LANGUAGE' => 'ar', 'REGION' => 'TN', 'DIR' => 'rtl', 'NAME' => 'العربية TN', 'NAME_ENG' => 'Arabic (Tunisia)'), 
	'au' => array('LANGUAGE' => 'en', 'REGION' => 'AU', 'DIR' => 'ltr', 'NAME' => 'English AU', 'NAME_ENG' => 'English (Australia)'),
	'aw' => array('LANGUAGE' => 'ar', 'REGION' => 'KW', 'DIR' => 'rtl', 'NAME' => 'العربية KW', 'NAME_ENG' => 'Arabic (Kuwait)'),
	'az' => array('LANGUAGE' => 'az', 'REGION' => 'AZ', 'DIR' => 'ltr', 'NAME' => 'Азәрбајҹан', 'NAME_ENG' => 'Azeri'),
	'be' => array('LANGUAGE' => 'be', 'REGION' => 'BY', 'DIR' => 'ltr', 'NAME' => 'Беларуская', 'NAME_ENG' => 'Belarusian'),
	'bg' => array('LANGUAGE' => 'bg', 'REGION' => 'BG', 'DIR' => 'ltr', 'NAME' => 'Български', 'NAME_ENG' => 'Bulgarian'),
	'bn' => array('LANGUAGE' => 'bn', 'REGION' => 'BD', 'DIR' => 'ltr', 'NAME' => 'বাংলা', 'NAME_ENG' => 'Bengali Bangla'),
	'bo' => array('LANGUAGE' => 'dz', 'REGION' => 'CN', 'DIR' => 'ltr', 'NAME' => 'བོད་སྐད་', 'NAME_ENG' => 'Tibetan'),
	'bs' => array('LANGUAGE' => 'bs', 'REGION' => 'BA', 'DIR' => 'ltr', 'NAME' => 'Bosanski', 'NAME_ENG' => 'Bosnian'),
	'br' => array('LANGUAGE' => 'br', 'REGION' => 'FR', 'DIR' => 'ltr', 'NAME' => 'Brezhoneg', 'NAME_ENG' => 'Breton'),
	'ca' => array('LANGUAGE' => 'ca', 'REGION' => 'AD', 'DIR' => 'ltr', 'NAME' => 'català', 'NAME_ENG' => 'Catalan'),
	'cm' => array('LANGUAGE' => 'cy', 'REGION' => 'GB', 'DIR' => 'ltr', 'NAME' => 'Cymraeg', 'NAME_ENG' => 'Welsh'),
	'cs' => array('LANGUAGE' => 'cs', 'REGION' => 'CZ', 'DIR' => 'ltr', 'NAME' => 'čeština', 'NAME_ENG' => 'Czech'),
	'cy' => array('LANGUAGE' => 'el', 'REGION' => 'CY', 'DIR' => 'ltr', 'NAME' => 'Ελληνικά (Κύπρος)', 'NAME_ENG' => 'Greek Cyprus'),
	'da' => array('LANGUAGE' => 'da', 'REGION' => 'DK', 'DIR' => 'ltr', 'NAME' => 'Dansk', 'NAME_ENG' => 'Danish'),
	'de' => array('LANGUAGE' => 'de', 'REGION' => 'DE', 'DIR' => 'ltr', 'NAME' => 'Deutsch', 'NAME_ENG' => 'German'),
	'dz' => array('LANGUAGE' => 'dz', 'REGION' => 'BT', 'DIR' => 'ltr', 'NAME' => 'རྫོང་ཁ', 'NAME_ENG' => 'Dzongkha'),
	'ec' => array('LANGUAGE' => 'es', 'REGION' => 'CL', 'DIR' => 'ltr', 'NAME' => 'Español CL', 'NAME_ENG' => 'Spanish Chile'),
	'ea' => array('LANGUAGE' => 'el', 'REGION' => 'GR', 'DIR' => 'ltr', 'NAME' => 'Αρχαία Ελληνικά', 'NAME_ENG' => 'Greek Ancient'),
	'el' => array('LANGUAGE' => 'el', 'REGION' => 'GR', 'DIR' => 'ltr', 'NAME' => 'Ελληνικά', 'NAME_ENG' => 'Greek'),
	'em' => array('LANGUAGE' => 'es', 'REGION' => 'CO', 'DIR' => 'ltr', 'NAME' => 'Español CO', 'NAME_ENG' => 'Spanish Colombia'),
	'en' => array('LANGUAGE' => 'en', 'REGION' => 'GB', 'DIR' => 'ltr', 'NAME' => 'English', 'NAME_ENG' => 'English'),
	'ep' => array('LANGUAGE' => 'ep', 'REGION' => 'UC', 'DIR' => 'ltr', 'NAME' => 'Esperanto', 'NAME_ENG' => 'Esperanto'),
	'er' => array('LANGUAGE' => 'es', 'REGION' => 'AR', 'DIR' => 'ltr', 'NAME' => 'Español AR', 'NAME_ENG' => 'Spanish Argentina'),
	'es' => array('LANGUAGE' => 'es', 'REGION' => 'ES', 'DIR' => 'ltr', 'NAME' => 'Español', 'NAME_ENG' => 'Spanish'),
	'et' => array('LANGUAGE' => 'et', 'REGION' => 'EE', 'DIR' => 'ltr', 'NAME' => 'Eesti', 'NAME_ENG' => 'Estonian'),
	'eu' => array('LANGUAGE' => 'eu', 'REGION' => 'ES', 'DIR' => 'ltr', 'NAME' => 'Euskara', 'NAME_ENG' => 'Basque'),
	'ev' => array('LANGUAGE' => 'es', 'REGION' => 'VE', 'DIR' => 'ltr', 'NAME' => 'Español VE', 'NAME_ENG' => 'Spanish Venezuela'),
	'fa' => array('LANGUAGE' => 'fa', 'REGION' => 'IR', 'DIR' => 'rtl', 'NAME' => 'فارسی', 'NAME_ENG' => 'Farsi'),
	'fb' => array('LANGUAGE' => 'fr', 'REGION' => 'BE', 'DIR' => 'ltr', 'NAME' => 'Français BE', 'NAME_ENG' => 'French (Belgium)'),
	'fc' => array('LANGUAGE' => 'fr', 'REGION' => 'CA', 'DIR' => 'ltr', 'NAME' => 'Français CA', 'NAME_ENG' => 'French (Canada)'),
	'ff' => array('LANGUAGE' => 'ff', 'REGION' => 'SN', 'DIR' => 'ltr', 'NAME' => 'Fulfulde', 'NAME_ENG' => 'Fulah'),
	'fi' => array('LANGUAGE' => 'fi', 'REGION' => 'FI', 'DIR' => 'ltr', 'NAME' => 'Suomi', 'NAME_ENG' => 'Finnish'),
	'fo' => array('LANGUAGE' => 'fo', 'REGION' => 'FO', 'DIR' => 'ltr', 'NAME' => 'Føroyskt', 'NAME_ENG' => 'Faroese'),
	'fr' => array('LANGUAGE' => 'fr', 'REGION' => 'FR', 'DIR' => 'ltr', 'NAME' => 'Français', 'NAME_ENG' => 'French'),
	'fy' => array('LANGUAGE' => 'mk', 'REGION' => 'MK', 'DIR' => 'ltr', 'NAME' => 'Fyrom', 'NAME_ENG' => 'Fyrom'), 
	'ga' => array('LANGUAGE' => 'ga', 'REGION' => 'IE', 'DIR' => 'ltr', 'NAME' => 'Gaeilge', 'NAME_ENG' => 'Irish'),
    'gd' => array('LANGUAGE' => 'gd', 'REGION' => 'GB', 'DIR' => 'ltr', 'NAME' => 'Lallans', 'NAME_ENG' => 'Scots'),
	'gl' => array('LANGUAGE' => 'gl', 'REGION' => 'ES', 'DIR' => 'ltr', 'NAME' => 'Galego', 'NAME_ENG' => 'Galician'),
	'gu' => array('LANGUAGE' => 'gu', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'ગુજરાતી', 'NAME_ENG' => 'Gujarati'),
	'gv' => array('LANGUAGE' => 'gv', 'REGION' => 'GB', 'DIR' => 'ltr', 'NAME' => 'Gaelg/Gailck', 'NAME_ENG' => 'Manx'),
	'ha' => array('LANGUAGE' => 'ha', 'REGION' => 'NG', 'DIR' => 'ltr', 'NAME' => 'حَوْسَ', 'NAME_ENG' => 'Hausa'),
	'he' => array('LANGUAGE' => 'he', 'REGION' => 'IL', 'DIR' => 'rtl', 'NAME' => 'עברית', 'NAME_ENG' => 'Hebrew'),
	'hi' => array('LANGUAGE' => 'hi', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'हिन्दी', 'NAME_ENG' => 'Hindi'),
	'hr' => array('LANGUAGE' => 'hr', 'REGION' => 'HR', 'DIR' => 'ltr', 'NAME' => 'Hrvatski', 'NAME_ENG' => 'Croatian'),
	'ht' => array('LANGUAGE' => 'ht', 'REGION' => 'HT', 'DIR' => 'ltr', 'NAME' => 'Kreyòl ayisyen', 'NAME_ENG' => 'Haitian (Creole)'),
	'hu' => array('LANGUAGE' => 'hu', 'REGION' => 'HU', 'DIR' => 'ltr', 'NAME' => 'Magyar', 'NAME_ENG' => 'Hungarian'),
	'hy' => array('LANGUAGE' => 'hy', 'REGION' => 'AM', 'DIR' => 'ltr', 'NAME' => 'Հայերէն', 'NAME_ENG' => 'Armenian'),
	'id' => array('LANGUAGE' => 'id', 'REGION' => 'ID', 'DIR' => 'ltr', 'NAME' => 'Bahasa Indonesia', 'NAME_ENG' => 'Indonesian'),
	'ig' => array('LANGUAGE' => 'ig', 'REGION' => 'NG', 'DIR' => 'ltr', 'NAME' => 'Igbo', 'NAME_ENG' => 'Igbo'),
	'is' => array('LANGUAGE' => 'is', 'REGION' => 'IS', 'DIR' => 'ltr', 'NAME' => 'Íslenska', 'NAME_ENG' => 'Icelandic'),
	'it' => array('LANGUAGE' => 'it', 'REGION' => 'IT', 'DIR' => 'ltr', 'NAME' => 'Italiano', 'NAME_ENG' => 'Italian'),
	'iu' => array('LANGUAGE' => 'iu', 'REGION' => 'CA', 'DIR' => 'ltr', 'NAME' => 'ᐃᓄᒃᑎᑐᑦ', 'NAME_ENG' => 'Inuktitut'),
	'ja' => array('LANGUAGE' => 'ja', 'REGION' => 'JP', 'DIR' => 'rtl', 'NAME' => '日本語', 'NAME_ENG' => 'Japanese'),
	'ka' => array('LANGUAGE' => 'ka', 'REGION' => 'GE', 'DIR' => 'ltr', 'NAME' => 'ქართული', 'NAME_ENG' => 'Georgian'),
	'ki' => array('LANGUAGE' => 'ki', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'कोंकणी (kōṅkaṇī)', 'NAME_ENG' => 'Konkani'),
	'kk' => array('LANGUAGE' => 'kk', 'REGION' => 'KZ', 'DIR' => 'ltr', 'NAME' => 'Қазақ тілі', 'NAME_ENG' => 'Kazakh'),
	'kl' => array('LANGUAGE' => 'kl', 'REGION' => 'GL', 'DIR' => 'ltr', 'NAME' => 'Kalaallisut', 'NAME_ENG' => 'Greenlandic'),
	'km' => array('LANGUAGE' => 'km', 'REGION' => 'KH', 'DIR' => 'ltr', 'NAME' => 'bhāsā khmɛ̄r', 'NAME_ENG' => 'Khmer'),
	'kn' => array('LANGUAGE' => 'kn', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'ಕನ್ನಡ', 'NAME_ENG' => 'Kannada'),
	'ko' => array('LANGUAGE' => 'ko', 'REGION' => 'KR', 'DIR' => 'rtl', 'NAME' => '한국어', 'NAME_ENG' => 'Korean'),
	'ks' => array('LANGUAGE' => 'ks', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'कॉशुर', 'NAME_ENG' => 'Kashmiri'),
	'ku' => array('LANGUAGE' => 'ku', 'REGION' => 'TR', 'DIR' => 'ltr', 'NAME' => 'Kurdí', 'NAME_ENG' => 'Kurdish'),
	'ky' => array('LANGUAGE' => 'ky', 'REGION' => 'KG', 'DIR' => 'ltr', 'NAME' => 'قىرعىز', 'NAME_ENG' => 'Kyrgyz'),
	'kw' => array('LANGUAGE' => 'kw', 'REGION' => 'GB', 'DIR' => 'ltr', 'NAME' => 'Kernewek', 'NAME_ENG' => 'Cornish'),
	'la' => array('LANGUAGE' => 'ln', 'REGION' => 'IT', 'DIR' => 'ltr', 'NAME' => 'Lingua Latina', 'NAME_ENG' => 'Latin'),
	'lg' => array('LANGUAGE' => 'lg', 'REGION' => 'UG', 'DIR' => 'ltr', 'NAME' => 'LùGáànda', 'NAME_ENG' => 'Luganda'),
	'lo' => array('LANGUAGE' => 'lo', 'REGION' => 'LA', 'DIR' => 'ltr', 'NAME' => 'ພາສາລາວ', 'NAME_ENG' => 'Lao'),
	'lv' => array('LANGUAGE' => 'lv', 'REGION' => 'LV', 'DIR' => 'ltr', 'NAME' => 'latviešu valoda', 'NAME_ENG' => 'Latvian'),
	'li' => array('LANGUAGE' => 'li', 'REGION' => 'BE', 'DIR' => 'ltr', 'NAME' => 'Lèmburgs', 'NAME_ENG' => 'Limburgish'),
	'lt' => array('LANGUAGE' => 'lt', 'REGION' => 'LT', 'DIR' => 'ltr', 'NAME' => 'lietuvių kalba', 'NAME_ENG' => 'Lithuanian'),
	'mg' => array('LANGUAGE' => 'mg', 'REGION' => 'MG', 'DIR' => 'ltr', 'NAME' => 'Fiteny Malagasy', 'NAME_ENG' => 'Malagasy'),
	'mi' => array('LANGUAGE' => 'mi', 'REGION' => 'NZ', 'DIR' => 'ltr', 'NAME' => 'te Reo Māori', 'NAME_ENG' => 'Maori'),
	'ml' => array('LANGUAGE' => 'ml', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'മലയാളം (malayāḷam)', 'NAME_ENG' => 'Malayalam'),
	'mn' => array('LANGUAGE' => 'mn', 'REGION' => 'MN', 'DIR' => 'ltr', 'NAME' => 'монгол', 'NAME_ENG' => 'Mongolian'), 
	'mr' => array('LANGUAGE' => 'mr', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'मराठी (marāṭhī)', 'NAME_ENG' => 'Marathi'),
	'ms' => array('LANGUAGE' => 'ms', 'REGION' => 'MY', 'DIR' => 'ltr', 'NAME' => 'Bahasa melayu', 'NAME_ENG' => 'Malay'),
	'mt' => array('LANGUAGE' => 'mt', 'REGION' => 'MT', 'DIR' => 'ltr', 'NAME' => 'Malti', 'NAME_ENG' => 'Maltese'),
	'my' => array('LANGUAGE' => 'my', 'REGION' => 'MM', 'DIR' => 'ltr', 'NAME' => 'Burmese', 'NAME_ENG' => 'Burmese'),
	'mx' => array('LANGUAGE' => 'es', 'REGION' => 'MX', 'DIR' => 'ltr', 'NAME' => 'Español MX', 'NAME_ENG' => 'Spanish Mexico'),
	'nb' => array('LANGUAGE' => 'nl', 'REGION' => 'BE', 'DIR' => 'ltr', 'NAME' => 'Nederlands', 'NAME_ENG' => 'Dutch (Belgium)'),
	'ne' => array('LANGUAGE' => 'ne', 'REGION' => 'NP', 'DIR' => 'ltr', 'NAME' => 'नेपाली (nēpālī)', 'NAME_ENG' => 'Nepali'),
	'nl' => array('LANGUAGE' => 'nl', 'REGION' => 'NL', 'DIR' => 'ltr', 'NAME' => 'Nederlands', 'NAME_ENG' => 'Dutch'),
	'nn' => array('LANGUAGE' => 'nn', 'REGION' => 'NO', 'DIR' => 'ltr', 'NAME' => 'Norsk nynorsk', 'NAME_ENG' => 'Norwegian Nynorsk'),
	'no' => array('LANGUAGE' => 'no', 'REGION' => 'NO', 'DIR' => 'ltr', 'NAME' => 'Norsk', 'NAME_ENG' => 'Norwegian'),
	'nv' => array('LANGUAGE' => 'nl', 'REGION' => 'BE', 'DIR' => 'ltr', 'NAME' => 'Vlaams', 'NAME_ENG' => 'Flemish'),
	'oc' => array('LANGUAGE' => 'oc', 'REGION' => 'FR', 'DIR' => 'ltr', 'NAME' => 'Occitan', 'NAME_ENG' => 'Occitan'),
	'om' => array('LANGUAGE' => 'om', 'REGION' => 'ET', 'DIR' => 'ltr', 'NAME' => 'Afaan Oromo', 'NAME_ENG' => 'Oromo'),
	'or' => array('LANGUAGE' => 'or', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'ଓଡ଼ିଆ (ōṛiyā)', 'NAME_ENG' => 'Oriya'),
	'os' => array('LANGUAGE' => 'os', 'REGION' => 'RU', 'DIR' => 'ltr', 'NAME' => 'ирон ӕвзаг', 'NAME_ENG' => 'Ossetian'),
	'pb' => array('LANGUAGE' => 'pt', 'REGION' => 'BR', 'DIR' => 'ltr', 'NAME' => 'Português BR', 'NAME_ENG' => 'Portuguese (Brazil)'),
	'ph' => array('LANGUAGE' => 'ph', 'REGION' => 'AF', 'DIR' => 'rtl', 'NAME' => '(paṧto) پښتو', 'NAME_ENG' => 'Pashto'),
	'pl' => array('LANGUAGE' => 'pl', 'REGION' => 'PL', 'DIR' => 'ltr', 'NAME' => 'polski', 'NAME_ENG' => 'Polish'),
	'pt' => array('LANGUAGE' => 'pt', 'REGION' => 'PT', 'DIR' => 'ltr', 'NAME' => 'Português', 'NAME_ENG' => 'Portuguese'),
	'pu' => array('LANGUAGE' => 'pu', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'ﺏﺎﺠﻨﭘ (panjābi)', 'NAME_ENG' => 'Punjabi'),
    'ro' => array('LANGUAGE' => 'ro', 'REGION' => 'RO', 'DIR' => 'ltr', 'NAME' => 'Român', 'NAME_ENG' => 'Romanian'),
    'ru' => array('LANGUAGE' => 'ru', 'REGION' => 'RU', 'DIR' => 'ltr', 'NAME' => 'Русский', 'NAME_ENG' => 'Russian'),
    'rs' => array('LANGUAGE' => 'rs', 'REGION' => 'RS', 'DIR' => 'ltr', 'NAME' => 'Cрпски', 'NAME_ENG' => 'Serbian'),
	'rw' => array('LANGUAGE' => 'rw', 'REGION' => 'RW', 'DIR' => 'ltr', 'NAME' => 'Ikinyarwanda', 'NAME_ENG' => 'Kinyarwanda'),
    'sa' => array('LANGUAGE' => 'sa', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'संस्कृतम् (saṁskr̥tam)', 'NAME_ENG' => 'Sanskrit'),
    'sc' => array('LANGUAGE' => 'sc', 'REGION' => 'IT', 'DIR' => 'ltr', 'NAME' => 'Sardu', 'NAME_ENG' => 'Sardinian'),
	'sd' => array('LANGUAGE' => 'sd', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'सिन्धी, سنڌي، سندھی‎', 'NAME_ENG' => 'Sindhi'),
	'sf' => array('LANGUAGE' => 'sv', 'REGION' => 'FI', 'DIR' => 'ltr', 'NAME' => 'Svenska FI', 'NAME_ENG' => 'Swedish Finland'),
	'si' => array('LANGUAGE' => 'si', 'REGION' => 'LK', 'DIR' => 'ltr', 'NAME' => '	සිංහල', 'NAME_ENG' => 'Sinhala'),
	'sk' => array('LANGUAGE' => 'sk', 'REGION' => 'SK', 'DIR' => 'ltr', 'NAME' => 'Slovenčina', 'NAME_ENG' => 'Slovak'),
	'sl' => array('LANGUAGE' => 'sl', 'REGION' => 'SI', 'DIR' => 'ltr', 'NAME' => 'Slovenščina', 'NAME_ENG' => 'Slovenian'),
	'so' => array('LANGUAGE' => 'so', 'REGION' => 'SO', 'DIR' => 'ltr', 'NAME' => 'Af Soomaali', 'NAME_ENG' => 'Somali'),
	'sq' => array('LANGUAGE' => 'sq', 'REGION' => 'AL', 'DIR' => 'ltr', 'NAME' => 'Shqip', 'NAME_ENG' => 'Albanian'),
	'sr' => array('LANGUAGE' => 'sr', 'REGION' => 'RS', 'DIR' => 'ltr', 'NAME' => 'Srpski', 'NAME_ENG' => 'Serbian Latin'),
	'ss' => array('LANGUAGE' => 'ss', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'SiSwati', 'NAME_ENG' => 'Swati'),
	'st' => array('LANGUAGE' => 'st', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'Sesotho', 'NAME_ENG' => 'Sotho'),
	'sv' => array('LANGUAGE' => 'sv', 'REGION' => 'SE', 'DIR' => 'ltr', 'NAME' => 'Svenska', 'NAME_ENG' => 'Swedish'),
	'sw' => array('LANGUAGE' => 'sw', 'REGION' => 'KE', 'DIR' => 'ltr', 'NAME' => 'Kiswahili', 'NAME_ENG' => 'Swahili'),
	'ta' => array('LANGUAGE' => 'ta', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'தமிழ் (tamiḻ)', 'NAME_ENG' => 'Tamil'),
	'te' => array('LANGUAGE' => 'te', 'REGION' => 'IN', 'DIR' => 'ltr', 'NAME' => 'తెలుగు', 'NAME_ENG' => 'Telugu'),
	'tg' => array('LANGUAGE' => 'tg', 'REGION' => 'TJ', 'DIR' => 'ltr', 'NAME' => 'тоҷики', 'NAME_ENG' => 'Tajik'),
	'th' => array('LANGUAGE' => 'th', 'REGION' => 'TH', 'DIR' => 'ltr', 'NAME' => 'ภาษาไทย', 'NAME_ENG' => 'Thai'),
	'ti' => array('LANGUAGE' => 'ti', 'REGION' => 'ER', 'DIR' => 'ltr', 'NAME' => 'ትግረ', 'NAME_ENG' => 'Tigre'),
	'tk' => array('LANGUAGE' => 'tk', 'REGION' => 'TM', 'DIR' => 'ltr', 'NAME' => 'түркmенче', 'NAME_ENG' => 'Turkmen'),
	'tl' => array('LANGUAGE' => 'tl', 'REGION' => 'PH', 'DIR' => 'ltr', 'NAME' => 'Tagalog', 'NAME_ENG' => 'Tagalog'),
	'tn' => array('LANGUAGE' => 'tn', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'Setswana', 'NAME_ENG' => 'Tswana'),
	'tr' => array('LANGUAGE' => 'tr', 'REGION' => 'TR', 'DIR' => 'ltr', 'NAME' => 'Türkçe', 'NAME_ENG' => 'Turkish'),
	'ts' => array('LANGUAGE' => 'ts', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'xiTsonga', 'NAME_ENG' => 'Tsonga'),
	'tt' => array('LANGUAGE' => 'tt', 'REGION' => 'RU', 'DIR' => 'ltr', 'NAME' => 'татарча', 'NAME_ENG' => 'Tatar'),
	'uk' => array('LANGUAGE' => 'uk', 'REGION' => 'UA', 'DIR' => 'ltr', 'NAME' => 'Українська', 'NAME_ENG' => 'Ukrainian'),
	'ug' => array('LANGUAGE' => 'ug', 'REGION' => 'CN', 'DIR' => 'ltr', 'NAME' => 'Уйғур /ئۇيغۇر', 'NAME_ENG' => 'Uyghur'),
	'ur' => array('LANGUAGE' => 'ur', 'REGION' => 'PK', 'DIR' => 'ltr', 'NAME' => 'اردو', 'NAME_ENG' => 'Urdu'),
	'us' => array('LANGUAGE' => 'en', 'REGION' => 'US', 'DIR' => 'ltr', 'NAME' => 'English US', 'NAME_ENG' => 'English US'),
	'uz' => array('LANGUAGE' => 'uz', 'REGION' => 'UZ', 'DIR' => 'ltr', 'NAME' => 'أۇزبېك ﺗﻴﻠی', 'NAME_ENG' => 'Uzbek'),
	've' => array('LANGUAGE' => 've', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'tshiVenḓa', 'NAME_ENG' => 'Venda'),
	'vi' => array('LANGUAGE' => 'vi', 'REGION' => 'VN', 'DIR' => 'ltr', 'NAME' => '㗂越', 'NAME_ENG' => 'Vietnamese'),
	'wa' => array('LANGUAGE' => 'wa', 'REGION' => 'BE', 'DIR' => 'ltr', 'NAME' => 'walon', 'NAME_ENG' => 'Walloon'),
	'wo' => array('LANGUAGE' => 'wo', 'REGION' => 'SN', 'DIR' => 'ltr', 'NAME' => 'Wollof', 'NAME_ENG' => 'Wolof'),
	'xh' => array('LANGUAGE' => 'xh', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'isiXhosa', 'NAME_ENG' => 'Xhosa'),
	'yi' => array('LANGUAGE' => 'yi', 'REGION' => 'US', 'DIR' => 'ltr', 'NAME' => 'ײִדיש', 'NAME_ENG' => 'Yiddish'),
	'zh' => array('LANGUAGE' => 'zh', 'REGION' => 'ZN', 'DIR' => 'ltr', 'NAME' => '中文', 'NAME_ENG' => 'Chinese'), 
	'zt' => array('LANGUAGE' => 'zt', 'REGION' => 'ZN', 'DIR' => 'ltr', 'NAME' => '普通话', 'NAME_ENG' => 'Chinese traditional'), 
	'zu' => array('LANGUAGE' => 'zu', 'REGION' => 'ZA', 'DIR' => 'ltr', 'NAME' => 'isiZulu', 'NAME_ENG' => 'Zulu')
);

?>