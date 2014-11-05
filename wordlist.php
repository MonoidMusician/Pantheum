<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/misc.php');
defaultDB()->load_language("tp");
function make_word($name, $jp=NULL, $zh=NULL) {
	global $sql_stmts;
	sql_exec($sql_stmts["word_lang,word_name,word_spart->new in words"], ["sss","tp",$name,"multi"]);
	$id = NULL;
	sql_getone($sql_stmts["word_lang,word_name,word_spart->word_id"], $id, ["sss","tp",$name,"multi"]);
	$GLOBALS["word"] = WORD(defaultDB(), $id);
	if ($jp) {
		$a = ATTR("jp", $jp);
		$GLOBALS["word"]->add_attr($a);
	}
	if ($zh) {
		$a = ATTR("zh", $jp);
		$GLOBALS["word"]->add_attr($a);
	}
}
function add_definition($spart, $value) {
	$w = $GLOBALS["word"];
	$d = DEFINITION(defaultDB(), NULL, $w);
	$d->set_lang("en");
	$d->set_value($value);
	$p = PATH($w, $spart);
	$d->set_path($p);
	$d = $w->add_definition($d);
}


/*make_word("ike","悪","歹");
add_definition("mod","bad, negative, wrong, evil, overly complex, (figuratively) unhealthy");
add_definition("interj","oh dear! woe! alas!");
add_definition("n","negativity, badness, evil");
add_definition("vt","to make bad, to worsen, to have a negative effect upon");
add_definition("vi","to be bad, to suck");

exit();*/


make_word("a","あ","啊");
add_definition("interj","ah, ha, uh, oh, ooh, aw, well (emotion word)");

make_word("akesi","獣","龟");
add_definition("n","non-cute animal, reptile, amphibian");
add_definition("noun","creeping animal, a scaly or slimy animal that creeps on land");
add_definition("noun","reptile, any air-breathing cold-blooded animal that crawls on land and has scales and bones; reptile");
add_definition("noun","amphibian, any cold-blooded animal with bones that lives on both land and in water; amphibian");
add_definition("noun","large arthropod, a large arthropod that lives on land; scorpion");
add_definition("describer","like an<b>akesi</b>in appearance");
add_definition("describer","having the characteristics or properties of an<b>akesi</b>");


make_word("ala","無","不");
add_definition("mod","no, not, none, un-");
add_definition("n","nothing, negation, zero");
add_definition("interj","no!");
add_definition("describer","not, not such; no, not");
add_definition("describer","no, zero, no quantity of; no, zero");
add_definition("describer","un-, opposite of; un-");
add_definition("noun","the state, situation or general phenomenon of being<b>ala</b>; absence, emptiness, negation, nothing, zero");
add_definition("interjection","a short, sudden or emotional expression of<b>ala</b>; no!");


make_word("alasa","探");
add_definition("transitive-verb","to gather, to collect food, resources or material needed for daily life and survival; to gather, harvest");
add_definition("transitive-verb","to hunt, to pursue and kill animals to use as food and clothing; to hunt");


make_word("ale, ali,","全","全");
add_definition("n","everything, anything, life, the universe");
add_definition("mod","all, every, complete, whole");


make_word("anpa","下","下");
add_definition("n","bottom, lower part, under, below, floor, beneath");
add_definition("mod","low, lower, bottom, down");


make_word("ante","变","变");
add_definition("n","difference");
add_definition("mod","different");
add_definition("conj","otherwise, or else");
add_definition("vt","change, alter, modify");


make_word("anu","ぬ","或");
add_definition("conj","or");


make_word("awen","待","守");
add_definition("vi","stay, wait, remain");
add_definition("vt","keep");
add_definition("mod","remaining, stationary, permanent, sedentary");


make_word("e","え","把");
add_definition("sep","(introduces a direct object)");


make_word("en","ん","又");
add_definition("conj","and (used to coordinate head nouns)");


make_word("esun","市");
add_definition("n","market, shop");


make_word("ijo","物 or 事");
add_definition("n","thing, something, stuff, anything, object");
add_definition("mod","of something");
add_definition("vt","objectify");


