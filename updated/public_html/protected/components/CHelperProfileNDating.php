<?php


class CHelperProfileNDating
{
	static $fieldDefine =	array(
		'body_type' => array('group' => 'basic', 'text' => 'Body Type', 
			'values' => array("Heavyset","A Few Extra Pounds","Curvy","Stocky","About Average","Slender","Athletic And Toned","Other",)),
		'drinking' => array('group' => 'basic', 'text' => 'Drinking', 
			'values' => array("Never","Occasionally","Daily",)),
		'eye_color' => array('group' => 'basic', 'text' => 'Eye Colour', 
			'values' => array("Blue","Grey","Brown","Hazel","Green","Black","Other",)),
		//n: 2012 07 22
		'education' => array('group' => 'basic', 'text' => 'Education', 
			'values' => array('Aquarius','Aries','Cancer','Capricorn','Gemini','Leo','Libra','Pisces','Sagittarius','Scorpio','Taurus','Virgo',)),
				
		'hair_color' => array('group' => 'basic', 'text' => 'Hair Colour', 
			'values' => array("Black","Dark Brown","Light Brown","Brown","Dark Blonde","Blonde","Light Blonde","Auburn/Red","Salt And Pepper","Silver",
				"Platinum","Grey","Other",)),
		'interests' => array('group' => 'basic', 'text' => 'Interests', 
			'values' => array("Singing/playing","Instrument","Camping","Cars","Coffee","Conversation","Computers","Cooking","Dancing","Dining",
				"Out Drawing","Fishing/hunting","Gardening/landscaping","Hobbies And Crafts","Internet","Museums/exhibits","Music","Others","Performing","Arts",
				"Photography","Playing","Cards","Sports","Political","Interests","Book","Club/discussion","Religion/spiritual","Antiques",
				"Shopping","Television","The Arts","The Outdoors","Theater","Travel/sightseeing","Video","Games","Volunteering","Watching Sports",
				"Wine","Tasting","Writing","University","Friends","Business","Networking","Movies/Videos",)),
		'looking_for' => array('group' => 'basic', 'text' => 'Looking For', 
			'values' => array("Sexual","Encounter","Threesome/Group","Talk/Email","Webcam/Flirt/Pics","Open To Relationship",)),
		
		//n: 2012 07 22
		'occupation' => array('group' => 'basic', 'text' => 'Occupation', 
			'values' => array("Administrative/secretarial","Architecture/interior Design","Executive/management","Fashion/model/beauty","Financial Services","Labour/construction","Legal","Medical/dental/veterinary","Political/govt/civil Service/military","Retail/food Services",
				"Retired","Sales/marketing","Self Employed","Student","Teacher/professor","Technical/computers/engineering","Travel/hospitality/transportation","Other Profession",)),
		
		'personality' => array('group' => 'basic', 'text' => 'Personality', 
			'values' => array("Easy","Going","Reserved","Shy","Stubborn","Energetic","Enthusiastic","Adventurous","Quiet","Helpful",
				"Generous","Spontaneous","Confident","Superstitious","Thoughtful","Carefree","Sociable","High Maintenence","Sensitive","Possessive",
				"Funny","Reliable","Reflective","Other",)),
		'relationship_status' => array('group' => 'basic', 'text' => 'Relationship status', 
			'values' => array("Currently Separated","Divorced","Widowed","Single","Married","In A Relationship",)),
		'smoking' => array('group' => 'basic', 'text' => 'Smoking', 
			'values' => array("No Way","Occasionally","Cigars","Trying To Quit","Daily",)),
		'anal' => array('group' => 'lifestyle', 'text' => 'Anal', 
			'values' => array("Giving","Receiving","Both","Neither",)),
		'experience' => array('group' => 'lifestyle', 'text' => 'Experience', 
			'values' => array("Very Experienced","Experienced","Need Some Practice","Inexperienced",)),
		'live' => array('group' => 'lifestyle', 'text' => 'Live...', 
			'values' => array("Live Alone","Live With Kids","Live With Parents","Live With Roommate(s)","Live With Partner",)),
		'income' => array('group' => 'lifestyle', 'text' => 'Income', 
			'values' => array("Less Than 25.000","25.001 To 35.000","35.001 To 50.000","50.001 To 75.000","75.001 To 100.000","100.001 To 150.000","More Than 150.000",)),
		'kinky' => array('group' => 'lifestyle', 'text' => 'Kinky', 
			'values' => array("Very Kinky","Kinky","Not Very Kinky","Not Kinky At All",)),

		'oral' => array('group' => 'lifestyle', 'text' => 'Oral', 
			'values' => array("Giving","Receiving","Both","Neither",)),
		'religion' => array('group' => 'lifestyle', 'text' => 'Religion', 
			'values' => array("Agnostic","Atheist","Buddhist/Taoist","Christian / Catholic","Christian / Protestant","Hindu","Jewish","Muslim / Islam","None","Other",
				"Shintu","Sikh","Spiritual But Not Religious","Christian / Other","Christian / LDS",)),
		'1st_language' => array('group' => 'profiles', 'text' => '1st language', 
			'values' => array("English","Chinese","Spanish","Japanese","German","French","Korean","Italian","Portuguese","Russian",
				"Dutch","Malay","Arabic","Polish","Swedish","Thai","Turkish","Vietnamese","Persian","Romanian",
				"Czech","Hebrew","Danish","Finnish","Hungarian","Greek","Catalan","Norwegian","Slovak","Serbo-Croatian",
				"Ukrainian","Punjabi","Slovene","Icelandic","Other",)),
		'2nd_language' => array('group' => 'profiles', 'text' => '2nd language', 
			'values' => array("English","Chinese","Spanish","Japanese","German","French","Korean","Italian","Portuguese","Russian",
				"Dutch","Malay","Arabic","Polish","Swedish","Thai","Turkish","Vietnamese","Persian","Romanian",
				"Czech","Hebrew","Danish","Finnish","Hungarian","Greek","Catalan","Norwegian","Slovak","Serbo-Croatian",
				"Ukrainian","Punjabi","Slovene","Icelandic","Other",)),
		
		//n: 2012 07 22
		'glasses' => array('group' => 'profiles', 'text' => 'Glasses', 
			'values' => array('Contact Lenses','No','Occasionally','Yes',)),
		
		//n: 2012 07 22
		'startsign' => array('group' => 'profiles', 'text' => 'Start Sign', 
			'values' => array('Aquarius','Aries','Cancer','Capricorn','Gemini','Leo','Libra','Pisces','Sagittarius','Scorpio','Taurus','Virgo',)),
		
		//'age' => array('group' => 'profiles', 'text' => 'Age', 
		//	'values' => array("19","20","21","22","23","24","25","26","27","28",
		//		"29","30","31","32","33","34","35","36","37","38",
		//		"39","40","41","42","43","44","45","46","47","48",
		//		"49","50","51","52","53","54","55","56","57","58",
		//		"59","60","61","62","63","64","65","66","67","68",
		//		"69","70","71","72","73","74","75","76","77","78",
		//		"79","80","81","82","83","84","85","86","87","88",
		//		"89","90","91","92","93","94","95","96","97","98",
		//		"99",)),
		'appearance' => array('group' => 'profiles', 'text' => 'Appearance', 
			'values' => array("Very Attractive","Attractive","Average",)),
		'ethnicity' => array('group' => 'profiles', 'text' => 'Ethnicity', 
			'values' => array("White / Caucasian","Black / African Descent","Middle Eastern","Asian","Latino / Hispanic","Other","East Indian","Native American","Mixed Race","Mediterranean",
				"Latin-american","Pacific Islander",)),
		'hair_length' => array('group' => 'profiles', 'text' => 'Hair length', 
			'values' => array("Shaved","Very Short","Short","Shoulder-length","Long","Balding",)),
		'height' => array('group' => 'profiles', 'text' => 'Height', 
			'values' => array("Less Than 4′7″","4′7″","4′8″","4′9″","4′10″","4′11″","5′0″","5′1″","5′2″","5′3″",
				"5′4″","5′5″","5′6″","5′7″","5′8″","5′9″","5′10″","5′11″","6′0″","6′1″",
				"6′2″","6′3″","6′4″","6′5″","6′6″","6′7″","More Than 6′7″",)),
		'best_feature' => array('group' => 'profiles', 'text' => 'Best feature', 
			'values' => array("Arms","Belly Button","Ears","Bum","Chest","Breasts","Eyes","Feet","Hair","Hands",
				"Legs","Lips","Muscles","Neck","Calves","Smile","A Sweet Spot Not On The List",)),
		'piercings_tattoos' => array('group' => 'profiles', 'text' => 'Piercings & tattoos', 
			'values' => array("None","Visible Tattoo","Strategically Placed Tattoo","Pierced Ear(s)","Belly Button Ring","Genital Piercing","Other",)),
		'nationality' => array('group' => 'profiles', 'text' => 'Nationality', 
			'values' => array("British","Irish","Afghan","Albanian","Algerian","American","Andorran","Angolian","Antiguan/Barbudian","Argentinian",
				"Armenian","Australian","Austrian","Azerbaijanian","Bahamian","Bahraini","Bangladeshi","Barbadian","Basotho","Belarusian",
				"Belgian","Belizean","Beninese","Bhutanese","Bolivian","Bosnian","Botswanan","Brazilian","Bruneian","Bulgarian",
				"Burkinabe","Burmese","Burundian","Cambodian","Cameroonian","Canadian","Cape Verdean","Central African","Chadian","Chilean",
				"Chinese","Colombian","Comorian","Congolese (DRC)","Congolese (ROC)","Costa Rican","Croatian","Cuban","Cypriot","Czech",
				"Danish","Djiboutian","Dominican","Dutch","East Timorese","Ecuadorian","Egyptian","Emirian","Equatorial Guinean","Eritrean",
				"Estonian","Ethiopian","Fijian","Filipino","Finnish","French","Gabonese","Gambian","Georgian","German",
				"Ghanaian","Greek","Grenadian","Guatemalian","Guinea-Bissau","Guinean","Guyanian","Haitian","Honduran","Hong Kong",
				"Hungarian","Icelandic","Indian","Indonesian","Iranian","Iraqi","Israeli","Italian","Ivorian","Jamaican",
				"Japanese","Jordanian","Kazakh","Kenyan","Kiribatian","Kuwaiti","Kyrgyz","Lao","Malawian","Lebanese",
				"Liberian","Libyan","Liechtensteiner","Lithuanian","Luxembourger","Macanese","Macedonian","Madagascan","Malaysian","Maldivian",
				"Malian","Maltese","Marshallese","Mauritanian","Mauritian","Mexican","Micronesian","Moldovan","Monegasque","Mongolian",
				"Montenegrian","Morrocan","Mozambican","Nambian","Nauruan","Nepalese","New-Zealander","Nicaraguan","Nigerian","Nigerien",
				"North Korean","Norwegian","Omani","Pakistani","Palauan","Palestinian","Panamanian","Papua New Guinean","Paraguayan","Peruvian",
				"Polish","Portuguese","Puerto Rican","Qatari","Romanian","Russian","Rwandan","Sahrawi","Saint Lucian","Salvadorian",
				"Samoan","San Cristobalian","San Marinese","S?o Toméan","Saudi Arabian","Senegalese","Serbian","Seychellois","Sierra Leonean","Singaporean",
				"Slovakian","Slovenian","Solomonm Islander","Somalian","South African","South Korean","Spanish","Spratly Islander","Sri Lankan","Sudanese",
				"Surinamer","Swazi","Swedish","Swiss","Syrian","Taiwanian","Tajik","Tanzanian","Thai","Togolese",
				"Tongan","Trinidadian/Tobagonian","Tunisian","Turkish","Turkmen","Tuvaluan","Ugandan","Ukrainian","Uruguayan","Uzbek",
				"Vanuatuan","Venezuelan","Vietnamese","Vincentian","Yemeni","Zambian","Zimbabwean",)),
		'style' => array('group' => 'profiles', 'text' => 'Style', 
			'values' => array("Bohemian","Classical","Cool","Ethnic","Rock","Sophisticated","Sporty","Trendy","Other",)),
		);
	
