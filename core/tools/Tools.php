<?php
namespace Coxis\Core\Tools;

class Tools {
	static $months = array(
		'January'	=>	'Janvier',
		'February'	=>	'Février',
		'March'	=>	'Mars',
		'April'	=>	'Avril',
		'May'	=>	'Mai',
		'June'	=>	'Juin',
		'July'	=>	'Juillet',
		'August'	=>	'Aoùt',
		'September'	=>	'Septembre',
		'October'	=>	'Octobre',
		'November'	=>	'Novembre',
		'December'	=>	'Décembre',
	);

	static $jours = array(
		'Lundi'=>'Lundi',
		'Mardi'=>'Mardi',
		'Mercredi'=>'Mercredi',
		'Jeudi'=>'Jeudi',
		'Vendredi'=>'Vendredi',
		'Samedi'=>'Samedi',
		'Dimanche'=>'Dimanche',
	);
	
	public static function truncateHTML($html, $maxLength, $trailing='...') {
		$html = trim($html);
		$printedLength = 0;
		$position = 0;
		$tags = array();
		
		$res = '';

		while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)) {
			list($tag, $tagPosition) = $match[0];

			// Print text leading up to the tag.
			$str = substr($html, $position, $tagPosition - $position);
			if ($printedLength + strlen($str) > $maxLength) {
				$res .= (substr($str, 0, $maxLength - $printedLength));
				$printedLength = $maxLength;
				break;
			}

			$res .= ($str);
			$printedLength += strlen($str);

			if ($tag[0] == '&') {
				// Handle the entity.
				$res .= ($tag);
				$printedLength++;
			}
			else {
				// Handle the tag.
				$tagName = $match[1][0];
				if($tag[1] == '/') {
					// This is a closing tag.

					$openingTag = array_pop($tags);

					$res .= ($tag);
				}
				else if ($tag[strlen($tag) - 2] == '/' || $tagName == 'br' || $tagName == 'hr') {
					// Self-closing tag.
					$res .= ($tag);
				}
				else {
					// Opening tag.
					$res .= ($tag);
					$tags[] = $tagName;
				}
			}

			// Continue after the tag.
			$position = $tagPosition + strlen($tag);
		}

		// Print any remaining text.
		if ($printedLength < $maxLength && $position < strlen($html))
			$res .= (substr($html, $position, $maxLength - $printedLength));
			
		if($position < strlen($html))
			$res .= $trailing;
			
		// Close any open tags.
		while (!empty($tags))
			$res .= sprintf('</%s>', array_pop($tags));
			
		return $res;
	}

	public static function truncate($str, $length, $trailing='...') {
		// take off chars for the trailing
		$length-=mb_strlen($trailing);
		
		if (mb_strlen($str)> $length)
			// string exceeded length, truncate and add trailing dots
			return mb_substr($str,0,$length).$trailing;
		else
			// string was already short enough, return the string
			$res = $str;

		return $res;
	}
	
	public static function truncateWords($str, $length, $trailing='...') {
		$words = explode(' ', $str);
		
		$cutwords = array_slice($words, 0, 15);
		
		return implode(' ', $cutwords).(sizeof($words) > sizeof($cutwords) ? $trailing:'');
	}
	
	private static function remove_accents($str, $charset='utf-8') {
		$str = htmlentities($str, ENT_NOQUOTES, $charset);
		
		$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
		$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caract�res
		
		return $str;
	}
	
	static public function slugify($text) {
		$text = static::remove_accents($text);
	
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// transliterate
		if (function_exists('iconv'))
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// lowercase
		$text = strtolower($text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
			return 'n-a';

		return $text;
	}
	
	static public function randstr($length=10) {
		$validCharacters = 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789';
		$validCharNumber = strlen($validCharacters);

		$result = '';

		for ($i = 0; $i < $length; $i++) {
			$index = mt_rand(0, $validCharNumber - 1);
			$result .= $validCharacters[$index];
		}

		return $result;
	}
	
	#todo move to i18n
	static $countries = array(
		'AF' => 'AFGHANISTAN',
		'AX' => 'ÅLAND ISLANDS',
		'AL' => 'ALBANIA',
		'DZ' => 'ALGERIA',
		'AS' => 'AMERICAN SAMOA',
		'AD' => 'ANDORRA',
		'AO' => 'ANGOLA',
		'AI' => 'ANGUILLA',
		'AQ' => 'ANTARCTICA',
		'AG' => 'ANTIGUA AND BARBUDA',
		'AR' => 'ARGENTINA',
		'AM' => 'ARMENIA',
		'AW' => 'ARUBA',
		'AU' => 'AUSTRALIA',
		'AT' => 'AUSTRIA',
		'AZ' => 'AZERBAIJAN',
		'BS' => 'BAHAMAS',
		'BH' => 'BAHRAIN',
		'BD' => 'BANGLADESH',
		'BB' => 'BARBADOS',
		'BY' => 'BELARUS',
		'BE' => 'BELGIUM',
		'BZ' => 'BELIZE',
		'BJ' => 'BENIN',
		'BM' => 'BERMUDA',
		'BT' => 'BHUTAN',
		'BO' => 'BOLIVIA, PLURINATIONAL STATE OF',
		'BQ' => 'BONAIRE, SINT EUSTATIUS AND SABA',
		'BA' => 'BOSNIA AND HERZEGOVINA',
		'BW' => 'BOTSWANA',
		'BV' => 'BOUVET ISLAND',
		'BR' => 'BRAZIL',
		'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
		'BN' => 'BRUNEI DARUSSALAM',
		'BG' => 'BULGARIA',
		'BF' => 'BURKINA FASO',
		'BI' => 'BURUNDI',
		'KH' => 'CAMBODIA',
		'CM' => 'CAMEROON',
		'CA' => 'CANADA',
		'CV' => 'CAPE VERDE',
		'KY' => 'CAYMAN ISLANDS',
		'CF' => 'CENTRAL AFRICAN REPUBLIC',
		'TD' => 'CHAD',
		'CL' => 'CHILE',
		'CN' => 'CHINA',
		'CX' => 'CHRISTMAS ISLAND',
		'CC' => 'COCOS (KEELING) ISLANDS',
		'CO' => 'COLOMBIA',
		'KM' => 'COMOROS',
		'CG' => 'CONGO',
		'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
		'CK' => 'COOK ISLANDS',
		'CR' => 'COSTA RICA',
		'CI' => 'CÔTE D\'IVOIRE',
		'HR' => 'CROATIA',
		'CU' => 'CUBA',
		'CW' => 'CURAÇAO',
		'CY' => 'CYPRUS',
		'CZ' => 'CZECH REPUBLIC',
		'DK' => 'DENMARK',
		'DJ' => 'DJIBOUTI',
		'DM' => 'DOMINICA',
		'DO' => 'DOMINICAN REPUBLIC',
		'EC' => 'ECUADOR',
		'EG' => 'EGYPT',
		'SV' => 'EL SALVADOR',
		'GQ' => 'EQUATORIAL GUINEA',
		'ER' => 'ERITREA',
		'EE' => 'ESTONIA',
		'ET' => 'ETHIOPIA',
		'FK' => 'FALKLAND ISLANDS (MALVINAS)',
		'FO' => 'FAROE ISLANDS',
		'FJ' => 'FIJI',
		'FI' => 'FINLAND',
		'FR' => 'FRANCE',
		'GF' => 'FRENCH GUIANA',
		'PF' => 'FRENCH POLYNESIA',
		'TF' => 'FRENCH SOUTHERN TERRITORIES',
		'GA' => 'GABON',
		'GM' => 'GAMBIA',
		'GE' => 'GEORGIA',
		'DE' => 'GERMANY',
		'GH' => 'GHANA',
		'GI' => 'GIBRALTAR',
		'GR' => 'GREECE',
		'GL' => 'GREENLAND',
		'GD' => 'GRENADA',
		'GP' => 'GUADELOUPE',
		'GU' => 'GUAM',
		'GT' => 'GUATEMALA',
		'GG' => 'GUERNSEY',
		'GN' => 'GUINEA',
		'GW' => 'GUINEA-BISSAU',
		'GY' => 'GUYANA',
		'HT' => 'HAITI',
		'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
		'VA' => 'HOLY SEE (VATICAN CITY STATE)',
		'HN' => 'HONDURAS',
		'HK' => 'HONG KONG',
		'HU' => 'HUNGARY',
		'IS' => 'ICELAND',
		'IN' => 'INDIA',
		'ID' => 'INDONESIA',
		'IR' => 'IRAN, ISLAMIC REPUBLIC OF',
		'IQ' => 'IRAQ',
		'IE' => 'IRELAND',
		'IM' => 'ISLE OF MAN',
		'IL' => 'ISRAEL',
		'IT' => 'ITALY',
		'JM' => 'JAMAICA',
		'JP' => 'JAPAN',
		'JE' => 'JERSEY',
		'JO' => 'JORDAN',
		'KZ' => 'KAZAKHSTAN',
		'KE' => 'KENYA',
		'KI' => 'KIRIBATI',
		'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF',
		'KR' => 'KOREA, REPUBLIC OF',
		'KW' => 'KUWAIT',
		'KG' => 'KYRGYZSTAN',
		'LA' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',
		'LV' => 'LATVIA',
		'LB' => 'LEBANON',
		'LS' => 'LESOTHO',
		'LR' => 'LIBERIA',
		'LY' => 'LIBYA',
		'LI' => 'LIECHTENSTEIN',
		'LT' => 'LITHUANIA',
		'LU' => 'LUXEMBOURG',
		'MO' => 'MACAO',
		'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
		'MG' => 'MADAGASCAR',
		'MW' => 'MALAWI',
		'MY' => 'MALAYSIA',
		'MV' => 'MALDIVES',
		'ML' => 'MALI',
		'MT' => 'MALTA',
		'MH' => 'MARSHALL ISLANDS',
		'MQ' => 'MARTINIQUE',
		'MR' => 'MAURITANIA',
		'MU' => 'MAURITIUS',
		'YT' => 'MAYOTTE',
		'MX' => 'MEXICO',
		'FM' => 'MICRONESIA, FEDERATED STATES OF',
		'MD' => 'MOLDOVA, REPUBLIC OF',
		'MC' => 'MONACO',
		'MN' => 'MONGOLIA',
		'ME' => 'MONTENEGRO',
		'MS' => 'MONTSERRAT',
		'MA' => 'MOROCCO',
		'MZ' => 'MOZAMBIQUE',
		'MM' => 'MYANMAR',
		'NA' => 'NAMIBIA',
		'NR' => 'NAURU',
		'NP' => 'NEPAL',
		'NL' => 'NETHERLANDS',
		'NC' => 'NEW CALEDONIA',
		'NZ' => 'NEW ZEALAND',
		'NI' => 'NICARAGUA',
		'NE' => 'NIGER',
		'NG' => 'NIGERIA',
		'NU' => 'NIUE',
		'NF' => 'NORFOLK ISLAND',
		'MP' => 'NORTHERN MARIANA ISLANDS',
		'NO' => 'NORWAY',
		'OM' => 'OMAN',
		'PK' => 'PAKISTAN',
		'PW' => 'PALAU',
		'PS' => 'PALESTINIAN TERRITORY, OCCUPIED',
		'PA' => 'PANAMA',
		'PG' => 'PAPUA NEW GUINEA',
		'PY' => 'PARAGUAY',
		'PE' => 'PERU',
		'PH' => 'PHILIPPINES',
		'PN' => 'PITCAIRN',
		'PL' => 'POLAND',
		'PT' => 'PORTUGAL',
		'PR' => 'PUERTO RICO',
		'QA' => 'QATAR',
		'RE' => 'RÉUNION',
		'RO' => 'ROMANIA',
		'RU' => 'RUSSIAN FEDERATION',
		'RW' => 'RWANDA',
		'BL' => 'SAINT BARTHÉLEMY',
		'SH' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA',
		'KN' => 'SAINT KITTS AND NEVIS',
		'LC' => 'SAINT LUCIA',
		'MF' => 'SAINT MARTIN (FRENCH PART)',
		'PM' => 'SAINT PIERRE AND MIQUELON',
		'VC' => 'SAINT VINCENT AND THE GRENADINES',
		'WS' => 'SAMOA',
		'SM' => 'SAN MARINO',
		'ST' => 'SAO TOME AND PRINCIPE',
		'SA' => 'SAUDI ARABIA',
		'SN' => 'SENEGAL',
		'RS' => 'SERBIA',
		'SC' => 'SEYCHELLES',
		'SL' => 'SIERRA LEONE',
		'SG' => 'SINGAPORE',
		'SX' => 'SINT MAARTEN (DUTCH PART)',
		'SK' => 'SLOVAKIA',
		'SI' => 'SLOVENIA',
		'SB' => 'SOLOMON ISLANDS',
		'SO' => 'SOMALIA',
		'ZA' => 'SOUTH AFRICA',
		'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
		'SS' => 'SOUTH SUDAN',
		'ES' => 'SPAIN',
		'LK' => 'SRI LANKA',
		'SD' => 'SUDAN',
		'SR' => 'SURINAME',
		'SJ' => 'SVALBARD AND JAN MAYEN',
		'SZ' => 'SWAZILAND',
		'SE' => 'SWEDEN',
		'CH' => 'SWITZERLAND',
		'SY' => 'SYRIAN ARAB REPUBLIC',
		'TW' => 'TAIWAN, PROVINCE OF CHINA',
		'TJ' => 'TAJIKISTAN',
		'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
		'TH' => 'THAILAND',
		'TL' => 'TIMOR-LESTE',
		'TG' => 'TOGO',
		'TK' => 'TOKELAU',
		'TO' => 'TONGA',
		'TT' => 'TRINIDAD AND TOBAGO',
		'TN' => 'TUNISIA',
		'TR' => 'TURKEY',
		'TM' => 'TURKMENISTAN',
		'TC' => 'TURKS AND CAICOS ISLANDS',
		'TV' => 'TUVALU',
		'UG' => 'UGANDA',
		'UA' => 'UKRAINE',
		'AE' => 'UNITED ARAB EMIRATES',
		'GB' => 'UNITED KINGDOM',
		'US' => 'UNITED STATES',
		'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
		'UY' => 'URUGUAY',
		'UZ' => 'UZBEKISTAN',
		'VU' => 'VANUATU',
		'VE' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF',
		'VN' => 'VIET NAM',
		'VG' => 'VIRGIN ISLANDS, BRITISH',
		'VI' => 'VIRGIN ISLANDS, U.S.',
		'WF' => 'WALLIS AND FUTUNA',
		'EH' => 'WESTERN SAHARA',
		'YE' => 'YEMEN',
		'ZM' => 'ZAMBIA',
		'ZW' => 'ZIMBABWE'
	);
}