make_word("ike","悪","歹");
add_definition("mod","bad, negative, wrong, evil, overly complex, (figuratively) unhealthy");
add_definition("interj","oh dear! woe! alas!");
add_definition("n","negativity, badness, evil");
add_definition("vt","to make bad, to worsen, to have a negative effect upon");
add_definition("vi","to be bad, to suck");


make_word("ilo","具","匕");
add_definition("n","tool, device, machine, thing used for a specific purpose");


make_word("insa","内","内");
add_definition("n","inside, inner world, centre, stomach");
add_definition("mod","inner, internal");


make_word("jaki","汚","污 or 汙");
add_definition("mod","dirty, gross, filthy");
add_definition("n","dirt, pollution, garbage, filth");
add_definition("vt","pollute, dirty");
add_definition("interj","ew! yuck!");


make_word("jan","人","人");
add_definition("n","person, people, human, being, somebody, anybody");
add_definition("mod","human, somebody's, personal, of people");
add_definition("vt","personify, humanize, personalize");


make_word("jelo","黄","黄");
add_definition("mod","yellow, light green");


make_word("jo","有","有");
add_definition("vt","have, contain");
add_definition("n","having");
add_definition("kama","receive, get, take, obtain");


make_word("kala","魚","鱼");
add_definition("n","fish, sea creature");


make_word("kalama","音","音");
add_definition("n","sound, noise, voice");
add_definition("vi","make noise");
add_definition("vt","sound, ring, play (an instrument)");


make_word("kama","来","到");
add_definition("vi","come, become, arrive, happen, pursue actions to arrive to (a certain state), manage to, start to");
add_definition("n","event, happening, chance, arrival, beginning");
add_definition("mod","coming, future");
add_definition("vt","bring about, summon");


make_word("kasi","木","木");
add_definition("n","plant, leaf, herb, tree, wood");


make_word("ken","能","能");
add_definition("vi","can, is able to, is allowed to, may, is possible");
add_definition("n","possibility, ability, power to do things, permission");
add_definition("vt","make possible, enable, allow, permit");
add_definition("cont","it is possible that");


make_word("kepeken","使","用");
add_definition("vt","use");
add_definition("prep","with");


make_word("kili","果 or 果");
add_definition("n","fruit, pulpy vegetable, mushroom");


make_word("kin","又","也");
add_definition("mod","also, too, even, indeed (emphasizes the word(s) before it)");


make_word("kipisi","切");


make_word("kiwen","石","石");
add_definition("mod","hard, solid, stone-like, made of stone or metal");
add_definition("n","hard thing, rock, stone, metal, mineral, clay");


make_word("ko","粉","膏");
add_definition("n","semi-solid or squishy substance, e.g. paste, powder, gum");


make_word("kon","空","气");
add_definition("n","air, wind, smell, soul");
add_definition("mod","air-like, ethereal, gaseous");


make_word("kule","色","色");
add_definition("n","colour, paint");
add_definition("mod","colourful");
add_definition("vt","colour, paint");


make_word("kute","聞","耳");
add_definition("vt","listen, hear");
add_definition("mod","auditory, hearing");


make_word("kulupu","群","组");
add_definition("n","group, community, society, company, people");
add_definition("mod","communal, shared, public, of the society");


make_word("la","ら","喇");
add_definition("sep","(between adverb or phrase of context and sentence)");


make_word("lape","眠","觉");
add_definition("n","sleep, rest");
add_definition("vi","sleep, rest");
add_definition("mod","sleeping, of sleep");


make_word("laso","青","青");
add_definition("mod","blue, blue-green");


make_word("lawa","首","首");
add_definition("n","head, mind");
add_definition("mod","main, leading, in charge");
add_definition("vt","lead, control, rule, steer");


make_word("len","布","巾");
add_definition("n","clothing, cloth, fabric");


make_word("lete","冷","冰");
add_definition("n","cold");
add_definition("mod","cold, uncooked");
add_definition("vt","cool down, chill");


make_word("li","り","哩");
add_definition("sep","(between any subject except mi and sina and its verb; also used to introduce a new verb for the same subject)");


make_word("lili","小","小");
add_definition("mod","small, little, young, a bit, short, few, less");
add_definition("vt","reduce, shorten, shrink, lessen");


