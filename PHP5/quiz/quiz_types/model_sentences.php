<?php
sro('/PHP5/quiz/quiz_types.php');


global $quiz_types;
$quiz_types = array_merge($quiz_types, [
	"modelsentences-12" => [
		"name" => "Stage 12 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => "auto",
		"options" => [[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. Syphāx et Celer in portū stābant. amīcī montem spectābant.","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Syphāx and Celer were standing in the harbor. The friends were watching the mountain.",
					'({*Syphāx and Celer} {were standing|stood} {(in|at) _a (port|harbo[u]r)}). ({*_a friends} {were (watching|looking at)} {_a mountain}).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms2.jpg' style='width: 205px;'><br>"),
				format_word("2. Syphāx amīcō dīxit, “ego prope portum servōs vēndēbam. ego subitō sonōs audīvī.”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Syphāx said to his friend, “I was selling slaves near the harbor. Suddenly I heard sounds.”",
					'{_Said$(Syphāx)$(_his friend) _quot$(*I} {was selling} {_some slaves} {near the (port|harbo[u]r)) {} _quot2$(*suddenly} {I} {heard} {_some (sounds|noises)).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms3.jpg' style='width: 205px;'><br>"),
				format_word("3. Celer Syphācī respondit, “tū sonōs audīvistī. ego tremōrēs sēnsī. ego prope montem ambulābam.”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Celer replied to Syphāx, “You heard sounds. I felt tremors. I was walking near the mountain.”",
					'_Replied$(Celer)$(Syphāx) _quot$(*you} {heard} {sounds|noises) {} _quot2$(*I} {felt|sensed} {_some (tremors|shak(es|ing)).) {} _quot2$(*I} {was walking} {near _a mountain).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms4.jpg' style='width: 205px;'><br>"),
				format_word("4. Poppæa et Lucriō in ātriō stābant. sollicitī erant.","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Poppæa and Lucriō were standing in the atrium. They were worried.",
					'({*were standing} {Poppæa and Lucriō} {(in[side]|at) _a atrium}). ({*worried} {were} {they}).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms5.jpg' style='width: 205px;'><br>"),
				format_word("5. Poppæa Lucriōnī dīxit, “ego in forō eram. ego tibi togam quærēbam. ego nūbem mīrābilem cōnspexī.”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Poppæa said to Lucriō, “I was in the forum. I was searching for a toga for you. I caught sight of a marvelous cloud.”",
					'_Said$(Poppæa)$(Lucriō) _quot$({*I} {was} {in _a forum}) {_quot$(_opts$(*I} {for you)$(was (look|search)ing} {for ${2})$(was seeking ${1}) $(_a toga).)} {_quot$({*I} {saw|caught sight of} {_a (wonderful|marvelous|strange|odd|weird) cloud}.)}.'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms6.jpg' style='width: 205px;'><br>"),
				format_word("6. Lucriō Poppææ respondit, “tū nūbem cōnspexistī. ego cinerem sēnsī. ego flammās vīdī.”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Lucriō replied to Poppæa, “You caught sight of a cloud. I felt ashes. I saw flames.”",
					'_Replied$(Lucriō)$(to Poppæa) _quot$(*you} {caught sight of|saw} {_a cloud) _quot2$(*I} {felt|sensed} {_some (ash[es])) _quot2$(*I} {saw} {_some flames).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms7.jpg' style='width: 205px;'><br>"),
				format_word("7. Marcus et Quārtus in forō erant. Sulla ad frātrēs contendit.","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Marcus and Quārtus were in the forum. Sulla hurried to the brothers.",
					'({{Marcus} and {Quārtus}} {were} {(in[side]|at) _a forum}). ({Sulla} {hurried|rushed} {[up] to ([the] brothers|them)}).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms8.jpg' style='width: 205px;'><br>"),
				format_word("8. Sulla frātribus dīxit, “ego ad theātrum contendēbam. ego sonōs audīvī et tremōrēs sēnsī. vōs sonōs audīvistis? vōs tremōrēs sēnsistis?”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"Sulla said to the brothers, “I was hurrying to the theater. I heard sounds and felt tremors. Did you hear sounds? Did you feel tremors?”.",
					'_Said$(Sulla)$([the] brothers) _quot$(*I} {was (hurry|runn)ing} {to _a theater) _quot2$(*I} {heard} {_some (sounds|noises)} _AND {[I]} {felt} {tremors|shakes|shaking) _quest2$(*did you} {hear} {_some (sounds|noises)) _quest2$(*did you} {feel} {_some (tremors|shak(es|ing))).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s12/ms9.jpg' style='width: 205px;'><br>"),
				format_word("9. frātrēs Sullæ respondērunt, “nōs tremōrēs sēnsimus et sonōs audīvimus. nōs nūbem mīrābilem vīdimus. nōs sollicitī sumus.”","la"),
				HTML("<br>"),
				name_answer_lang_tool("translation", new FreeResponseExpr(
					"The brothers replied to Sulla, “We felt tremors and heard the sounds. We saw the marvelous cloud. We are worried.”",
					'_Replied$([the] brothers)$(Sulla) _quot$(*we} {felt} {_some (tremors|shakes|shaking)} _AND {[we]} {heard} {_some (sounds|noises)) _quot2$(*we} {saw} {_a (wonderful|marvelous|strange|odd|weird) cloud) _quot2$(*we} {are worried).'
				), "en", "English translation")
			]
		]]
	],



	"modelsentences-19" => [
		"name" => "Stage 19 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => "auto",
		"options" => [[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s19/19_mod_sen_1_large.jpg' style='width: 205px;'><br>"),
				format_word("1. hic vir est Aristō. Aristō est amīcus Barbillī. in vīllā splendidā habitat, sed miserrimus est.","la",true),
				HTML("<br><br>"),
				name_answer_lang_tool("translation", new FreeParagraphExpr(
					"This man is Aristō. Aristō is a friend of Barbillus. He lives in a splendid house, but he is very miserable.",
					'({*this [man]} {is} {Aristō}). ({*Aristō|he} {is} {_a friend of Barbillus|Barbillus\' friend}). ({*he|Aristō} {(lives|dwells|resides|remains|lingers) in|inhabits} {_a (distinguished|noble|illustrious|bright|shining|glittering|brilliant|splendid|magnificent|sumptuous) (house|villa)}), but ({[he|Aristō]} {is} {(very|most) (miserable|unhappy|poor|wretched|pitiful|worthless|null|tragic|unfortunate|sick|tormenting)}).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s19/19_mod_sen_2_large.jpg' style='width: 205px;'><br>"),
				format_word("2. haec fēmina est Galatēa. Galatēa est uxor Aristōnis. Galatēa marītum saepe castīgat, numquam laudat.","la",true),
				HTML("<br><br>"),
				name_answer_lang_tool("translation", new FreeParagraphExpr(
					"This woman is Galatēa. Galatēa is Aristō's wife. Galatēa often scolds her husband, she never praises him.",
					'({*this [woman]} {is} {Galatēa}). ({Galatēa|she} {is} {_a (wife of Aristō|Aristō\'s wife)}). ({Galatēa} {often} {scolds} {[her|a|the] husband}), [but|and] ({[she]} {never} {prases} {him}|{never} {praising} {him}).'
				), "en", "English translation")
			]
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s19/19_mod_sen_3_large.jpg' style='width: 205px;'><br>"),
				format_word("3. haec puella est Helena. Helena est fīlia Aristōnis et Galatēae. multī iuvenēs hanc puellam amant, quod pulcherrima est.","la",true),
				HTML("<br><br>"),
				name_answer_lang_tool("translation", new FreeParagraphExpr(
					"This girl is Helena. Helena is Aristō and Galatēa's daughter. Many young men love this girl, because she is very beautiful.",
					'({*this [girl]} {is} {Helen[a]}). ({Helen[a]|she} {is} {_a (daughter of Aristō and Galatēa|Aristō and Galatēa\'s daughter)}). (Many (young men|boys) love (this girl|her|Helen[a])), because ({she} {is} {(very|[the] most) (beautiful|pretty)}).'
				), "en", "English translation")
			]
		]]
	],



	"modelsentences-25" => [
		"name" => "Stage 25 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => 4,
		"options" => function(){global $OP_USER_PARAGRAPH;return[[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s25/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. mīles legiōnis secundæ per castra ambulābat. subitō iuvenem ignōtum prope horreum latentem cōnspexit.","la"),
				format_word("“heus tū,” clāmāvit mīles, “quis es?”","la"),
				format_word("iuvenis nihil respondit. mīles iuvenem iterum rogāvit quis esset. iuvenis fūgit.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => [
					"A soldier of the second legion was walking through the camp. Suddenly he caught sight of a strange young man hiding near the barn.
“Hey you,” the soldier shouted, “Who are you?”
The young man said nothing. The soldier asked the young man again who he was. The young man fled."
				],
				"acceptable" => permute_sentence_choices([
					"A soldier",
					["of","with","in"],
					"the second legion was walking through",
					["the",""],
					"camp. Suddenly he caught sight of a",
					["strange","unfamiliar","unknown"],
					["young man","man","boy"],
					"hiding near the barn.
“Hey you,” the soldier shouted, “Who are you?”
The",
					["young man","man","boy"],
					"said nothing. The soldier asked the",
					["young man","man","boy"],
					"again who he was. The",
					["young man","man","boy"],
					["fled.","escaped.","ran."]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s25/ms2.jpg' style='width: 205px;'><br>"),
				format_word("2. mīles iuvenem petīvit et facile superāvit. “furcifer!” exclāmāvit. “quid prope horreum facis?”
iuvenis dīcere nōlēbat quid prope horreum faceret. mīles eum ad centuriōnem dūxit.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The soldier followed the young man and easily overcame him. “Thief!” he shouted. “What are you doing near the barn?”
The young man did not want to say what he was doing near the barn. The soldier lead him to the centurion."],
				"acceptable" => permute_sentence_choices([
					"The soldier",
					["followed","chased"],
					"the",
					["young man","man","boy"],
					"and",
					["the soldier","he",""],
					"easily",
					["reached","caught up to","overcame","overpowered"],
					"him. “Thief!”",
					["he","the soldier"],
					"shouted. “What are you doing near the ",
					["barn?”", "granary?”"],
					"The",
					["young man","man","boy"],
					["did not want","was not wanting","was not inclined"],
					"to say what he was doing near the",
					["barn.","granary."],
					"The soldier lead him to the centurion."
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s25/ms3.jpg' style='width: 205px;'><br>"),
				format_word("3. centuriō, iuvenem cōnspicātus, “hunc agnōscō!” inquit. “explōrātor Britannicus est, quem sæpe prope castra cōnspexī. quō modō eum cēpistī?”
tum mīles explicāvit quō modō iuvenem cēpisset.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The centurion, having caught sight of the young man, said, “I recognize this man! He is a British explorer, whom I have often seen near the camp. How did you catch him?”
Then the soldier explained how he had caught the young man."],
				"acceptable" => permute_sentence_choices([
					"The centurion, ",
					["having","after he had"],
					["caught sight of","seen","saw"],
					"the",
					["young man","man","boy"],
					"said, “I recognize this",
					["man!","person!","!"],
					"He is a",
					["British","Britannic"],
					"explorer, whom I have often seen near the camp. How did you catch him?”
Then the soldier explained how he had caught the",
					["young man.","man.","boy."],
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		],[
			"help" => "Translate the sentence",
			"sentence" => [
				HTML("<img src='https://www.cambridgescp.com/singles/webbook/s25/ms4.jpg' style='width: 205px;'><br>"),
				format_word("4. centuriō, ad iuvenem conversus, “cūr in castra vēnistī?” rogāvit. iuvenis tamen tacēbat.
    centuriō, ubi cognōscere nōn poterat cūr iuvenis in castra vēnisset, mīlitem iussit eum ad carcerem dūcere.
    iuvenis, postquam verba centuriōnis audīvit, “ego sum Vercobrix,” inquit, “fīlius prīncipis Deceanglōrum. vōbīs nōn decōrum est mē in carcere tenēre.”
    “fīlius prīncipis Deceanglōrum?” exclāmāvit centuriō. “libentissimē tē videō. nōs tē diū quærimus, cellamque optimam tibi in carcere parāvimus.”","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The centurion, having turned to the young man, asked, “Why have you come into the camp?” The young man however was silent.
The centurion, when he was unable to learn why the young man had come into the camp, ordered the soldier to lead him to the jail.
The young man, after he heard the centurion’s words, said, “I am Vercobrix, son of the chief of the Deceangli. It is not proper for you to hold me in prison.”
“The son of the chief of the Deceangli?” shouted the centurion. “I see you very happily. We have sought you for a long time, and we will prepare the best room for you in jail.”"],
				"acceptable" => permute_sentence_choices([
					"The centurion,",
					["having","after he"],
					"turned to the",
					["young man,","man,","boy,"],
					"asked, “Why have you",
					["come into","come to","entered","entered into"],
					"the camp?” The",
					["young man","man","boy"],
					"however",
					["was silent.","remained silent.","said nothing"],"
The centurion, when he was unable to learn why the young man had",
					["come into","come to","entered","entered into"],
					["the",""],
					"camp, ordered the soldier to lead him to the jail.
The",
					["young man,","man,","boy,"],
					"after he heard the",
					["centurion’s words,","words of the centurion","words spoken by the centurion"],
					"said, “I am Vercobrix, son of the chief of the Deceangli. It is not proper for you to hold me in prison.”
“The son of the chief of the Deceangli?” shouted the centurion. “I see you very happily. We have sought you for a long time, and we will prepare the best room for you in jail.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		]];}
	],
]);