	/**
	 * Note: multiple value fields are: interests, and looking_for
	 *
	 * @param mixed $group This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	static function &getItemsByGroup($group){
		$items = array();
		foreach(self::$fieldDefine as $key => $item){
			if($item['group'] ==$group){
				$items[$key] = $item;
			}
		}
		return $items;
	}
	
	
	static $age = array('group' => 'profiles', 'text' => 'Age', 
		'values' => array("18", "19","20","21","22","23","24","25","26","27","28",
			"29","30","31","32","33","34","35","36","37","38",
			"39","40","41","42","43","44","45","46","47","48",
			"49","50","51","52","53","54","55","56","57","58",
			"59","60","61","62","63","64","65","66","67","68",
			"69","70","71","72","73","74","75","76","77","78",
			"79","80","81","82","83","84","85","86","87","88",
			"89","90","91","92","93","94","95","96","97","98",
			"99",));
	
	static function getDatingItems(){
		$items = array();
		
		$items['age'] = self::$age;
		$items['age']['text'] = 'Min Age';
		
		$items['maxage'] = self::$age;
		$items['maxage']['text'] = 'Max Age';
		
		foreach(self::$fieldDefine as $key => $item){
			if($key == '1st_language'){
				$items['language'] = $item;
				$items['language']['text'] = 'Language';
			} else if($key == '2nd_language'){
				continue;
			} else {
				$items[$key] = $item;
			}
			if($key == 'income'){
				$items[$key]['text'] = 'Min Income';
				$items['maxincome'] = $item;
				$items['maxincome']['text'] = 'Max Income';
			}
			if($key == 'height'){
				$items[$key]['text'] = 'Min Height';
				$items['maxheight'] = $item;
				$items['maxheight']['text'] = 'Max Height';
			}
		}
		return $items;
	}	
		
}