make_word("linja","糸","糸");
add_definition("n","long, very thin, floppy thing, e.g. string, rope, hair, thread, cord, chain");


make_word("lipu","葉","叶");
add_definition("n","flat and bendable thing, e.g. paper, card, ticket");


make_word("loje","赤","红");
add_definition("mod","red");


make_word("lon","在","在");
add_definition("prep","be (located) in/at/on");
add_definition("vi","be there, be present, be real/true, exist, be awake");


make_word("luka","手","手");
add_definition("n","hand, arm");


make_word("lukin","見","看");
add_definition("vt","see, look at, watch, read");
add_definition("vi","look, watch out, pay attention");
add_definition("mod","visual(ly)");


make_word("lupa","穴","孔");
add_definition("n","hole, orifice, window, door");


make_word("ma","土","土");
add_definition("n","land, earth, country, (outdoor) area");


make_word("mama","母","母");
add_definition("n","parent, mother, father");
add_definition("mod","of the parent, parental, maternal, fatherly");


make_word("mani","貝","元 or 贝");
add_definition("n","money, material wealth, currency, dollar, capital");


make_word("meli","女","女");
add_definition("n","woman, female, girl, wife, girlfriend");
add_definition("mod","female, feminine, womanly");


make_word("mi","私","我");
add_definition("n","I, we");
add_definition("mod","my, our");


make_word("mije","男","男");
add_definition("n","man, male, boy, husband, boyfriend");
add_definition("mod","male, masculine, manly");


make_word("moku","食","菜");
add_definition("n","food, meal");
add_definition("vt","eat, drink, swallow, ingest, consume");


make_word("moli","死","死");
add_definition("n","death");
add_definition("vi","die, be dead");
add_definition("vt","kill");
add_definition("mod","dead, deadly, fatal");


make_word("monsi","後","后");
add_definition("n","back, rear end, butt, behind");
add_definition("mod","back, rear");


make_word("mu","む","吽");
add_definition("interj","woof! meow! moo! etc. (cute animal noise)");


make_word("mun","月","月");
add_definition("n","moon");
add_definition("mod","lunar");


make_word("musi","楽","玩");
add_definition("n","fun, playing, game, recreation, art, entertainment");
add_definition("mod","artful, fun, recreational");
add_definition("vi","play, have fun");
add_definition("vt","amuse, entertain");


make_word("mute","多","大");
add_definition("mod","many, very, much, several, a lot, abundant, numerous, more");
add_definition("n","amount, quantity");
add_definition("vt","make many or much");


make_word("namako","冗");

make_word("nanpa","番","个");
add_definition("n","number");
add_definition("oth","-th (ordinal numbers)");

make_word("nasa","狂","怪");
add_definition("mod","silly, crazy, foolish, drunk, strange, stupid, weird");
add_definition("vt","drive crazy, make weird");


make_word("nasin","道","道");
add_definition("n","way, manner, custom, road, path, doctrine, system, method");


make_word("nena","丘","山");
add_definition("n","bump, nose, hill, mountain, button");


make_word("ni","此","这");
add_definition("mod","this, that");


make_word("nimi","称","名");
add_definition("n","word, name");


make_word("noka","足","足");
add_definition("n","leg, foot");


make_word("o","お","令");
add_definition("sep","O (vocative or imperative)");
add_definition("interj","hey! (calling somebody's attention)");


make_word("oko","目","目");
add_definition("n","eye");


make_word("olin","愛","爱");
add_definition("n","love");
add_definition("mod","love");
add_definition("vt","to love (a person)");


make_word("ona","彼","他");
add_definition("n","she, he, it, they");
add_definition("mod","her, his, its, their");


make_word("open","開","开");
add_definition("vt","open, turn on");


make_word("pakala","打","打");
add_definition("n","blunder, accident, mistake, destruction, damage, breaking");
add_definition("vt","screw up, fuck up, botch, ruin, break, hurt, injure, damage, spoil, ruin");
add_definition("vi","screw up, fall apart, break");
add_definition("interj","damn! fuck!");


make_word("pali","作","工");
add_definition("n","activity, work, deed, project");
add_definition("mod","active, work-related, operating, working");
add_definition("vt","do, make, build, create");
add_definition("vi","act, work, function");


