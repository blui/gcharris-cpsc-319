-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Server version: 5.1.66
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `frp`
--

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE IF NOT EXISTS `child` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `family_id` mediumint(8) unsigned NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `child_full_name` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `registration_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`),
  KEY `last_name` (`last_name`),
  KEY `child_full_name` (`child_full_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Triggers `child`
--
DROP TRIGGER IF EXISTS `child_BINS`;
DELIMITER //
CREATE TRIGGER `child_BINS` BEFORE INSERT ON `child`
 FOR EACH ROW SET NEW.child_full_name = CONCAT_WS(' ', NEW.first_name, NEW.last_name)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `child_BUPD`;
DELIMITER //
CREATE TRIGGER `child_BUPD` BEFORE UPDATE ON `child`
 FOR EACH ROW SET NEW.child_full_name = CONCAT_WS(' ', NEW.first_name, NEW.last_name)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `code` char(2) NOT NULL,
  `name` char(52) NOT NULL,
  PRIMARY KEY (`code`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`code`, `name`) VALUES
('AD', 'Andorra'),
('AE', 'United Arab Emirates'),
('AF', 'Afghanistan'),
('AG', 'Antigua and Barbuda'),
('AI', 'Anguilla'),
('AL', 'Albania'),
('AM', 'Armenia'),
('AN', 'Netherlands Antilles'),
('AO', 'Angola'),
('AQ', 'Antarctica'),
('AR', 'Argentina'),
('AS', 'American Samoa'),
('AT', 'Austria'),
('AU', 'Australia'),
('AW', 'Aruba'),
('AX', 'Åland Islands'),
('AZ', 'Azerbaijan'),
('BA', 'Bosnia and Herzegovina'),
('BB', 'Barbados'),
('BD', 'Bangladesh'),
('BE', 'Belgium'),
('BF', 'Burkina Faso'),
('BG', 'Bulgaria'),
('BH', 'Bahrain'),
('BI', 'Burundi'),
('BJ', 'Benin'),
('BL', 'Saint Barthélemy'),
('BM', 'Bermuda'),
('BN', 'Brunei'),
('BO', 'Bolivia'),
('BQ', 'British Antarctic Territory'),
('BR', 'Brazil'),
('BS', 'Bahamas'),
('BT', 'Bhutan'),
('BV', 'Bouvet Island'),
('BW', 'Botswana'),
('BY', 'Belarus'),
('BZ', 'Belize'),
('CA', 'Canada'),
('CC', 'Cocos [Keeling] Islands'),
('CD', 'Congo - Kinshasa'),
('CF', 'Central African Republic'),
('CG', 'Congo - Brazzaville'),
('CH', 'Switzerland'),
('CI', 'Côte d’Ivoire'),
('CK', 'Cook Islands'),
('CL', 'Chile'),
('CM', 'Cameroon'),
('CN', 'China'),
('CO', 'Colombia'),
('CR', 'Costa Rica'),
('CS', 'Serbia and Montenegro'),
('CT', 'Canton and Enderbury Islands'),
('CU', 'Cuba'),
('CV', 'Cape Verde'),
('CX', 'Christmas Island'),
('CY', 'Cyprus'),
('CZ', 'Czech Republic'),
('DD', 'East Germany'),
('DE', 'Germany'),
('DJ', 'Djibouti'),
('DK', 'Denmark'),
('DM', 'Dominica'),
('DO', 'Dominican Republic'),
('DZ', 'Algeria'),
('EC', 'Ecuador'),
('EE', 'Estonia'),
('EG', 'Egypt'),
('EH', 'Western Sahara'),
('ER', 'Eritrea'),
('ES', 'Spain'),
('ET', 'Ethiopia'),
('FI', 'Finland'),
('FJ', 'Fiji'),
('FK', 'Falkland Islands'),
('FM', 'Micronesia'),
('FO', 'Faroe Islands'),
('FQ', 'French Southern and Antarctic Territories'),
('FR', 'France'),
('FX', 'Metropolitan France'),
('GA', 'Gabon'),
('GB', 'United Kingdom'),
('GD', 'Grenada'),
('GE', 'Georgia'),
('GF', 'French Guiana'),
('GG', 'Guernsey'),
('GH', 'Ghana'),
('GI', 'Gibraltar'),
('GL', 'Greenland'),
('GM', 'Gambia'),
('GN', 'Guinea'),
('GP', 'Guadeloupe'),
('GQ', 'Equatorial Guinea'),
('GR', 'Greece'),
('GS', 'South Georgia and the South Sandwich Islands'),
('GT', 'Guatemala'),
('GU', 'Guam'),
('GW', 'Guinea-Bissau'),
('GY', 'Guyana'),
('HK', 'Hong Kong SAR China'),
('HM', 'Heard Island and McDonald Islands'),
('HN', 'Honduras'),
('HR', 'Croatia'),
('HT', 'Haiti'),
('HU', 'Hungary'),
('ID', 'Indonesia'),
('IE', 'Ireland'),
('IL', 'Israel'),
('IM', 'Isle of Man'),
('IN', 'India'),
('IO', 'British Indian Ocean Territory'),
('IQ', 'Iraq'),
('IR', 'Iran'),
('IS', 'Iceland'),
('IT', 'Italy'),
('JE', 'Jersey'),
('JM', 'Jamaica'),
('JO', 'Jordan'),
('JP', 'Japan'),
('JT', 'Johnston Island'),
('KE', 'Kenya'),
('KG', 'Kyrgyzstan'),
('KH', 'Cambodia'),
('KI', 'Kiribati'),
('KM', 'Comoros'),
('KN', 'Saint Kitts and Nevis'),
('KP', 'North Korea'),
('KR', 'South Korea'),
('KW', 'Kuwait'),
('KY', 'Cayman Islands'),
('KZ', 'Kazakhstan'),
('LA', 'Laos'),
('LB', 'Lebanon'),
('LC', 'Saint Lucia'),
('LI', 'Liechtenstein'),
('LK', 'Sri Lanka'),
('LR', 'Liberia'),
('LS', 'Lesotho'),
('LT', 'Lithuania'),
('LU', 'Luxembourg'),
('LV', 'Latvia'),
('LY', 'Libya'),
('MA', 'Morocco'),
('MC', 'Monaco'),
('MD', 'Moldova'),
('ME', 'Montenegro'),
('MF', 'Saint Martin'),
('MG', 'Madagascar'),
('MH', 'Marshall Islands'),
('MI', 'Midway Islands'),
('MK', 'Macedonia'),
('ML', 'Mali'),
('MM', 'Myanmar [Burma]'),
('MN', 'Mongolia'),
('MO', 'Macau SAR China'),
('MP', 'Northern Mariana Islands'),
('MQ', 'Martinique'),
('MR', 'Mauritania'),
('MS', 'Montserrat'),
('MT', 'Malta'),
('MU', 'Mauritius'),
('MV', 'Maldives'),
('MW', 'Malawi'),
('MX', 'Mexico'),
('MY', 'Malaysia'),
('MZ', 'Mozambique'),
('NA', 'Namibia'),
('NC', 'New Caledonia'),
('NE', 'Niger'),
('NF', 'Norfolk Island'),
('NG', 'Nigeria'),
('NI', 'Nicaragua'),
('NL', 'Netherlands'),
('NO', 'Norway'),
('NP', 'Nepal'),
('NQ', 'Dronning Maud Land'),
('NR', 'Nauru'),
('NT', 'Neutral Zone'),
('NU', 'Niue'),
('NZ', 'New Zealand'),
('OM', 'Oman'),
('PA', 'Panama'),
('PC', 'Pacific Islands Trust Territory'),
('PE', 'Peru'),
('PF', 'French Polynesia'),
('PG', 'Papua New Guinea'),
('PH', 'Philippines'),
('PK', 'Pakistan'),
('PL', 'Poland'),
('PM', 'Saint Pierre and Miquelon'),
('PN', 'Pitcairn Islands'),
('PR', 'Puerto Rico'),
('PS', 'Palestinian Territories'),
('PT', 'Portugal'),
('PU', 'U.S. Miscellaneous Pacific Islands'),
('PW', 'Palau'),
('PY', 'Paraguay'),
('PZ', 'Panama Canal Zone'),
('QA', 'Qatar'),
('RE', 'Réunion'),
('RO', 'Romania'),
('RS', 'Serbia'),
('RU', 'Russia'),
('RW', 'Rwanda'),
('SA', 'Saudi Arabia'),
('SB', 'Solomon Islands'),
('SC', 'Seychelles'),
('SD', 'Sudan'),
('SE', 'Sweden'),
('SG', 'Singapore'),
('SH', 'Saint Helena'),
('SI', 'Slovenia'),
('SJ', 'Svalbard and Jan Mayen'),
('SK', 'Slovakia'),
('SL', 'Sierra Leone'),
('SM', 'San Marino'),
('SN', 'Senegal'),
('SO', 'Somalia'),
('SR', 'Suriname'),
('ST', 'São Tomé and Príncipe'),
('SU', 'Union of Soviet Socialist Republics'),
('SV', 'El Salvador'),
('SY', 'Syria'),
('SZ', 'Swaziland'),
('TC', 'Turks and Caicos Islands'),
('TD', 'Chad'),
('TF', 'French Southern Territories'),
('TG', 'Togo'),
('TH', 'Thailand'),
('TJ', 'Tajikistan'),
('TK', 'Tokelau'),
('TL', 'Timor-Leste'),
('TM', 'Turkmenistan'),
('TN', 'Tunisia'),
('TO', 'Tonga'),
('TR', 'Turkey'),
('TT', 'Trinidad and Tobago'),
('TV', 'Tuvalu'),
('TW', 'Taiwan'),
('TZ', 'Tanzania'),
('UA', 'Ukraine'),
('UG', 'Uganda'),
('UM', 'U.S. Minor Outlying Islands'),
('US', 'United States'),
('UY', 'Uruguay'),
('UZ', 'Uzbekistan'),
('VA', 'Vatican City'),
('VC', 'Saint Vincent and the Grenadines'),
('VD', 'North Vietnam'),
('VE', 'Venezuela'),
('VG', 'British Virgin Islands'),
('VI', 'U.S. Virgin Islands'),
('VN', 'Vietnam'),
('VU', 'Vanuatu'),
('WF', 'Wallis and Futuna'),
('WK', 'Wake Island'),
('WS', 'Samoa'),
('YD', 'People''s Democratic Republic of Yemen'),
('YE', 'Yemen'),
('YT', 'Mayotte'),
('ZA', 'South Africa'),
('ZM', 'Zambia'),
('ZW', 'Zimbabwe'),
('ZZ', 'Unknown or Invalid Region');

--
-- Triggers `country`
--
DROP TRIGGER IF EXISTS `country_ADEL`;
DELIMITER //
CREATE TRIGGER `country_ADEL` AFTER DELETE ON `country`
 FOR EACH ROW UPDATE family SET guardian_origin_country_name = NULL WHERE guardian_origin_country = OLD.code
//
DELIMITER ;
DROP TRIGGER IF EXISTS `country_AUPD`;
DELIMITER //
CREATE TRIGGER `country_AUPD` AFTER UPDATE ON `country`
 FOR EACH ROW UPDATE family SET guardian_origin_country_name = NEW.name WHERE guardian_origin_country = NEW.code
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

CREATE TABLE IF NOT EXISTS `family` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(10) NOT NULL,
  `registration_date` date NOT NULL,
  `first_attendance_date` date DEFAULT NULL,
  `guardian_first_name` varchar(255) NOT NULL,
  `guardian_last_name` varchar(255) NOT NULL,
  `guardian_full_name` varchar(255) NOT NULL,
  `guardian_partner_first_name` text NOT NULL,
  `guardian_partner_last_name` text NOT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `guardian_role` text NOT NULL,
  `guardian_origin_country` char(2) DEFAULT NULL,
  `guardian_origin_country_name` varchar(52) DEFAULT NULL,
  `guardian_first_lang` varchar(3) DEFAULT NULL,
  `guardian_first_lang_name` varchar(52) DEFAULT NULL,
  `postal_3dig` varchar(3) NOT NULL,
  `allergies` text NOT NULL,
  `emerg_contact_first_name` text NOT NULL,
  `emerg_contact_last_name` text NOT NULL,
  `emerg_contact_relation` text NOT NULL,
  `emerg_contact_phone` varchar(15) NOT NULL,
  `hear_about_us` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guardian_email` (`guardian_email`),
  KEY `registration_date` (`registration_date`),
  KEY `phone_number` (`phone_number`),
  KEY `hear_about_us` (`hear_about_us`),
  KEY `guardian_first_lang` (`guardian_first_lang`),
  KEY `guardian_origin_country` (`guardian_origin_country`),
  KEY `guardian_full_name` (`guardian_full_name`),
  KEY `guardian_last_name` (`guardian_last_name`),
  KEY `guardian_origin_country_name` (`guardian_origin_country_name`),
  KEY `guardian_first_lang_name` (`guardian_first_lang_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Triggers `family`
--
DROP TRIGGER IF EXISTS `family_BINS`;
DELIMITER //
CREATE TRIGGER `family_BINS` BEFORE INSERT ON `family`
 FOR EACH ROW BEGIN
SET NEW.guardian_full_name = CONCAT_WS(' ', NEW.guardian_first_name, NEW.guardian_last_name),
NEW.guardian_first_lang_name = (SELECT lang_name_english FROM language WHERE lang_code = NEW.guardian_first_lang),
NEW.guardian_origin_country_name = (SELECT name FROM country WHERE code = NEW.guardian_origin_country);
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `family_BUPD`;
DELIMITER //
CREATE TRIGGER `family_BUPD` BEFORE UPDATE ON `family`
 FOR EACH ROW BEGIN
SET NEW.guardian_full_name = CONCAT_WS(' ', NEW.guardian_first_name, NEW.guardian_last_name),
NEW.guardian_first_lang_name = (SELECT lang_name_english FROM language WHERE lang_code = NEW.guardian_first_lang),
NEW.guardian_origin_country_name = (SELECT name FROM country WHERE code = NEW.guardian_origin_country);
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `guest_speaker`
--

CREATE TABLE IF NOT EXISTS `guest_speaker` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `program_session_id` mediumint(8) unsigned NOT NULL,
  `speaker_name` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `program_session_id` (`program_session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hear_about_us`
--

CREATE TABLE IF NOT EXISTS `hear_about_us` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `hear_about_us`
--

INSERT INTO `hear_about_us` (`id`, `text`) VALUES
(1, 'Friend'),
(2, 'Co-worker'),
(3, 'Family Member'),
(4, 'Advertisement '),
(5, 'Newspaper'),
(6, 'Internet');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `lang_code` varchar(3) NOT NULL,
  `lang_name_english` varchar(52) NOT NULL,
  `lang_name_localized` text NOT NULL,
  PRIMARY KEY (`lang_code`),
  KEY `lang_name_english` (`lang_name_english`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`lang_code`, `lang_name_english`, `lang_name_localized`) VALUES
('aar', 'Afar', 'Afaraf'),
('abk', 'Abkhaz', 'аҧсуа бызшәа'),
('afr', 'Afrikaans', 'Afrikaans'),
('aka', 'Akan', 'Akan'),
('amh', 'Amharic', 'አማርኛ'),
('ara', 'Arabic', 'العربية'),
('arg', 'Aragonese', 'Aragonés'),
('asm', 'Assamese', 'অসমীয়া'),
('ava', 'Avaric', 'авар мацӀ'),
('ave', 'Avestan', 'Avesta'),
('aym', 'Aymara', 'aymar aru'),
('aze', 'Azerbaijani', 'azərbaycan dili'),
('bak', 'Bashkir', 'башҡорт теле'),
('bam', 'Bambara', 'bamanankan'),
('bel', 'Belarusian', 'беларуская мова'),
('ben', 'Bengali', 'বাংলা'),
('bih', 'Bihari', 'भोजपुरी'),
('bis', 'Bislama', 'Bislama'),
('bod', 'Tibetan', 'བོད་ཡིག'),
('bos', 'Bosnian', 'bosanski jezik'),
('bre', 'Breton', 'brezhoneg'),
('bul', 'Bulgarian', 'български език'),
('cat', 'Catalan', 'Català'),
('ces', 'Czech', 'čeština'),
('cha', 'Chamorro', 'Chamoru'),
('che', 'Chechen', 'нохчийн мотт'),
('chu', 'Slavonic', 'ѩзыкъ словѣньскъ'),
('chv', 'Chuvash', 'чӑваш чӗлхи'),
('cor', 'Cornish', 'Kernewek'),
('cos', 'Corsican', 'Corsu'),
('cre', 'Cree', 'ᓀᐦᐃᔭᐍᐏᐣ'),
('cym', 'Welsh', 'Cymraeg'),
('dan', 'Danish', 'Dansk'),
('deu', 'German', 'Deutsch'),
('div', 'Divehi', 'Divehi'),
('dzo', 'Dzongkha', 'རྫོང་ཁ'),
('ell', 'Greek', 'ελληνικά'),
('eng', 'English', 'English'),
('epo', 'Esperanto', 'Esperanto'),
('est', 'Estonian', 'Eesti'),
('eus', 'Basque', 'Euskera'),
('ewe', 'Ewe', 'Eʋegbe'),
('fao', 'Faroese', 'Føroyskt'),
('fas', 'Persian', 'فارسی'),
('fij', 'Fijian', 'vosa Vakaviti'),
('fin', 'Finnish', 'Suomi'),
('fra', 'French', 'Français'),
('fry', 'Western Frisian', 'Frysk'),
('ful', 'Fula', 'Fulfulde'),
('gla', 'Gaelic', 'Gàidhlig'),
('gle', 'Irish', 'Gaeilge'),
('glg', 'Galician', 'Galego'),
('glv', 'Manx', 'Gaelg'),
('grn', 'Guaraní', 'Avañe''ẽ'),
('guj', 'Gujarati', 'ગુજરાતી'),
('hat', 'Haitian', 'Kreyòl ayisyen'),
('hau', 'Hausa', ' هَوُسَ'),
('heb', 'Hebrew', 'עברית'),
('her', 'Herero', 'Otjiherero'),
('hin', 'Hindi', 'हिन्दी'),
('hmo', 'Hiri Motu', 'Hiri Motu'),
('hrv', 'Croatian', 'hrvatski jezik'),
('hun', 'Hungarian', 'Magyar'),
('hye', 'Armenian', 'Հայերեն'),
('ibo', 'Igbo', 'Asụsụ Igbo'),
('ido', 'Ido', 'Ido'),
('iii', 'Nuosu', 'ꆈꌠ꒿ Nuosuhxop'),
('iku', 'Inuktitut', 'ᐃᓄᒃᑎᑐᑦ'),
('ina', 'Interlingua', 'Interlingua'),
('ind', 'Indonesian', 'Bahasa Indonesia'),
('ipk', 'Inupiaq', 'Iñupiaq'),
('isl', 'Icelandic', 'Íslenska'),
('ita', 'Italian', 'italiano'),
('jav', 'Javanese', 'Basa Jawa'),
('jpn', 'Japanese', '日本語'),
('kal', 'Kalaallisut', 'Kalaallisut'),
('kan', 'Kannada', 'ಕನ್ನಡ'),
('kas', 'Kashmiri', 'कश्मीरी'),
('kat', 'Georgian', 'ქართული'),
('kau', 'Kanuri', 'Kanuri'),
('kaz', 'Kazakh', 'қазақ тілі'),
('khm', 'Khmer', 'ខ្មែរ'),
('kik', 'Gikuyu', 'Gĩkũyũ'),
('kin', 'Kinyarwanda', 'Ikinyarwanda'),
('kir', 'Kyrgyz', 'Кыргызча'),
('kom', 'Komi', 'коми кыв'),
('kon', 'Kongo', 'KiKongo'),
('kor', 'Korean', '한국어'),
('kua', 'Kuanyama', 'Kuanyama'),
('kur', 'Kurdish', 'كوردی‎'),
('lao', 'Lao', 'ພາສາລາວ'),
('lav', 'Latvian', 'latviešu valoda'),
('lim', 'Limburgish', 'Limburgs'),
('lin', 'Lingala', 'Lingála'),
('lit', 'Lithuanian', 'lietuvių kalba'),
('ltz', 'Luxembourgish', 'Lëtzebuergesch'),
('lub', 'Luba-Katanga', 'Tshiluba'),
('lug', 'Ganda', 'Luganda'),
('mah', 'Marshallese', 'Kajin M̧ajeļ'),
('mal', 'Malayalam', 'മലയാളം'),
('mar', 'Marathi', 'मराठी'),
('mkd', 'Macedonian', 'македонски јазик'),
('mlg', 'Malagasy', 'Fiteny Malagasy'),
('mlt', 'Maltese', 'Malti'),
('mon', 'Mongolian', 'монгол'),
('mri', 'Māori', 'te reo Māori'),
('msa', 'Malay', 'بهاس ملايو‎'),
('mya', 'Burmese', 'ဗမာစာ'),
('nau', 'Nauru', 'Ekakairũ Naoero'),
('nav', 'Navajo', 'Dinékʼehǰí'),
('nbl', 'South Ndebele', 'isiNdebele'),
('nde', 'North Ndebele', 'isiNdebele'),
('ndo', 'Ndonga', 'Owambo'),
('nep', 'Nepali', 'नेपाली'),
('nld', 'Dutch', 'Vlaams'),
('nno', 'Norwegian Nynorsk', 'Norsk nynorsk'),
('nob', 'Norwegian Bokmål', 'Norsk bokmål'),
('nor', 'Norwegian', 'Norsk'),
('nya', 'Nyanja', 'chiCheŵa'),
('oci', 'Occitan', 'Lenga d''òc'),
('oji', 'Ojibwe', 'ᐊᓂᔑᓈᐯᒧᐎᓐ'),
('ori', 'Oriya', 'ଓଡ଼ିଆ'),
('orm', 'Oromo', 'Afaan Oromoo'),
('oss', 'Ossetian', 'ирон æвзаг'),
('pan', 'Punjabi', 'پنجابی‎'),
('pli', 'Pāli', 'पाऴि'),
('pol', 'Polish', 'Polszczyzna'),
('por', 'Portuguese', 'Português'),
('pus', 'Pashto', 'پښتو'),
('que', 'Quechua', 'Runa Simi'),
('roh', 'Romansh', 'Rumantsch Grischun'),
('ron', 'Romanian', 'Română'),
('run', 'Kirundi', 'Ikirundi'),
('rus', 'Russian', 'русский язык'),
('sag', 'Sango', 'yângâ tî sängö'),
('san', 'Sanskrit', 'संस्कृतम्'),
('sin', 'Sinhalese', 'සිංහල'),
('slk', 'Slovak', 'Slovenský'),
('slv', 'Slovene', 'Slovenščina'),
('sme', 'Northern Sami', 'Davvisámegiella'),
('smo', 'Samoan', 'gagana fa''a Samoa'),
('sna', 'Shona', 'chiShona'),
('snd', 'Sindhi', 'सिन्धी'),
('som', 'Somali', 'Soomaaliga'),
('sot', 'Southern Sotho', 'Sesotho'),
('spa', 'Spanish', 'Español'),
('sqi', 'Albanian', 'Gjuha Shqipe'),
('srd', 'Sardinian', 'Sardu'),
('srp', 'Serbian', 'српски језик'),
('ssw', 'Swati', 'SiSwati'),
('sun', 'Sundanese', 'Basa Sunda'),
('swa', 'Swahili', 'Kiswahili'),
('swe', 'Swedish', 'Svenska'),
('tah', 'Tahitian', 'Reo Tahiti'),
('tam', 'Tamil', 'தமிழ்'),
('tat', 'Tatar', 'татар теле'),
('tel', 'Telugu', 'తెలుగు'),
('tgk', 'Tajik', 'تاجیکی‎'),
('tgl', 'Tagalog', 'Wikang Tagalog'),
('tha', 'Thai', 'ไทย'),
('tir', 'Tigrinya', 'ትግርኛ'),
('ton', 'Tonga', 'Tonga'),
('tsn', 'Tswana', 'Setswana'),
('tso', 'Tsonga', 'Xitsonga'),
('tuk', 'Turkmen', 'Түркмен'),
('tur', 'Turkish', 'Türkçe'),
('twi', 'Twi', 'Twi'),
('uig', 'Uyghur', 'ئۇيغۇرچە‎'),
('ukr', 'Ukrainian', 'українська мова'),
('urd', 'Urdu', 'اردو'),
('uzb', 'Uzbek', 'أۇزبېك‎'),
('ven', 'Venda', 'Tshivenḓa'),
('vie', 'Vietnamese', 'Tiếng Việt'),
('vol', 'Volapük', 'Volapük'),
('wln', 'Walloon', 'Walon'),
('wol', 'Wolof', 'Wollof'),
('xho', 'Xhosa', 'isiXhosa'),
('yid', 'Yiddish', 'ייִדיש'),
('yor', 'Yoruba', 'Yorùbá'),
('zha', 'Zhuang', 'Saw cuengh'),
('zhc', 'Chinese (Simplified)', '汉语'),
('zht', 'Chinese (Traditional)', '漢語'),
('zul', 'Zulu', 'isiZulu');

--
-- Triggers `language`
--
DROP TRIGGER IF EXISTS `language_ADEL`;
DELIMITER //
CREATE TRIGGER `language_ADEL` AFTER DELETE ON `language`
 FOR EACH ROW UPDATE family SET guardian_first_lang_name = NULL WHERE guardian_first_lang = OLD.lang_code
//
DELIMITER ;
DROP TRIGGER IF EXISTS `language_AUPD`;
DELIMITER //
CREATE TRIGGER `language_AUPD` AFTER UPDATE ON `language`
 FOR EACH ROW UPDATE family SET guardian_first_lang_name = NEW.lang_name_english WHERE guardian_first_lang = NEW.lang_code
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mail_attachment`
--

CREATE TABLE IF NOT EXISTS `mail_attachment` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(255) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail_queue`
--

CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `message_obj` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `partner`
--

CREATE TABLE IF NOT EXISTS `partner` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `organization` text NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE IF NOT EXISTS `password_reset` (
  `staff_id` mediumint(8) unsigned NOT NULL,
  `reset_code` varchar(25) NOT NULL,
  `sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reset_code`),
  KEY `sent` (`sent`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE IF NOT EXISTS `program` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `program_session`
--

CREATE TABLE IF NOT EXISTS `program_session` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` mediumint(8) unsigned NOT NULL,
  `running` tinyint(1) unsigned NOT NULL,
  `date` date NOT NULL,
  `snacks_served` text NOT NULL,
  `special_celebrations` text NOT NULL,
  `field_trip` text NOT NULL,
  `hours` decimal(4,2) unsigned NOT NULL,
  `count_adult` smallint(5) unsigned NOT NULL DEFAULT '0',
  `count_child` smallint(5) unsigned NOT NULL DEFAULT '0',
  `count_total` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `program_id` (`program_id`),
  KEY `running` (`running`),
  KEY `program_id_id` (`program_id`,`id`),
  KEY `count_adult` (`count_adult`),
  KEY `count_child` (`count_child`),
  KEY `count_total` (`count_total`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `program_staff`
--

CREATE TABLE IF NOT EXISTS `program_staff` (
  `staff_id` mediumint(8) unsigned NOT NULL,
  `program_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`staff_id`,`program_id`),
  KEY `program_id` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `referral`
--

CREATE TABLE IF NOT EXISTS `referral` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_id` (`program_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_id` (`program_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_referral`
--

CREATE TABLE IF NOT EXISTS `session_referral` (
  `program_session_id` mediumint(8) unsigned NOT NULL,
  `referral_id` mediumint(8) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`program_session_id`,`referral_id`),
  KEY `referral_id` (`referral_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_resource`
--

CREATE TABLE IF NOT EXISTS `session_resource` (
  `program_session_id` mediumint(8) unsigned NOT NULL,
  `resource_id` mediumint(8) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`program_session_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sign_in_child`
--

CREATE TABLE IF NOT EXISTS `sign_in_child` (
  `sign_in_family_id` mediumint(8) unsigned NOT NULL,
  `child_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`sign_in_family_id`,`child_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `sign_in_child`
--
DROP TRIGGER IF EXISTS `sign_in_child_AINS`;
DELIMITER //
CREATE TRIGGER `sign_in_child_AINS` AFTER INSERT ON `sign_in_child`
 FOR EACH ROW BEGIN
UPDATE program_session AS PS, sign_in_family AS SIF SET PS.count_child = PS.count_child + 1, PS.count_total = PS.count_total + 1 WHERE PS.id = SIF.program_session_id AND SIF.id = NEW.sign_in_family_id;

SET @programID = (
     SELECT PS.program_id
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
       JOIN program_session AS PS ON SF.program_session_id = PS.id
      WHERE SC.child_id = NEW.child_id
        AND SC.sign_in_family_id = NEW.sign_in_family_id
      LIMIT 1
);

     DELETE SCV
       FROM sign_in_child_first_visit AS SCV
       JOIN (SELECT SC.sign_in_family_id, SC.child_id
               FROM sign_in_child AS SC
               JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
               JOIN program_session AS PS ON SF.program_session_id = PS.id
              WHERE PS.program_id = @programID
                AND SC.child_id = NEW.child_id)
         AS DEL
         ON SCV.sign_in_family_id = DEL.sign_in_family_id
        AND SCV.child_id = DEL.child_id;

SET @programSessionID = (
     SELECT MIN(PS.id)
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
       JOIN program_session AS PS ON SF.program_session_id = PS.id 
      WHERE PS.program_id = @programID
        AND SC.child_id = NEW.child_id
);

INSERT INTO sign_in_child_first_visit (sign_in_family_id, child_id, program_session_id)
     SELECT SC.sign_in_family_id, SC.child_id, SF.program_session_id
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
      WHERE SF.program_session_id = @programSessionID
        AND SC.child_id = NEW.child_id;

END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `sign_in_child_BDEL`;
DELIMITER //
CREATE TRIGGER `sign_in_child_BDEL` BEFORE DELETE ON `sign_in_child`
 FOR EACH ROW BEGIN
UPDATE program_session AS PS, sign_in_family AS SIF SET PS.count_child = PS.count_child - 1, PS.count_total = PS.count_total - 1 WHERE PS.id = SIF.program_session_id AND SIF.id = OLD.sign_in_family_id;

SET @programID = (
     SELECT PS.program_id
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
       JOIN program_session AS PS ON SF.program_session_id = PS.id
      WHERE SC.child_id = OLD.child_id
        AND SC.sign_in_family_id = OLD.sign_in_family_id
      LIMIT 1
);

DELETE SCV
       FROM sign_in_child_first_visit AS SCV
       JOIN (SELECT SC.sign_in_family_id, SC.child_id
               FROM sign_in_child AS SC
               JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
               JOIN program_session AS PS ON SF.program_session_id = PS.id
              WHERE PS.program_id = @programID
                AND SC.child_id = OLD.child_id)
         AS DEL
         ON SCV.sign_in_family_id = DEL.sign_in_family_id
        AND SCV.child_id = DEL.child_id;

SET @programSessionID = (
     SELECT MIN(PS.id)
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
       JOIN program_session AS PS ON SF.program_session_id = PS.id 
      WHERE PS.program_id = @programID
        AND SC.child_id = OLD.child_id
        AND SC.sign_in_family_id != OLD.sign_in_family_id
);

INSERT INTO sign_in_child_first_visit (sign_in_family_id, child_id, program_session_id)
     SELECT SC.sign_in_family_id, SC.child_id, SF.program_session_id
       FROM sign_in_child AS SC
       JOIN sign_in_family AS SF ON SF.id = SC.sign_in_family_id
      WHERE SF.program_session_id = @programSessionID
        AND SC.child_id = OLD.child_id;

END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sign_in_child_first_visit`
--

CREATE TABLE IF NOT EXISTS `sign_in_child_first_visit` (
  `sign_in_family_id` mediumint(8) unsigned NOT NULL,
  `child_id` mediumint(8) unsigned NOT NULL,
  `program_session_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`sign_in_family_id`,`child_id`),
  KEY `child_id` (`child_id`),
  KEY `program_session_id` (`program_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sign_in_family`
--

CREATE TABLE IF NOT EXISTS `sign_in_family` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `program_session_id` mediumint(8) unsigned NOT NULL,
  `family_id` mediumint(8) unsigned NOT NULL,
  `parent_present` tinyint(2) unsigned NOT NULL,
  `adult_count` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_session_id` (`program_session_id`,`family_id`),
  KEY `family_id` (`family_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Triggers `sign_in_family`
--
DROP TRIGGER IF EXISTS `sign_in_family_AINS`;
DELIMITER //
CREATE TRIGGER `sign_in_family_AINS` AFTER INSERT ON `sign_in_family`
 FOR EACH ROW BEGIN
UPDATE program_session SET count_adult = count_adult + NEW.adult_count, count_total = count_total + NEW.adult_count WHERE id = NEW.program_session_id;

SET @programID = (
     SELECT PS.program_id
       FROM sign_in_family AS SF
       JOIN program_session AS PS ON SF.program_session_id = PS.id
      WHERE SF.id = NEW.id
      LIMIT 1
);

     DELETE sign_in_family_first_visit
       FROM sign_in_family_first_visit
       JOIN (SELECT SF.id
               FROM sign_in_family AS SF
               JOIN program_session AS PS ON SF.program_session_id = PS.id
              WHERE PS.program_id = @programID
                AND SF.family_id = NEW.family_id)
         AS DEL
         ON sign_in_family_id = DEL.id;

SET @programSessionID = (
     SELECT MIN(PS.id)
       FROM sign_in_family AS SF
       JOIN program_session AS PS ON SF.program_session_id = PS.id 
      WHERE PS.program_id = @programID
        AND SF.family_id = NEW.family_id
);

INSERT INTO sign_in_family_first_visit (sign_in_family_id, program_session_id)
     SELECT SF.id, SF.program_session_id
       FROM sign_in_family AS SF
      WHERE SF.program_session_id = @programSessionID
        AND SF.family_id = NEW.family_id;

END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `sign_in_family_AUPD`;
DELIMITER //
CREATE TRIGGER `sign_in_family_AUPD` AFTER UPDATE ON `sign_in_family`
 FOR EACH ROW BEGIN
UPDATE program_session SET count_adult = count_adult + (NEW.adult_count - OLD.adult_count), count_total = count_total + (NEW.adult_count - OLD.adult_count) WHERE id = NEW.program_session_id;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `sign_in_family_BDEL`;
DELIMITER //
CREATE TRIGGER `sign_in_family_BDEL` BEFORE DELETE ON `sign_in_family`
 FOR EACH ROW BEGIN
UPDATE program_session SET count_adult = count_adult - OLD.adult_count, count_total = count_total - OLD.adult_count WHERE id = OLD.program_session_id;

SET @programID = (
     SELECT PS.program_id
       FROM sign_in_family AS SF
       JOIN program_session AS PS ON SF.program_session_id = PS.id
      WHERE SF.id = OLD.id
      LIMIT 1
);

     DELETE SV
       FROM sign_in_family_first_visit AS SV
       JOIN (SELECT SF.id
               FROM sign_in_family AS SF
               JOIN program_session AS PS ON SF.program_session_id = PS.id
              WHERE PS.program_id = @programID
                AND SF.family_id = OLD.family_id)
         AS DEL
         ON SV.sign_in_family_id = DEL.id;

SET @programSessionID = (
     SELECT MIN(PS.id)
       FROM sign_in_family AS SF
       JOIN program_session AS PS ON SF.program_session_id = PS.id 
      WHERE PS.program_id = @programID
        AND SF.family_id = OLD.family_id
        AND SF.id != OLD.id
);

INSERT INTO sign_in_family_first_visit (sign_in_family_id, program_session_id)
     SELECT SF.id, SF.program_session_id
       FROM sign_in_family AS SF
      WHERE SF.program_session_id = @programSessionID
        AND SF.family_id = OLD.family_id;

END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sign_in_family_first_visit`
--

CREATE TABLE IF NOT EXISTS `sign_in_family_first_visit` (
  `sign_in_family_id` mediumint(8) unsigned NOT NULL,
  `program_session_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`sign_in_family_id`),
  KEY `program_session_id` (`program_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comments` text NOT NULL,
  `job_type` tinyint(1) unsigned NOT NULL COMMENT '1 = Director/Coordinator, 2 = Volunteer, 3 = Practicum Student',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `job_type` (`job_type`),
  KEY `last_name` (`last_name`),
  KEY `full_name` (`full_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Triggers `staff`
--
DROP TRIGGER IF EXISTS `staff_BINS`;
DELIMITER //
CREATE TRIGGER `staff_BINS` BEFORE INSERT ON `staff`
 FOR EACH ROW SET NEW.full_name = CONCAT_WS(' ', NEW.first_name, NEW.last_name)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `staff_BUPD`;
DELIMITER //
CREATE TRIGGER `staff_BUPD` BEFORE UPDATE ON `staff`
 FOR EACH ROW SET NEW.full_name = CONCAT_WS(' ', NEW.first_name, NEW.last_name)
//
DELIMITER ;
--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `first_name`, `last_name`, `email`, `comments`, `job_type`) VALUES
(1, 'admin', '', 'admin', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff_hours`
--

CREATE TABLE IF NOT EXISTS `staff_hours` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` mediumint(8) unsigned NOT NULL,
  `program_id` mediumint(8) unsigned NOT NULL,
  `date` date NOT NULL,
  `hours` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_id` (`staff_id`,`program_id`,`date`),
  KEY `program_id` (`program_id`),
  KEY `hours` (`hours`),
  KEY `date` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `translation`
--

CREATE TABLE IF NOT EXISTS `translation` (
  `lang_code` varchar(3) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `translation`
--

INSERT INTO `translation` (`lang_code`, `translation`) VALUES
('ara', '{\n    "lang_screen": {\n        "welcome": "ترحيب",\n        "select": "الرجاء تحديد اللغة"\n    },\n    "welcome_message": {\n        "title": "ترحيب",\n        "line1": "لدينا أسرة برنامج الموارد!",\n        "line2": "لم يكن لديك للإجابة على أي سؤال أن لم يكن لديك للرد.",\n        "line3": " سيتم الاحتفاظ بسرية تامة إجاباتك."\n    },\n    "your_information": {\n        "title": " المعلومات الخاصة بك",\n        "parent": "أصل",\n        "guardian_name": "ولي اسم",\n        "first_name": "الاسم الأول",\n        "last_name": "اسم العائلة",\n        "phone_number": "رقم الهاتف",\n        "email": "البريد الإلكتروني",\n        "first_lang": "اللغة القومية",\n        "cultural_bg": "بلد الميلاد"\n    },\n    "your_family": {\n        "message": "عائلتك",\n        "family_role": {\n            "role_in_family": "دورك في عائلتك",\n            "parent": "أصل",\n            "step_parent": "خطوة الوالد",\n            "grandparent": "جد",\n            "other": "آخر"\n        },\n        "parent_info": {\n            "partner": " شريك",\n            "husband": "زوج",\n            "wife": "زوجة",\n            "first_name": "الاسم الأول",\n            "last_name": "اسم العائلة"\n        },\n        "child_info": {\n            "child": "طفل",\n            "first_name": "الاسم الأول",\n            "last_name": "اسم العائلة",\n            "birth_date": "تاريخ الميلاد",\n            "rm_child": "إزالة الطفل",\n            "add_child": "إضافة طفل آخر",\n            "allergies": "الحساسية"\n        }\n    },\n    "emergency_contact": {\n        "title": "الاتصال في حالات الطوارئ",\n        "relationship": {\n            "title": "علاقة",\n            "partner": " شريك",\n            "husband": "زوج",\n            "wife": "زوجة",\n            "step_parent": "خطوة الوالد",\n            "grandparent": "جد",\n            "relative": "نسبي",\n            "friend": "صديق",\n            "family_friend": "عائلة صديق"\n        },\n        "first_name": "الاسم الأول",\n        "last_name": "اسم العائلة",\n        "phone_number": "رقم الهاتف"\n    },\n    "extra_info": {\n        "title": "معلومات إضافية",\n        "postal_code": "ما هي أول 3 أرقام من الرمز البريدي الخاص بك؟？",\n        "hear_program": {\n            "title": " كيف سمعت عن برنامجنا؟？",\n            "select_values": [\n                {"id": 1, "text": "عائلة الأعضاء"},\n                {"id": 2, "text": "صديق"},\n                {"id": 3, "text": "صحيفة"},\n                {"id": 4, "text": "زميل في العمل"},\n                {"id": 5, "text": "إعلان"},\n                {"id": 6, "text": "الإنترنت"}\n            ]\n        },\n        "start_program": "متى بدء تشغيل أول حضور البرامج هنا؟？"\n    },\n    "disclaimer": {\n        "title": "اتفاقية",\n        "line0": "وأنا أفهم أن مشاركتي في خدمة المجتمع خيارات سرية.",\n        "line1": "لن المعلومات المتعلقة نفسي أو عائلتي أن تكون مشتركة من فريق خدمة المجتمع ما عدا خيارات على النحو المبين في شكل حدود السرية، دون موافقتي.",\n        "sign_in": " تسجيل الدخول"\n    },\n    "error": {\n        "invalid_field": "يرجى ملء هذا المجال",\n        "unchecked_box": "يرجى التحقق من هذا المربع إذا كنت ترغب في المضي قدما",\n        "invalid_email_exists": "عنوان البريد الإلكتروني غير صحيح",\n        "invalid_email_used": "البريد الإلكتروني المستخدمة بالفعل", \n        "invalid_phone": "رقم الهاتف قيد الاستخدام",\n        "invalid_format": "تنسيق غير صالح"\n    }\n}'),
('eng', '{\n    "lang_screen": {\n        "welcome": "Welcome",\n        "select": "Please select your language"\n    },\n    "welcome_message": {\n        "title": "Welcome",\n        "line1": "to our Family Resource Program!",\n        "line2": "You don’t have to answer any question that you don’t want to answer.",\n        "line3": "Your answers will be kept strictly confidential."\n    },\n    "your_information": {\n        "title": "Your Information",\n        "parent": "Parent",\n        "guardian_name": "Guardian Name",\n        "first_name": "First Name",\n        "last_name": "Last Name",\n        "phone_number": "Phone Number",\n        "email": "Email",\n        "first_lang": "First Language",\n        "cultural_bg": "Country of Birth"\n    },\n    "your_family": {\n        "message": "Your Family",\n        "family_role": {\n            "role_in_family": "Your role in your family",\n            "parent": "Parent",\n            "step_parent": "Step-Parent",\n            "grandparent": "Grandparent",\n            "other": "Other"\n        },\n        "parent_info": {\n            "partner": "Partner",\n            "husband": "Husband",\n            "wife": "Wife",\n            "first_name": "First Name",\n            "last_name": "Last Name"\n        },\n        "child_info": {\n            "child": "Child",\n            "first_name": "First Name",\n            "last_name": "Last Name",\n            "birth_date": "Birth date",\n            "rm_child": "Remove child",\n            "add_child": "Add another child",\n            "allergies": "Allergies"\n        }\n    },\n    "emergency_contact": {\n        "title": "Emergency contact",\n        "relationship": {\n            "title": "Relationship",\n            "partner": "Partner",\n            "husband": "Husband",\n            "wife": "Wife",\n            "step_parent": "Step-Parent",\n            "grandparent": "Grandparent",\n            "relative": "Relative",\n            "friend": "Friend",\n            "family_friend": "Family Friend"\n        },\n        "first_name": "First Name",\n        "last_name": "Last Name",\n        "phone_number": "Phone Number"\n    },\n    "extra_info": {\n        "title": "Additional information",\n        "postal_code": "What are the first 3 digits of your postal code?",\n        "hear_program": {\n            "title": "How did you hear about our program?",\n            "select_values": [\n                {\n                    "id": 1,\n                    "text": "Family Member"\n                },\n                {\n                    "id": 2,\n                    "text": "Friend"\n                },\n                {\n                    "id": 3,\n                    "text": "Newspaper"\n                },\n                {\n                    "id": 4,\n                    "text": "Co-worker"\n                },\n                {\n                    "id": 5,\n                    "text": "Advertisement"\n                },\n                {\n                    "id": 6,\n                    "text": "Internet"\n                }\n            ]\n        },\n        "start_program": "When did you first start attending programs here?"\n    },\n    "disclaimer": {\n        "title": "Agreement",\n        "line0": "I understand that my involvement at Options Community Services is confidential. ",\n        "line1": "Information regarding myself or my family will not be shared out of the Options Community Services team except as outlined in the limits of confidentiality form, without my consent.",\n        "sign_in": "Sign In"\n    },\n    "error": {\n        "invalid_field": "Please fill out this field",\n        "unchecked_box": "Please check this box if you wish to proceed",\n        "invalid_email_exists": "Not a valid email address",\n        "invalid_email_used": "Email already in use",  \n        "invalid_phone": "The phone number is in use",\n        "invalid_format": "Invalid format"\n    }\n}'),
('pan', '{\n    "lang_screen": {\n        "welcome": "ਤੁਹਾਡਾ ਸਵਾਗਤ ਹੈ",\n        "select": "ਕ੍ਰਿਪਾ ਆਪਣੀ ਭਾਸ਼ਾ ਦਾ ਸੰਗ੍ਰਹਿ ਕਰੋਏ"\n    },\n    "welcome_message": {\n        "title": "ਤੁਹਾਡਾ ਸਵਾਗਤ ਹੈ",\n        "line1": "ਸਾਡੇ ਫੈਮਿਲੀ ਰਿਸੋਰਸ ਪਰੋਗਰਾਮ !",\n        "line2": "ਤੁਸੀ ਕਿਸੇ ਵੀ ਸਵਾਲ ਦਾ ਜਵਾਬ ਹੈ ਕਿ ਤੁਸੀ ਜਵਾਬ ਨਹੀਂ ਹੈ ਨਹੀਂ ਹੈ .",\n        "line3": "ਤੁਹਾਡਾ ਜਵਾਬ ਪੂਰੀ ਤਰ੍ਹਾਂ ਵਲੋਂ ਗੁਪਤ ਰੱਖਿਆ ਜਾਵੇਗਾ ."\n    },\n    "your_information": {\n        "title": "ਤੁਹਾਡੀ ਜਾਣਕਾਰੀ",\n        "parent": "ਮਾਤਾ  -  ਪਿਤਾ",\n        "guardian_name": "ਗਾਰਜਿਅਨ ਨਾਮ",\n        "first_name": "ਪਹਿਲਾਂ ਨਾਮ",\n        "last_name": "ਸਰਨੇਮ",\n        "phone_number": "ਫੋਨ ਨੰਬਰ",\n        "email": "ਈਮੇਲ",\n        "first_lang": "ਮਾਤ ਭਾਸ਼ਾ",\n        "cultural_bg": "ਸਾਂਸਕ੍ਰਿਤੀਕ ਪ੍ਰਸ਼ਠਭੂਮੀ"\n    },\n    "your_family": {\n        "message": "ਆਪਣੇ ਪਰਵਾਰ  ਦੇ ",\n        "family_role": {\n            "role_in_family": "ਆਪਣੇ ਪਰਵਾਰ ਵਿੱਚ ਤੁਹਾਡੀ ਭੂਮਿਕਾ",\n            "parent": "ਮਾਤਾ  -  ਪਿਤਾ",\n            "step_parent": "ਸਟੇਪ  -  ਮਾਤਾ  -  ਪਿਤਾ",\n            "grandparent": "ਗ੍ਰਾਨ੍ਦ੍ਪਾਰੇੰਟ",\n            "other": "ਹੋਰ"\n        },\n        "parent_info": {\n            "partner": "ਸਾਥੀ",\n            "husband": "ਪਤੀ",\n            "wife": "ਪਤਨੀ",\n            "first_name": "ਪਹਿਲਾਂ ਨਾਮ",\n            "last_name": "ਸਰਨੇਮ"\n        },\n        "child_info": {\n            "child": "ਬੱਚਾ",\n            "first_name": "ਪਹਿਲਾਂ ਨਾਮ",\n            "last_name": "ਸਰਨੇਮ",\n            "birth_date": "ਜਨਮ ਤਾਰੀਖ",\n            "rm_child": "ਬੱਚੇ ਕੱਢੀਏ",\n            "add_child": "ਇੱਕ ਅਤੇ ਬੱਚੇ ਨੂੰ ਜੋੜੇਂ",\n            "allergies": "ਏਲਰਜੀ"\n        }\n    },\n    "emergency_contact": {\n        "title": "ਆਪਾਤਕਾਲੀਨ ਸੰਪਰਕ",\n        "relationship": {\n            "title": "ਰਿਸ਼ਤਾ",\n            "partner": "ਸਾਥੀ",\n            "husband": "ਪਤੀ",\n            "wife": "ਪਤਨੀ",\n            "step_parent": "ਸਟੇਪ  -  ਮਾਤਾ  -  ਪਿਤਾ",\n            "grandparent": "ਗ੍ਰਾਨ੍ਦ੍ਪਾਰੇੰਟ",\n            "relative": "ਸਾਪੇਖ",\n            "friend": "ਦੋਸਤ",\n            "family_friend": "ਪਰਵਾਰਿਕ ਮਿੱਤਰ"\n        },\n        "first_name": "ਪਹਿਲਾਂ ਨਾਮ",\n        "last_name": "ਸਰਨੇਮ",\n        "phone_number": "ਫੋਨ ਨੰਬਰ"\n    },\n    "extra_info": {\n        "title": "ਇਲਾਵਾ ਸੂਚਨਾ",\n        "postal_code": "ਆਪਣੇ ਡਾਕ ਕੋਡ  ਦੇ ਪਹਿਲੇ 3 ਅੰਕ ਕੀ ਹਨ ? ",\n        "hear_program": {\n            "title": "ਤੁਸੀ ਕਿਵੇਂ ਸਾਡੇ ਪਰੋਗਰਾਮ  ਦੇ ਬਾਰੇ ਵਿੱਚ ਸੁਣਿਆ ਸੀ ? ",\n            "select_values": [\n                 {"id": 1, "text": "ਫੈਮਿਲੀ"},\n                 {"id": 2, "text": "ਦੋਸਤ"},\n                 {"id": 3, "text": "ਅਖ਼ਬਾਰ"},\n                 {"id": 4, "text": "ਕੋਮ੍ਪਾਨੀ ਵੋਰ੍ਕੇਰ"},\n                 {"id": 5, "text": "ਅਦ੍ਵੇਰ੍ਤੀਸ੍ਮੇੰਟ"},\n                 {"id": 6, "text": "ਇੰਟਰਨੇਟ"}\n            ]\n        },\n        "start_program": "ਜਦੋਂ ਤੁਸੀ ਪਹਿਲੀ ਵਾਰ ਇੱਥੇ ਪ੍ਰੋਗਰਾਮਾਂ ਵਿੱਚ ਭਾਗ ਲੈਣ ਸ਼ੁਰੂ ਕੀਤਾ ਸੀ ?"\n    },\n    "disclaimer": {\n        "title": "ਸਮੱਝੌਤਾ",\n        "line0": "ਮੈਂ ਸੱਮਝਦਾ ਹਾਂ ਕਿ ਵਿਕਲਪ ਸਮੁਦਾਇਕ ਸੇਵਾ ਵਿੱਚ ਆਪਣੀ ਭਾਗੀਦਾਰੀ  ਦੇ ਗੁਪਤ ਹੈ .",\n        "line1": "विकल्प सामुदायिक सेवा को छोड़कर टीम रूप में गोपनीयता फार्म के सीमा में उल्लिखित के बाहर खुद को या अपने परिवार के बारे में जानकारी साझा नहीं किया मेरी सहमति के बिना किया जाएगा.",\n        "sign_in": "ਵਿੱਚ ਸਾਇਨ ਇਸ ਕਰੋਏ"\n    },\n    "error": {\n        "invalid_field": "ਕ੍ਰਿਪਾ ਇਸ ਖੇਤਰ ਭਰੀਏ",\n        "unchecked_box": "ਕ੍ਰਿਪਾ ਇਸ ਬਾਕਸ ਨੂੰ ਚੇਕ ਕਰੀਏ ਜੇਕਰ ਤੁਸੀ ਅੱਗੇ ਵਧਨਾ ਚਾਹੁੰਦੇ ਹੋ",\n        "invalid_email_exists": "ਈ ਮੇਲ ਪਤਾ ਗਲਤ ਹੈ", \n        "invalid_email_used": "ਈਮੇਲ ਪਹਿਲਾਂ ਵਲੋਂ ਹੀ ਇਸਤੇਮਾਲ ਕੀਤਾ", \n        "invalid_phone": "ਪਹਿਲਾਂ ਵਲੋਂ ਹੀ ਵਰਤੋ ਵਿੱਚ ਟੇਲੀਫੋਨ ਨੰਬਰ",\n        "invalid_format": "ਗ਼ੈਰਕਾਨੂੰਨੀ ਪ੍ਰਾਰੂਪ"\n    }\n}'),
('spa', '{\n    "lang_screen": {\n        "welcome": "Bienvenido",\n        "select": "Por favo seleccione su idioma"\n    },\n    "welcome_message": {\n        "title": "Bienvenido",\n        "line1": "a nuestro Programa de Recursos para la Familia!",\n        "line2": "Usted no tiene que contestar ninguna pregunta que no tiene respuesta.",\n        "line3": "Sus respuestas se mantendrán estrictamente confidenciales."\n    },\n    "your_information": {\n        "title": "Su información",\n        "parent": "Padre",\n        "guardian_name": "Tutor",\n        "first_name": "Primer Nombre",\n        "last_name": "Apellido",\n        "phone_number": "Número de Teléfono",\n        "email": "Email",\n        "first_lang": "Primera Lengua",\n        "cultural_bg": "País de Nacimiento"\n    },\n    "your_family": {\n        "message": "Su Familia",\n        "family_role": {\n            "role_in_family": "Su papel en la familia",\n            "parent": "Padre",\n            "step_parent": "Padrastro",\n            "grandparent": "Abuelo",\n            "other": "Otro"\n        },\n        "parent_info": {\n            "partner": "Socio",\n            "husband": "Marido",\n            "wife": "Esposa",\n            "first_name": "Primer Nombre",\n            "last_name": "Apellido"\n        },\n        "child_info": {\n            "child": "Niño",\n            "first_name": "Primer Nombre",\n            "last_name": "Apellido",\n            "birth_date": "Fecha de nacimiento",\n            "rm_child": "Saque al niño",\n            "add_child": "Añadir otro niño",\n            "allergies": "Alergias"\n        }\n    },\n    "emergency_contact": {\n        "title": "Contacto de Emergencia",\n        "relationship": {\n            "title": "relación",\n            "partner": "Socio",\n            "husband": "Marido",\n            "wife": "Esposa",\n            "step_parent": "Padrastro",\n            "grandparent": "Abuelo",\n            "relative": "Relativo",\n            "friend": "Amigo",\n            "family_friend": "familia amigo"\n        },\n        "first_name": "Primer Nombre",\n        "last_name": "Apellido",\n        "phone_number": "Número de Teléfono"\n    },\n    "extra_info": {\n        "title": "Información Adicional",\n        "postal_code": "¿Cuáles son los 3 primeros dígitos de su código postal?",\n        "hear_program": {\n            "title": "¿Cómo se enteró de nuestro programa?",\n            "select_values": [\n                {\n                    "id": 1,\n                    "text": "Familia"\n                },\n                {\n                    "id": 2,\n                    "text": "Amigo"\n                },\n                {\n                    "id": 3,\n                    "text": "Periódico"\n                },\n                {\n                    "id": 4,\n                    "text": "Compañero de trabajo"\n                },\n                {\n                    "id": 5,\n                    "text": "Anuncio"\n                },\n                {\n                    "id": 6,\n                    "text": "El Internet"\n                }\n            ]\n        },\n        "start_program": "¿Cuándo fue la primera vez que empezar a asistir a programas aquí?"\n    },\n    "disclaimer": {\n        "title": "Acuerdo",\n        "line0": "Yo entiendo que mi participación en los Servicios de opciones comunitarias es confidencial.",\n        "line1": "La información sobre mí o mi familia no será compartida fuera de la Comunidad Servicios Opciones equipo excepto como se indica en los límites de forma confidencial, sin mi consentimiento.",\n        "sign_in": "Registrarse"\n    },\n    "error": {\n        "invalid_field": "Por favor llene este campo",\n        "unchecked_box": "Por favor marque esta casilla si desea continuar",\n        "invalid_email_exists": "Dirección de correo electrónico incorrecta",\n        "invalid_email_used": "Email ya utiliza", \n        "invalid_phone": "Número de teléfono ya en uso",\n        "invalid_format": "Formato no válido"\n    }\n}'),
('zhc', '{\n    "lang_screen": {\n        "welcome": "欢迎光临",\n        "select": "请选择你的语言"\n    },\n    "welcome_message": {\n        "title": "欢迎 ",\n        "line1": "我们的家庭资源计划",\n        "line2": "您无需回答任何您不想回答的问题。",\n        "line3": "您的回答将被严格保密."\n    },\n    "your_information": {\n        "title": "您的信息",\n        "parent": "父亲／母亲",\n        "guardian_name": "监护人 姓名",\n        "first_name": "名字",\n        "last_name": "姓",\n        "phone_number": "电话号码",\n        "email": "邮箱",\n        "first_lang": "母语",\n        "cultural_bg": " 出生国家"\n    },\n    "your_family": {\n        "message": "你的家庭",\n        "family_role": {\n            "role_in_family": "您的家庭角色",\n            "parent": "父母",\n            "step_parent": "继父母",\n            "grandparent": "祖父母或外祖父母",\n            "other": "其他"\n        },\n        "parent_info": {\n            "partner": "合伙人",\n            "husband": "丈夫",\n            "wife": "妻子",\n            "first_name": "名字",\n            "last_name": "姓"\n        },\n        "child_info": {\n            "child": "孩子",\n            "first_name": "名字",\n            "last_name": "姓",\n            "birth_date": "出生日期",\n            "rm_child": "删除",\n            "add_child": "增加其他子女",\n            "allergies": "过敏症"\n        }\n    },\n    "emergency_contact": {\n        "title": "紧急联络人",\n        "relationship": {\n            "title": "关系",\n            "partner": "合伙人",\n            "husband": "丈夫",\n            "wife": "妻子",\n            "step_parent": "继父母 ",\n            "grandparent": "祖父母或外祖父母",\n            "relative": "相对的",\n            "friend": "朋友",\n            "family_friend": "家人朋友"\n        },\n        "first_name": "名字",\n            "last_name": "姓",\n        "phone_number": "电话号码"\n    },\n    "extra_info": {\n        "title": "其他信息",\n        "postal_code": "您的邮政编码的前3位的是什么？",\n        "hear_program": {\n            "title": "您是如何得知我们的活动的？",\n            "select_values": [\n                {\n                    "id": 1,\n                    "text": "家属"\n                },\n                {\n                    "id": 2,\n                    "text": "朋友"\n                },\n                {\n                    "id": 3,\n                    "text": "报纸"\n                },\n                {\n                    "id": 4,\n                    "text": "同事"\n                },\n                {\n                    "id": 5,\n                    "text": "广告"\n                },\n                {\n                    "id": 6,\n                    "text": "因特网"\n                }\n            ]\n        },\n        "start_program": "您第一次在这参加活动是什么时候？ "\n    },\n    "disclaimer": {\n        "title": "协议",\n        "line0": "我明白此次在“选项团体服务”的活动是被保密的.",\n        "line1": "有关我本人及我家人不会被共享出来的“选项”社区服务队除了所列的保密形式的限制，在未经过我本人同意的情况下，我本人及我家人的信息是不会被''''选项团体服务”的工作组共享，除非它超过了机密性的界限.",\n        "sign_in": "登录"\n    },\n    "error": {\n        "invalid_field": "请在此输入",\n        "unchecked_box": "如果你想继续，请选中此复选框",\n        "invalid_email_exists": "不是一个有效的电子邮件地址", \n        "invalid_email_used": "电子邮件已经使用", \n        "invalid_phone": "在使用的电话号码是",\n        "invalid_format": "无效的格式"\n    }\n}'),
('zht', '{\n    "lang_screen": {\n        "welcome": "歡迎",\n        "select": "請選擇您的語言"\n    },\n    "welcome_message": {\n        "title": "歡迎",\n        "line1": "我們的家庭資源計劃！",\n        "line2": "你不必回答任何問題，你沒有回答。",\n        "line3": "您的回答將被嚴格保密。"\n    },\n    "your_information": {\n        "title": "您的信息",\n        "parent": "親",\n        "guardian_name": "監護人姓名",\n        "first_name": "名字",\n        "last_name": "姓",\n        "phone_number": "電話號碼",\n        "email": "電子郵件",\n        "first_lang": "第一語言",\n        "cultural_bg": "出生國家"\n    },\n    "your_family": {\n        "message": "你的家庭",\n        "family_role": {\n            "role_in_family": "你的家人在你的角色",\n            "parent": "親",\n            "step_parent": "繼父母",\n            "grandparent": "祖父母或外祖父母",\n            "other": "其他"\n        },\n        "parent_info": {\n            "partner": "合夥人",\n            "husband": "丈夫",\n            "wife": "妻子",\n            "first_name": "名字",\n            "last_name": "姓"\n        },\n        "child_info": {\n            "child": "孩子",\n            "first_name": "名字",\n            "last_name": "姓",\n            "birth_date": "出生日期",\n            "rm_child": "刪除子",\n            "add_child": "另一個孩子",\n            "allergies": "過敏"\n        }\n    },\n    "emergency_contact": {\n        "title": "緊急聯絡人",\n        "relationship": {\n            "title": "關係",\n            "partner": "合夥人",\n            "husband": "丈夫",\n            "wife": "妻子",\n            "step_parent": "繼父母",\n            "grandparent": "祖父母或外祖父母",\n            "relative": "相對的",\n            "friend": "朋友",\n            "family_friend": "家人朋友"\n        },\n        "first_name": "名字",\n        "last_name": "姓",\n        "phone_number": "電話號碼"\n    },\n    "extra_info": {\n        "title": "其他信息",\n        "postal_code": "您的郵政編碼的前3位的是什麼？",\n        "hear_program": {\n            "title": "您是如何得知我們的節目嗎？",\n            "select_values": [\n                 {"id": 1, "text": "家庭"},\n                 {"id": 2, "text": "朋友"},\n                 {"id": 3, "text": "報紙"},\n                 {"id": 4, "text": "同事"},\n                 {"id": 5, "text": "廣告"},\n                 {"id": 6, "text": "網際網路"}\n            ]\n        },\n        "start_program": "當你第一次開始參加計劃在這裡嗎？"\n    },\n    "disclaimer": {\n        "title": "協議",\n        "line0": "據我所知，我在選項參與社區服務是保密的。",\n        "line1": "有關我本人或我的家人不會被共享出來的“選項”社區服務隊除了所列的保密形式的限制，沒有經過我的同意。",\n        "sign_in": "登錄"\n    },\n    "error": {\n        "invalid_field": "請在此輸入",\n        "unchecked_box": "如果你想繼續，請選中此複選框",\n        "invalid_email_exists": "不正確的電子郵件地址",\n        "invalid_email_used": "電子郵件已經使用", \n        "invalid_phone": "已在使用中的電話號碼",\n        "invalid_format": "無效的格式"\n    }\n}');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `staff_id` mediumint(8) unsigned NOT NULL,
  `pass_hash` varchar(512) NOT NULL,
  `pass_salt` varchar(39) NOT NULL,
  `permission_level` tinyint(1) unsigned NOT NULL COMMENT '0 = Director, 1 = Coordinator',
  PRIMARY KEY (`staff_id`),
  KEY `permission_level` (`permission_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`staff_id`, `pass_hash`, `pass_salt`, `permission_level`) VALUES
(1, '$2a$13$00795b928365210c9a892uGAyF4Nf72rI69qr1qWcfNs6U5dXZ7xW', '$2a$13$00795b928365210c9a8920e4318992a8', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_ibfk_1` FOREIGN KEY (`family_id`) REFERENCES `family` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `family`
--
ALTER TABLE `family`
  ADD CONSTRAINT `family_ibfk_1` FOREIGN KEY (`hear_about_us`) REFERENCES `hear_about_us` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `family_ibfk_2` FOREIGN KEY (`guardian_origin_country`) REFERENCES `country` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `family_ibfk_3` FOREIGN KEY (`guardian_first_lang`) REFERENCES `language` (`lang_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `guest_speaker`
--
ALTER TABLE `guest_speaker`
  ADD CONSTRAINT `guest_speaker_ibfk_1` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_session`
--
ALTER TABLE `program_session`
  ADD CONSTRAINT `program_session_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_staff`
--
ALTER TABLE `program_staff`
  ADD CONSTRAINT `program_staff_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `user` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `program_staff_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referral`
--
ALTER TABLE `referral`
  ADD CONSTRAINT `referral_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_referral`
--
ALTER TABLE `session_referral`
  ADD CONSTRAINT `session_referral_ibfk_1` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_referral_ibfk_2` FOREIGN KEY (`referral_id`) REFERENCES `referral` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_resource`
--
ALTER TABLE `session_resource`
  ADD CONSTRAINT `session_resource_ibfk_1` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_resource_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sign_in_child`
--
ALTER TABLE `sign_in_child`
  ADD CONSTRAINT `sign_in_child_ibfk_1` FOREIGN KEY (`sign_in_family_id`) REFERENCES `sign_in_family` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sign_in_child_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `child` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sign_in_child_first_visit`
--
ALTER TABLE `sign_in_child_first_visit`
  ADD CONSTRAINT `sign_in_child_first_visit_ibfk_1` FOREIGN KEY (`sign_in_family_id`, `child_id`) REFERENCES `sign_in_child` (`sign_in_family_id`, `child_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sign_in_child_first_visit_ibfk_2` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sign_in_family`
--
ALTER TABLE `sign_in_family`
  ADD CONSTRAINT `sign_in_family_ibfk_1` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sign_in_family_ibfk_2` FOREIGN KEY (`family_id`) REFERENCES `family` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sign_in_family_first_visit`
--
ALTER TABLE `sign_in_family_first_visit`
  ADD CONSTRAINT `sign_in_family_first_visit_ibfk_1` FOREIGN KEY (`sign_in_family_id`) REFERENCES `sign_in_family` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sign_in_family_first_visit_ibfk_2` FOREIGN KEY (`program_session_id`) REFERENCES `program_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff_hours`
--
ALTER TABLE `staff_hours`
  ADD CONSTRAINT `staff_hours_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `staff_hours_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
