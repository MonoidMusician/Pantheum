<?php
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
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. Syphāx et Celer in portū stābant. amīcī montem spectābant.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => [
					"Syphāx and Celer were standing in the harbor. The friends were watching the mountain."
				],
				"acceptable" => permute_sentence_choices([
					"Syphāx and Celer",
					["were standing","stood"],
					["in","at"],
					["the","a",""],
					["harbor.","harbour."],
					["The",""],
					"friends were",
					["watching","looking at"],
					["the","a",""],
					"mountain.",
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms2.jpg' style='width: 205px;'><br>"),
				format_word("2. Syphāx amīcō dīxit, “ego prope portum servōs vēndēbam. ego subitō sonōs audīvī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Syphāx said to his friend, “I was selling slaves near the harbor. Suddenly I heard sounds.”"],
				"acceptable" => permute_sentence_choices([
					"Syphāx",
					["said to","told"],
					["his","the","a",""],
					"friend, ",
					["“I was selling slaves near the harbor."],
					["Suddenly I","I suddenly"],
					"heard",
					["the","some",""],
					["sounds.”","noises.”"]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms3.jpg' style='width: 205px;'><br>"),
				format_word("3. Celer Syphācī respondit, “tū sonōs audīvistī. ego tremōrēs sēnsī. ego prope montem ambulābam.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Celer replied to Syphāx, “You heard sounds. I felt tremors. I was walking near the mountain.”"],
				"acceptable" => permute_sentence_choices([
					"Celer",
					["replied","responded"],
					"to Syphāx, “You heard",
					["sounds.","noises."],
					"I felt",
					["the","some",""],
					["tremors.","shakes","shaking"],
					"I was walking near the mountain.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms4.jpg' style='width: 205px;'><br>"),
				format_word("4. Poppæa et Lucriō in ātriō stābant. sollicitī erant.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Poppæa and Lucriō were standing in the atrium. They were worried."],
				"acceptable" => permute_sentence_choices([
					"Poppæa and Lucriō were",
					["standing",""],
					["in","inside","at"],
					["the","an",""],
					["atrium."],
					"They were worried."
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms5.jpg' style='width: 205px;'><br>"),
				format_word("5. Poppæa Lucriōnī dīxit, “ego in forō eram. ego tibi togam quærēbam. ego nūbem mīrābilem cōnspexī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Poppæa said to Lucriō, “I was in the forum. I was searching for a toga for you. I caught sight of a marvelous cloud.”"],
				"acceptable" => permute_sentence_choices([
					"Poppæa",
					["said to","told"],
					"Lucriō, “I was",
					["in","inside","at"],
					["the","a",""],
					"forum. I was",
					["searching for","seeking","looking for"],
					["the","a",""],
					"toga for you. I",
					["caught sight of","saw"],
					["the","a",""],
					["wonderful","marvelous","strange","odd","weird"],
					"cloud.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms6.jpg' style='width: 205px;'><br>"),
				format_word("6. Lucriō Poppææ respondit, “tū nūbem cōnspexistī. ego cinerem sēnsī. ego flammās vīdī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Lucriō replied to Poppæa, “You caught sight of a cloud. I felt ashes. I saw flames.”"],
				"acceptable" => permute_sentence_choices([
					"Lucriō",
					["replied","responded"],
					"to Poppæa, “You",
					["caught sight of","saw","spied"],
					["the","a",""],
					"cloud. I felt",
					["the","some",""],
					["ashes.","ash"],
					"I saw",
					["the","some",""],
					"flames.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms7.jpg' style='width: 205px;'><br>"),
				format_word("7. Marcus et Quārtus in forō erant. Sulla ad frātrēs contendit.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Marcus and Quārtus were in the forum. Sulla hurried to the brothers."],
				"acceptable" => permute_sentence_choices([
					"Marcus and Quārtus were",
					["in","inside","at"],
					["the","a",""],
					"forum. Sulla",
					["hurried","rushed"],
					["up to","to"],
					["the brothers","brothers","them"]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms8.jpg' style='width: 205px;'><br>"),
				format_word("8. Sulla frātribus dīxit, “ego ad theātrum contendēbam. ego sonōs audīvī et tremōrēs sēnsī. vōs sonōs audīvistis? vōs tremōrēs sēnsistis?”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Sulla said to the brothers, “I was hurrying to the theater. I heard sounds and felt tremors. Did you hear sounds? Did you feel tremors?”."],
				"acceptable" => permute_sentence_choices([
					"Sulla",
					["said to","told","asked"],
					["the brothers,","brothers,"],
					"“I was",
					["hurrying","running"],
					"to the theater. I heard",
					["the","some",""],
					["sounds","noises"],
					"and",["","I"],"felt",
					["the",""],
					["tremors.","shaking"],
					"Did you hear",
					["the",""],
					["sounds?","noises?"],
					"Did you feel",
					["the",""],
					["tremors?”.","shakes?”.","shaking?”."]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms9.jpg' style='width: 205px;'><br>"),
				format_word("9. frātrēs Sullæ respondērunt, “nōs tremōrēs sēnsimus et sonōs audīvimus. nōs nūbem mīrābilem vīdimus. nōs sollicitī sumus.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["The brothers replied to Sulla, “We felt tremors and heard the sounds. We saw the marvelous cloud. We are worried.”"],
				"acceptable" => permute_sentence_choices([
					["The",""],
					"brothers",
					["replied","responded"],
					"to Sulla, “We felt",
					["the",""],
					["tremors","shaking."],
					"and",["We",""],"heard",
					["the",""],
					"sounds. We saw",
					["the","a",""],
					["wonderful","marvelous","strange","odd","weird"],
					"cloud. We are worried.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		]]
	],
	"modelsentences-25" => [
		"name" => "Stage 25 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => "auto",
		"options" => [[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. mīles legiōnis secundæ per castra ambulābat. subitō iuvenem ignōtum prope horreum latentem cōnspexit.
“heus tū,” clāmāvit mīles, “quis es?”
iuvenis nihil respondit. mīles iuvenem iterum rogāvit quis esset. iuvenis fūgit.","la"),
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
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms2.jpg' style='width: 205px;'><br>"),
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
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms3.jpg' style='width: 205px;'><br>"),
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
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms4.jpg' style='width: 205px;'><br>"),
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
			"answer0-language" => "",
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
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s19/19_mod_sen_1_large.jpg' style='width: 205px;'><br>"),
				format_word("1. hic vir est Aristō. Aristō est
amīcus Barbillī. in vīllā
splendidā habitat, sed
miserrimus est.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => [
					"This man is Aristō. Aristō is a friend of Barbillus. He lives in a splendid house, but he is very miserable."
					// This man is Aristō. Aristō is a friend of Barbillus. He lives in a splendid villa, but he is very miserable.
				],
				"expr" => "This man is Aristō. Aristō is ([a|the] friend of Barbillus|Barbillus' friend). (He|Aristō) ((lives|dwells|resides|remains|lingers) in|inhabits) [a|the] (distinguished|noble|illustrious|bright|shining|glittering|brilliant|splendid|magnificent|sumptuous) (house|villa), but [he|Aristō] is (very|most) (miserable|unhappy|poor|wretched|pitiful|worthless|null|tragic|unfortunate|sick|tormenting).",
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		]],
	],
]);