make_word("palisa","棒","支");
add_definition("n","long, mostly hard object, e.g. rod, stick, branch");


make_word("pan","米","米");
add_definition("n","grain, cereal");


make_word("pana","授","给");
add_definition("vt","give, put, send, place, release, emit, cause");
add_definition("n","giving, transfer, exchange");


make_word("pata","氏");
add_definition("n","(obsolete) brother");


make_word("pi","ぴ","的");
add_definition("sep","of, belonging to");


make_word("pilin","心","想 or 心");
add_definition("n","feelings, emotion, heart");
add_definition("vi","feel");
add_definition("vt","feel, think, sense, touch");


make_word("pimeja","黒","黑");
add_definition("mod","black, dark");
add_definition("n","darkness, shadows");
add_definition("vt","darken");


make_word("pini","終","末");
add_definition("n","end, tip");
add_definition("mod","completed, finished, past, done, ago");
add_definition("vt","finish, close, end, turn off");


make_word("pipi","虫","虫");
add_definition("n","bug, insect, spider");


make_word("poka","側","旁");
add_definition("n","side, hip, next to");
add_definition("prep","in the accompaniment of, with");
add_definition("mod","neighbouring");


make_word("poki","箱","包");
add_definition("n","container, box, bowl, cup, glass");


make_word("pona","良","好");
add_definition("n","good, simplicity, positivity");
add_definition("mod","good, simple, positive, nice, correct, right");
add_definition("interj","great! good! thanks! OK! cool! yay!");
add_definition("vt","improve, fix, repair, make good");
add_definition("describer","beneficial; good");
add_definition("describer","benevolent; altruistic, kind, symbiotic");
add_definition("describer","helpful; cooperating");
add_definition("describer","ideal");
add_definition("describer","conducive to overall wellness");


make_word("sama","同","同");
add_definition("mod","same, similar, equal, of equal status or position");
add_definition("prep","like, as, seem");
add_definition("adjective","filling the same or a similar role; equivalent");
add_definition("adjective","self-");


make_word("seli","火","火");
add_definition("n","fire, warmth, heat");
add_definition("mod","hot, warm, cooked");
add_definition("vt","heat, warm up, cook");
add_definition("noun","fire, a force of nature or chemical reaction that releases heat and light, potentially causing destruction; fire, lightning, explosion, exothermic reaction");
add_definition("noun","heat source, something that provides heat");
add_definition("noun","cooking source, something that provides heat for preparing food");
add_definition("noun","light source, something that provides light");
add_definition("transitive-verb","to use seli on");


make_word("selo","皮","甲");
add_definition("n","outside, surface, skin, shell, bark, shape, peel");


make_word("seme","何","什");
add_definition("oth","what, which, wh- (question word)");


make_word("sewi","上","上");
add_definition("n","high, up, above, top, over, on");
add_definition("mod","superior, elevated, religious, formal");


make_word("sijelo","体","身");
add_definition("n","body, physical state");


make_word("sike","丸","回");
add_definition("n","circle, wheel, sphere, ball, cycle");
add_definition("mod","round, cyclical");


make_word("sin","新","新");
add_definition("mod","new, fresh, another, more");
add_definition("vt","renew, renovate, freshen");


make_word("sina","君","你");
add_definition("n","you");
add_definition("mod","your");


make_word("sinpin","前","前");
add_definition("n","front, chest, torso, face, wall");


make_word("sitelen","画","画");
add_definition("n","picture, image");
add_definition("vt","draw, write");
add_definition("noun","representation, a visual or tactile work that serves to show, describe, explain or remind us of something else; representation, model");
add_definition("noun","picture, specific lines and shapes marked on a surface; drawing, print, painting, image, sign, sketch, outline, blueprint, etching, picture");
add_definition("noun","diagram, chart, graph");
add_definition("noun","sculpture , an object made into the shape of something; carving, sculpture, figurine, replica");


make_word("sona","知","知");
add_definition("n","knowledge, wisdom, intelligence, understanding");
add_definition("vt","know, understand, know how to");
add_definition("vi","know, understand");
add_definition("kama","learn, study");


