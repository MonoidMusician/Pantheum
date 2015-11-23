<?php
		require_once('/var/www/config.php');
		sro('/Includes/mysql.php');
		sro('/Includes/session.php');
		sro('/Includes/functions.php');

		sro('/PHP5/lib/PHPLang/db.php');
		sro('/PHP5/lib/PHPLang/misc.php');
		sro('/PHP5/lib/PHPLang/templates.php');
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head><body><?php
		function add_word($name) {
			global $sql_stmts;
			$w = defaultDB()->searcher()->name($name)->spart("verb")->lang("la")->all();
			if (count($w)) {
				echo "Word seems to be already added: $name<br>";
				if (count($w) === 1) {
					$w = $w[0];
					if ($w->read_paths()) echo "- Had inflection already<br>";
					else return $w;
				} else echo "- More than one found, continuing<br>";
			} else {
				echo "Adding word: $name<br>";
				sql_exec(sql_stmt("word_lang,word_name,word_spart->new in words"), ["sss", "la",$name,"verb"]);
				$w = defaultDB()->searcher()->name($name)->spart("verb")->lang("la")->all();
				if (count($w) === 1) {
					$w = $w[0];
					return $w;
				}
			}
		}
		function get_template($conj) {
			return safe_get(0,defaultDB()->searcher()->spart("verb")->name($conj)->only_with_attr(ATTR("template", "true"))->all());
		}
		function run_template2($word,$t,$arg,$ignore=NULL) {
			if (!$ignore) $ignore = [];
			if ($word !== NULL and $t !== NULL)
				run_template($word,"",$t,$arg,$ignore,[],FALSE);
		}
		run_template2(add_word("accūsō"), get_template("conj-1 active"), ['accūs', 'accūs', 'accūsāv', 'accūsāt'], []);
		run_template2(add_word("adiuvō"), get_template("conj-1 active"), ['adiuv', 'adiuv', 'adiūv', 'adiūt'], []);
		run_template2(add_word("aedificiō"), get_template("conj-1 active"), ['aedific', 'aedific', 'aedificāv', 'aedificāt'], []);
		run_template2(add_word("ambulō"), get_template("conj-1 active"), ['ambul', 'ambul', 'ambulāv', 'ambulāt'], []);
		run_template2(add_word("amō"), get_template("conj-1 active"), ['am', 'am', 'amāv', 'amāt'], []);
		run_template2(add_word("appellō"), get_template("conj-1 active"), ['appell', 'appell', 'appellāv', 'appellāt'], []);
		run_template2(add_word("appropinquō"), get_template("conj-1 neutral"), ['appropinqu', 'appropinqu', 'appropinquāv', 'appropinquāt'], []);
		run_template2(add_word("cantō"), get_template("conj-1 active"), ['cant', 'cant', 'cantāv', 'cantāt'], []);
		run_template2(add_word("cēlō"), get_template("conj-1 active"), ['cēl', 'cēl', 'cēlāv', 'cēlāt'], []);
		run_template2(add_word("cēnō"), get_template("conj-1 active"), ['cēn', 'cēn', 'cēnāv', 'cēnāt'], []);
		run_template2(add_word("cessō"), get_template("conj-1 active"), ['cess', 'cess', 'cessāv', 'cessāt'], []);
		run_template2(add_word("clāmō"), get_template("conj-1 active"), ['clām', 'clām', 'clāmāv', 'clāmāt'], []);
		run_template2(add_word("cōgitō"), get_template("conj-1 active"), ['cōgit', 'cōgit', 'cōgitāv', 'cōgitāt'], []);
		run_template2(add_word("convocō"), get_template("conj-1 active"), ['convoc', 'convoc', 'convocāv', 'convocāt'], []);
		run_template2(add_word("cūrō"), get_template("conj-1 active"), ['cūr', 'cūr', 'cūrāv', 'cūrāt'], []);
		run_template2(add_word("dēlectō"), get_template("conj-1 active"), ['dēlect', 'dēlect', 'dēlectāv', 'dēlectāt'], []);
		run_template2(add_word("dēmōnstrō"), get_template("conj-1 active"), ['dēmōnstr', 'dēmōnstr', 'dēmōnstrāv', 'dēmōnstrāt'], []);
		run_template2(add_word("dēsiderō"), get_template("conj-1 active"), ['dēsider', 'dēsider', 'dēsiderāv', 'dēsiderāt'], []);
		run_template2(add_word("dēvorō"), get_template("conj-1 active"), ['dēvor', 'dēvor', 'dēvorāv', 'dēvorāt'], []);
		run_template2(add_word("errō"), get_template("conj-1 active"), ['err', 'err', 'errāv', 'errāt'], []);
		run_template2(add_word("excitō"), get_template("conj-1 active"), ['excit', 'excit', 'excitāv', 'excitāt'], []);
		run_template2(add_word("exclāmō"), get_template("conj-1 active"), ['exclām', 'exclām', 'exclāmāv', 'exclāmāt'], []);
		run_template2(add_word("explicō"), get_template("conj-1 active"), ['explic', 'explic', 'explicāv', 'explicāt'], []);
		run_template2(add_word("exspectō"), get_template("conj-1 active"), ['exspect', 'exspect', 'exspectāv', 'exspectāt'], []);
		run_template2(add_word("festīnō"), get_template("conj-1 active"), ['festīn', 'festīn', 'festīnāv', 'festīnāt'], []);
		run_template2(add_word("habitō"), get_template("conj-1 active"), ['habit', 'habit', 'habitāv', 'habitāt'], []);
		run_template2(add_word("incitō"), get_template("conj-1 active"), ['incit', 'incit', 'incitāv', 'incitāt'], []);
		run_template2(add_word("interpellō"), get_template("conj-1 active"), ['interpell', 'interpell', 'interpellāv', 'interpellāt'], []);
		run_template2(add_word("intrō"), get_template("conj-1 active"), ['intr', 'intr', 'intrāv', 'intrāt'], []);
		run_template2(add_word("labōrō"), get_template("conj-1 active"), ['labōr', 'labōr', 'labōrāv', 'labōrāt'], []);
		run_template2(add_word("lacrimō"), get_template("conj-1 neutral"), ['lacrim', 'lacrim', 'lacrimāv', 'lacrimāt'], []);
		run_template2(add_word("lātrō"), get_template("conj-1 active"), ['lātr', 'lātr', 'lātrāv', 'lātrāt'], []);
		run_template2(add_word("laudō"), get_template("conj-1 active"), ['laud', 'laud', 'laudāv', 'laudāt'], []);
		run_template2(add_word("lavō"), get_template("conj-1 active"), ['lav', 'lav', 'lāv', 'laut'], []);
		run_template2(add_word("mōnstrō"), get_template("conj-1 active"), ['mōnstr', 'mōnstr', 'mōnstrāv', 'mōnstrāt'], []);
		run_template2(add_word("mussō"), get_template("conj-1 neutral"), ['muss', 'muss', 'mussāv', 'mussāt'], []);
		run_template2(add_word("nārrō"), get_template("conj-1 active"), ['nārr', 'nārr', 'nārrāv', 'nārrāt'], []);
		run_template2(add_word("nāvigō"), get_template("conj-1 active"), ['nāvig', 'nāvig', 'nāvigāv', 'nāvigāt'], []);
		run_template2(add_word("necō"), get_template("conj-1 active"), ['nec', 'nec', 'necāv', 'necāt'], []);
		run_template2(add_word("observō"), get_template("conj-1 active"), ['observ', 'observ', 'observāv', 'observāt'], []);
		run_template2(add_word("parō"), get_template("conj-1 active"), ['par', 'par', 'parāv', 'parāt'], []);
		run_template2(add_word("pernoctō"), get_template("conj-1 neutral"), ['pernoct', 'pernoct', 'pernoctāv', 'pernoctāt'], []);
		run_template2(add_word("portō"), get_template("conj-1 active"), ['port', 'port', 'portāv', 'portāt'], []);
		run_template2(add_word("praecipitō"), get_template("conj-1 active"), ['praecipit', 'praecipit', 'praecipitāv', 'praecipitāt'], []);
		run_template2(add_word("pugnō"), get_template("conj-1 active"), ['pugn', 'pugn', 'pugnāv', 'pugnāt'], []);
		run_template2(add_word("purgō"), get_template("conj-1 active"), ['purg', 'purg', 'purgāv', 'purgāt'], []);
		run_template2(add_word("recuperō"), get_template("conj-1 active"), ['recuper', 'recuper', 'recuperāv', 'recuperāt'], []);
		run_template2(add_word("revocō"), get_template("conj-1 active"), ['revoc', 'revoc', 'revocāv', 'revocāt'], []);
		run_template2(add_word("rogō"), get_template("conj-1 active"), ['rog', 'rog', 'rogāv', 'rogāt'], []);
		run_template2(add_word("saltō"), get_template("conj-1 active"), ['salt', 'salt', 'saltāv', 'saltāt'], []);
		run_template2(add_word("salūtō"), get_template("conj-1 active"), ['salūt', 'salūt', 'salūtāv', 'salūtāt'], []);
		run_template2(add_word("servō"), get_template("conj-1 active"), ['serv', 'serv', 'servāv', 'servāt'], []);
		run_template2(add_word("simulō"), get_template("conj-1 active"), ['simul', 'simul', 'simulāv', 'simulāt'], []);
		run_template2(add_word("spectō"), get_template("conj-1 active"), ['spect', 'spect', 'spectāv', 'spectāt'], []);
		run_template2(add_word("stō"), get_template("conj-1 active"), ['st', 'st', 'stet', 'stat'], []);
		run_template2(add_word("temptō"), get_template("conj-1 active"), ['tempt', 'tempt', 'temptāv', 'temptāt'], []);
		run_template2(add_word("verberō"), get_template("conj-1 active"), ['verber', 'verber', 'verberāv', 'verberāt'], []);
		run_template2(add_word("vetō"), get_template("conj-1 active"), ['vet', 'vet', 'vetu', 'vetit'], []);
		run_template2(add_word("vexō"), get_template("conj-1 active"), ['vex', 'vex', 'vexāv', 'vexāt'], []);
		run_template2(add_word("vigilō"), get_template("conj-1 active"), ['vigil', 'vigil', 'vigilāv', 'vigilāt'], []);
		run_template2(add_word("vīsitō"), get_template("conj-1 active"), ['vīsit', 'vīsit', 'vīsitāv', 'vīsitāt'], []);
		run_template2(add_word("vītō"), get_template("conj-1 active"), ['vīt', 'vīt', 'vītāv', 'vītāt'], []);
		run_template2(add_word("admoveō"), get_template("conj-2 active"), ['admov', 'admov', 'admōv', 'admōt'], []);
		run_template2(add_word("appāreō"), get_template("conj-2 no-supine"), ['appār', 'appār', 'appāru', ''], []);
		run_template2(add_word("augeō"), get_template("conj-2 active"), ['aug', 'aug', 'aux', 'auct'], []);
		run_template2(add_word("caveō"), get_template("conj-2 active"), ['cav', 'cav', 'cāv', 'caut'], []);
		run_template2(add_word("cēnseō"), get_template("conj-2 active"), ['cēns', 'cēns', 'cēnsu', 'cēns'], []);
		run_template2(add_word("dēbeō"), get_template("conj-2 active"), ['dēb', 'dēb', 'dēbu', 'dēbit'], []);
		run_template2(add_word("doceō"), get_template("conj-2 active"), ['doc', 'doc', 'docu', 'doct'], []);
		run_template2(add_word("doleō"), get_template("conj-2 no-supine"), ['dol', 'dol', 'dolu', ''], []);
		run_template2(add_word("faveō"), get_template("conj-2 no-supine"), ['fav', 'fav', 'fāv', ''], []);
		run_template2(add_word("habeō"), get_template("conj-2 active"), ['hab', 'hab', 'habu', 'habit'], []);
		run_template2(add_word("haereō"), get_template("conj-2 no-supine"), ['haer', 'haer', 'haes', ''], []);
		run_template2(add_word("jaceō"), get_template("conj-2 no-supine"), ['jac', 'jac', 'jacu', ''], []);
		run_template2(add_word("iubeō"), get_template("conj-2 active"), ['iub', 'iub', 'iuss', 'iuss'], []);
		run_template2(add_word("licet"), get_template("conj-2 no-supine"), ['lic', 'lic', 'licu', ''], [['person-1'], ['person-2']]);
		run_template2(add_word("lūceō"), get_template("conj-2 no-supine"), ['lūc', 'lūc', 'lūx', ''], []);
		run_template2(add_word("maneō"), get_template("conj-2 active"), ['man', 'man', 'māns', 'māns'], []);
		run_template2(add_word("moveō"), get_template("conj-2 active"), ['mov', 'mov', 'mōv', 'mōt'], []);
		run_template2(add_word("noceō"), get_template("conj-2 no-supine"), ['noc', 'noc', 'nocu', ''], []);
		run_template2(add_word("obsideō"), get_template("conj-2 active"), ['obsid', 'obsid', 'obsēd', 'obsess'], []);
		run_template2(add_word("removeō"), get_template("conj-2 active"), ['remov', 'remov', 'remōv', 'remōt'], []);
		run_template2(add_word("respondeō"), get_template("conj-2 active"), ['respond', 'respond', 'respond', 'respōns'], []);
		run_template2(add_word("rīdeō"), get_template("conj-2 active"), ['rīd', 'rīd', 'rīs', 'rīs'], []);
		run_template2(add_word("sedeō"), get_template("conj-2 active"), ['sed', 'sed', 'sēd', 'sess'], []);
		run_template2(add_word("stupeō"), get_template("conj-2 no-supine"), ['stup', 'stup', 'stupu', ''], []);
		run_template2(add_word("taceō"), get_template("conj-2 active"), ['tac', 'tac', 'tacu', 'tacit'], []);
		run_template2(add_word("teneō"), get_template("conj-2 active"), ['ten', 'ten', 'tenu', 'tent'], []);
		run_template2(add_word("terreō"), get_template("conj-2 active"), ['terr', 'terr', 'terru', 'territ'], []);
		run_template2(add_word("timeō"), get_template("conj-2 no-supine"), ['tim', 'tim', 'timu', ''], []);
		run_template2(add_word("videō"), get_template("conj-2 active"), ['vid', 'vid', 'vīd', 'vīs'], []);
		run_template2(add_word("accidō"), get_template("conj-3 no-supine"), ['accid', 'accid', 'accid', ''], []);
		run_template2(add_word("advesperāscit"), get_template("conj-3 no-supine"), ['advesperāsc', 'advesperāsc', 'advesperāv', ''], [['person-1'], ['person-2']]);
		run_template2(add_word("agnōscō"), get_template("conj-3 active"), ['agnōsc', 'agnōsc', 'agnōv', 'agnit'], []);
		run_template2(add_word("agō"), get_template("conj-3 active"), ['ag', 'ag', 'ēg', 'āct'], []);
		run_template2(add_word("alō"), get_template("conj-3 active"), ['al', 'al', 'alu', 'alit'], []);
		run_template2(add_word("ascendō"), get_template("conj-3 neutral"), ['ascend', 'ascend', 'ascend', 'ascēns'], []);
		run_template2(add_word("cadō"), get_template("conj-3 neutral"), ['cad', 'cad', 'cecid', 'cās'], []);
		run_template2(add_word("claudō"), get_template("conj-3 active"), ['claud', 'claud', 'claus', 'claus'], []);
		run_template2(add_word("colō"), get_template("conj-3 active"), ['col', 'col', 'colu', 'cult'], []);
		run_template2(add_word("concidō"), get_template("conj-3 no-supine"), ['concid', 'concid', 'concid', ''], []);
		run_template2(add_word("condō"), get_template("conj-3 active"), ['cond', 'cond', 'condid', 'condit'], []);
		run_template2(add_word("condūcō"), get_template("conj-3 active"), ['condūc', 'condūc', 'condūx', 'conduct'], []);
		run_template2(add_word("cōnsidō"), get_template("conj-3 neutral"), ['cōnsid', 'cōnsid', 'cōnsēd', 'cōnsess'], []);
		run_template2(add_word("cōnstituō"), get_template("conj-3 active"), ['cōnstitu', 'cōnstitu', 'cōnstitu', 'cōnstitūt'], []);
		run_template2(add_word("cōnsulō"), get_template("conj-3 active"), ['cōnsul', 'cōnsul', 'cōnsulu', 'cōnsult'], []);
		run_template2(add_word("coquō"), get_template("conj-3 active"), ['coqu', 'coqu', 'cox', 'coct'], []);
		run_template2(add_word("currō"), get_template("conj-3 active"), ['curr', 'curr', 'cucurr', 'curs'], []);
		run_template2(add_word("dēfendō"), get_template("conj-3 active"), ['dēfend', 'dēfend', 'dēfend', 'dēfēns'], []);
		run_template2(add_word("dēscendō"), get_template("conj-3 active"), ['dēscend', 'dēscend', 'dēscend', 'dēscēns'], []);
		run_template2(add_word("dēvertō"), get_template("conj-3 active"), ['dēvert', 'dēvert', 'dēvert', 'dēvers'], []);
		run_template2(add_word("dīcō"), get_template("conj-3 active"), ['dīc', 'dīc', 'dīx', 'dict'], []);
		run_template2(add_word("discēdō"), get_template("conj-3 active"), ['discēd', 'discēd', 'discess', 'discess'], []);
		run_template2(add_word("dūcō"), get_template("conj-3 active"), ['dūc', 'dūc', 'dūx', 'duct'], []);
		run_template2(add_word("emō"), get_template("conj-3 active"), ['em', 'em', 'ēm', 'empt'], []);
		run_template2(add_word("extendō"), get_template("conj-3 active"), ['extend', 'extend', 'extend', 'extent'], []);
		run_template2(add_word("extrahō"), get_template("conj-3 active"), ['extrah', 'extrah', 'extrāx', 'extract'], []);
		run_template2(add_word("gemō"), get_template("conj-3 active"), ['gem', 'gem', 'gemu', 'gemit'], []);
		run_template2(add_word("gerō"), get_template("conj-3 active"), ['ger', 'ger', 'gess', 'gest'], []);
		run_template2(add_word("induō"), get_template("conj-3 active"), ['indu', 'indu', 'indu', 'indūt'], []);
		run_template2(add_word("inūrō"), get_template("conj-3 active"), ['inūr', 'inūr', 'inuss', 'inust'], []);
		run_template2(add_word("lambō"), get_template("conj-3 active"), ['lamb', 'lamb', 'lamb', 'lambit'], []);
		run_template2(add_word("legō"), get_template("conj-3 active"), ['leg', 'leg', 'lēg', 'lēct'], []);
		run_template2(add_word("lūdō"), get_template("conj-3 active"), ['lūd', 'lūd', 'lūs', 'lūs'], []);
		run_template2(add_word("mittō"), get_template("conj-3 active"), ['mitt', 'mitt', 'mīs', 'miss'], []);
		run_template2(add_word("occurrō"), get_template("conj-3 active"), ['occur', 'occur', 'occurr', 'occurs'], []);
		run_template2(add_word("petō"), get_template("conj-3 active"), ['pet', 'pet', 'petīv', 'petīt'], []);
		run_template2(add_word("pluit"), get_template("conj-3 no-supine"), ['plu', 'plu', 'pluit , plūv', ''], [['person-1'], ['person-2']]);
		run_template2(add_word("pōnō"), get_template("conj-3 active"), ['pōn', 'pōn', 'posu', 'posit'], []);
		run_template2(add_word("praecurrō"), get_template("conj-3 active"), ['praecurr', 'praecurr', "praecucurrī\npraecurr", 'praecurs'], []);
		run_template2(add_word("prōmittō"), get_template("conj-3 active"), ['prōmitt', 'prōmitt', 'prōmīs', 'prōmiss'], []);
		run_template2(add_word("quiēscō"), get_template("conj-3 active"), ['quiēsc', 'quiēsc', 'quiēv', 'quiēt'], []);
		run_template2(add_word("regō"), get_template("conj-3 active"), ['reg', 'reg', 'rēx', 'rēct'], []);
		run_template2(add_word("relinquō"), get_template("conj-3 active"), ['relinqu', 'relinqu', 'relīqu', 'relict'], []);
		run_template2(add_word("repellō"), get_template("conj-3 active"), ['repell', 'repell', 'reppul', 'repuls'], []);
		run_template2(add_word("reprehendō"), get_template("conj-3 active"), ['reprehend', 'reprehend', 'reprehend', 'reprehēns'], []);
		run_template2(add_word("scrībō"), get_template("conj-3 active"), ['scrīb', 'scrīb', 'scrīps', 'scrīpt'], []);
		run_template2(add_word("stertō"), get_template("conj-3 no-supine"), ['stert', 'stert', 'stertu', ''], []);
		run_template2(add_word("stringō"), get_template("conj-3 active"), ['string', 'string', 'strīnx', 'strict'], []);
		run_template2(add_word("sūmō"), get_template("conj-3 active"), ['sūm', 'sūm', 'sūmps', 'sūmpt'], []);
		run_template2(add_word("surgō"), get_template("conj-3 active"), ['surg', 'surg', 'surrēx', 'surrēct'], []);
		run_template2(add_word("trādō"), get_template("conj-3 active"), ['trād', 'trād', 'trādid', 'trādit'], []);
		run_template2(add_word("trahō"), get_template("conj-3 active"), ['trah', 'trah', 'trāx', 'tract'], []);
		run_template2(add_word("tremō"), get_template("conj-3 no-supine"), ['trem', 'trem', 'tremu', ''], []);
		run_template2(add_word("vertō"), get_template("conj-3 active"), ['vert', 'vert', 'vert', 'vers'], []);
		run_template2(add_word("vincō"), get_template("conj-3 active"), ['vinc', 'vinc', 'vīc', 'vict'], []);
		run_template2(add_word("arripiō"), get_template("conj-3-io active"), ['arrip', 'arrip', 'arripu', 'arrept'], []);
		run_template2(add_word("capiō"), get_template("conj-3-io active"), ['cap', 'cap', 'cēp', 'capt'], []);
		run_template2(add_word("cōnficiō"), get_template("conj-3-io active"), ['cōnfic', 'cōnfic', 'cōnfēc', 'cōnfect'], []);
		run_template2(add_word("coniciō"), get_template("conj-3-io active"), ['conic', 'conic', 'coniēc', 'coniect'], []);
		run_template2(add_word("cōnspiciō"), get_template("conj-3-io active"), ['cōnspic', 'cōnspic', 'cōnspex', 'cōnspect'], []);
		run_template2(add_word("effugiō"), get_template("conj-3-io active"), ['effug', 'effug', 'effūg', 'effugit'], []);
		run_template2(add_word("excipiō"), get_template("conj-3-io active"), ['excip', 'excip', 'excēp', 'except'], []);
		run_template2(add_word("faciō"), get_template("conj-3-io active"), ['fac', 'fac', 'fēc', 'fact'], []);
		run_template2(add_word("fugiō"), get_template("conj-3-io active"), ['fug', 'fug', 'fūg', 'fugit'], []);
		run_template2(add_word("jaciō"), get_template("conj-3-io active"), ['jac', 'jac', 'iēc', 'jact'], []);
		run_template2(add_word("īnspiciō"), get_template("conj-3-io active"), ['īnspic', 'īnspic', 'īnspex', 'īnspect'], []);
		run_template2(add_word("olfaciō"), get_template("conj-3-io active"), ['olfac', 'olfac', 'olfēc', 'olfact'], []);
		run_template2(add_word("perficiō"), get_template("conj-3-io active"), ['perfic', 'perfic', 'perfēc', 'perfect'], []);
		run_template2(add_word("adveniō"), get_template("conj-4 active"), ['adven', 'adven', 'advēn', 'advent'], []);
		run_template2(add_word("aperiō"), get_template("conj-4 active"), ['aper', 'aper', 'aperu', 'apert'], []);
		run_template2(add_word("audiō"), get_template("conj-4 active"), ['aud', 'aud', 'audīv', 'audīt'], []);
		run_template2(add_word("custōdiō"), get_template("conj-4 active"), ['custōd', 'custōd', 'custōdīv', 'custōdīt'], []);
		run_template2(add_word("dormiō"), get_template("conj-4 active"), ['dorm', 'dorm', 'dormīv', 'dormīt'], []);
		run_template2(add_word("ēsuriō"), get_template("conj-4 no-supine"), ['ēsur', 'ēsur', 'ēsurīv', ''], []);
		run_template2(add_word("feriō"), get_template("conj-4 active"), ['fer', 'fer', 'ferīv', 'ferīt'], []);
		run_template2(add_word("fīniō"), get_template("conj-4 active"), ['fīn', 'fīn', 'fīnīv', 'fīnīt'], []);
		run_template2(add_word("impediō"), get_template("conj-4 active"), ['imped', 'imped', 'impedīv', 'impedīt'], []);
		run_template2(add_word("inveniō"), get_template("conj-4 active"), ['inven', 'inven', 'invēn', 'invent'], []);
		run_template2(add_word("nesciō"), get_template("conj-4 active"), ['nesc', 'nesc', 'nescīv', 'nescīt'], []);
		run_template2(add_word("obdormiō"), get_template("conj-4 active"), ['obdorm', 'obdorm', 'obdormīv', 'obdormīt'], []);
		run_template2(add_word("pūniō"), get_template("conj-4 active"), ['pūn', 'pūn', 'pūnīv', 'pūnīt'], []);
		run_template2(add_word("sciō"), get_template("conj-4 active"), ['sc', 'sc', 'scīv', 'scīt'], []);
		run_template2(add_word("veniō"), get_template("conj-4 active"), ['ven', 'ven', 'vēn', 'vent'], [['person-1', 'passive'], ['person-2', 'passive']]);
echo "DONE!!";
	?></body></head>