make_word("soweli","猫","马 or 牛");
add_definition("n","animal, especially land mammal, lovable animal");


make_word("suli","大","高");
add_definition("mod","big, tall, long, adult, important");
add_definition("vt","enlarge, lengthen");
add_definition("n","size");


make_word("suno","日","日 or 光");
add_definition("n","sun, light");
add_definition("describer","having the qualities or characteristics of suno, shiny");


make_word("supa","面","张");
add_definition("n","horizontal surface, e.g furniture, table, chair, pillow, floor");
add_definition("noun","supporting platform, surface");


make_word("suwi","甜","甜");
add_definition("n","candy, sweet food");
add_definition("mod","sweet, cute");
add_definition("vt","sweeten");


make_word("tan","因","从");
add_definition("prep","from, by, because of, since");
add_definition("n","origin, cause");

make_word("taso","許","只");
add_definition("mod","only, sole");
add_definition("conj","but");


make_word("tawa","去","去");
add_definition("prep","to, in order to, towards, for, until");
add_definition("vi","go to, walk, travel, move, leave");
add_definition("n","movement, transportation");
add_definition("mod","moving, mobile");
add_definition("vt","move, displace");


make_word("telo","水","水");
add_definition("n","water, liquid, juice, sauce");
add_definition("vt","water, wash with water");
add_definition("noun","liquid, a flowing wet substance; liquid, fluid, water");
add_definition("noun","beverage, a liquid for drinking; beverage, drink, water, juice");
add_definition("noun","a liquid for washing; water");
add_definition("noun","body fluid, a liquid that comes out of the body; blood, milk, saliva, semen, sweat, tears, urine");
add_definition("noun","body of water, an area covered with water; bay, strait, sea, lake, river, stream");
add_definition("transitive-verb","to use<b>telo</b>on; to water, rinse, wash, wet");
add_definition("describer","having the characteristics or properties of<b>telo</b>; wet, liquid");


make_word("tenpo","时","时");
add_definition("n","time, period of time, moment, duration, situation");


make_word("toki","言","言");
add_definition("n","language, talking, speech, communication");
add_definition("mod","talking, verbal");
add_definition("vt","say");
add_definition("vi","talk, chat, communicate");
add_definition("interj","hello! hi!");
add_definition("transitive-verb","to give and receive (<b>e</b>: information) (<b>tawa</b>: with); to communicate");
add_definition("transitive-verb","to put together (<b>e</b>: thoughts or ideas); to think out");


make_word("tomo","家","穴");
add_definition("n","indoor constructed space, e.g. house, home, room, building");
add_definition("mod","urban, domestic, household");


make_word("tu","二","二");
add_definition("mod","two");
add_definition("n","duo, pair");
add_definition("vt","double, separate/cut/divide in two");

make_word("uta","口","口");
add_definition("n","mouth");
add_definition("mod","oral");
add_definition("noun","mouth, the part of the human body that includes the lips and everything inside the mouth and throat; mouth, oral cavity, throat, pharynx, lips");
add_definition("noun","maw, a similar part in an animal's body, used for eating, sucking or grooming; beak, bill, rostrum, jaw, proboscis");


make_word("utala","戦","战");
add_definition("n","conflict, disharmony, competition, fight, war, battle, attack, blow, argument, physical or verbal violence");
add_definition("vt","hit, strike, attack, compete against");


make_word("walo","白","白");
add_definition("mod","white, light (colour)");
add_definition("n","white thing or part, whiteness, lightness");


make_word("wan","一","一");
add_definition("mod","one, a");
add_definition("n","unit, element, particle, part, piece");
add_definition("vt","unite, make one");



make_word("waso","鳥","鸟");
add_definition("n","bird, winged animal");


make_word("wawa","力","力");
add_definition("n","energy, strength, power");
add_definition("mod","energetic, strong, fierce, intense, sure, confident");
add_definition("vt","strengthen, energize, empower");


make_word("weka","遥","脱");
add_definition("mod","away, absent, missing");
add_definition("n","absence");
add_definition("vt","throw away, remove, get rid of");


make_word("wile","要","要");
add_definition("vt","to want, need, wish, have to, must, will, should");
add_definition("n","desire, need, will");
add_definition("mod","necessary");


exit("success");