<?php
/*
 * NOTE: this file is auto-generated, PLEASE do NOT edit!
 */

$sql_stmts = [];
/************************
 * WORDS
 ************************/

/*
 * Find the word's name
 */
$sql_stmts['word_id->word_name']= "SELECT word_name FROM words WHERE word_id = (?)";
/*
 * Converse: find words by name
 */
$sql_stmts['word_name->word_id']= "SELECT word_id FROM words WHERE word_name = (?)";

/*
 * Find the word's inflection cache
 */
$sql_stmts['word_id->inflection_cache']= "SELECT inflection_cache FROM words WHERE word_id = (?)";

/*
 * Find the word's language
 */
$sql_stmts['word_id->word_lang']= "SELECT word_lang FROM words WHERE word_id = (?)";
/*
 * Converse: find words by language
 */
$sql_stmts['word_lang->word_id']= "SELECT word_id FROM words WHERE word_lang = (?)";

/*
 * Find the word's part of speech
 */
$sql_stmts['word_id->word_spart']= "SELECT word_spart FROM words WHERE word_id = (?)";
/*
 * Converse: find words by part of speech
 */
$sql_stmts['word_spart->word_id']= "SELECT word_id FROM words WHERE word_spart = (?)";

/*
 * When was the word updated last?
 */
$sql_stmts['word_id->last_changed']= "SELECT last_changed FROM words WHERE word_id = (?)";

/*
 * Make a new word
 */
$sql_stmts['word_name,word_lang,word_spart->new in words']= "INSERT INTO words (word_name,word_lang,word_spart) VALUES (?, ?, ?)";
$sql_stmts['word_lang,word_name,word_spart->new in words']= "INSERT INTO words (word_lang,word_name,word_spart) VALUES (?, ?, ?)";
$sql_stmts['word_name,word_spart,word_lang->new in words']= "INSERT INTO words (word_name,word_spart,word_lang) VALUES (?, ?, ?)";
$sql_stmts['word_spart,word_name,word_lang->new in words']= "INSERT INTO words (word_spart,word_name,word_lang) VALUES (?, ?, ?)";
$sql_stmts['word_lang,word_spart,word_name->new in words']= "INSERT INTO words (word_lang,word_spart,word_name) VALUES (?, ?, ?)";
$sql_stmts['word_spart,word_lang,word_name->new in words']= "INSERT INTO words (word_spart,word_lang,word_name) VALUES (?, ?, ?)";
$sql_stmts['word_name,word_lang,word_spart->word_id']= "SELECT word_id FROM words WHERE word_name = (?) AND word_lang = (?) AND word_spart = (?)";
$sql_stmts['word_lang,word_name,word_spart->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_name = (?) AND word_spart = (?)";
$sql_stmts['word_name,word_spart,word_lang->word_id']= "SELECT word_id FROM words WHERE word_name = (?) AND word_spart = (?) AND word_lang = (?)";
$sql_stmts['word_spart,word_name,word_lang->word_id']= "SELECT word_id FROM words WHERE word_spart = (?) AND word_name = (?) AND word_lang = (?)";
$sql_stmts['word_lang,word_spart,word_name->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_spart = (?) AND word_name = (?)";
$sql_stmts['word_spart,word_lang,word_name->word_id']= "SELECT word_id FROM words WHERE word_spart = (?) AND word_lang = (?) AND word_name = (?)";

/*
 * Delete a word
 */
$sql_stmts['word_id->delete from words']= "DELETE FROM words WHERE (word_id = (?))";

$sql_stmts['word_id->word_name=']= "UPDATE words SET word_name = (?) WHERE word_id = (?)";
$sql_stmts['word_id->inflection_cache=']= "UPDATE words SET inflection_cache = (?) WHERE word_id = (?)";

$sql_stmts['word_id->word_info']= "SELECT CONCAT(
        -- '#',word_id,
        ': ',word_name,
        ' (',REPLACE((
            SELECT def_value from definitions where definitions.word_id = words.word_id LIMIT 1
        ),'\n',''),')'
    )
    FROM words WHERE word_id = (?)";

$sql_stmts['word_id->word_info_formatted']= "SELECT CONCAT(
        -- '#',word_id,': ',
        '<a class=\"word-ref format-word-',
        word_lang,
        '\" href=\"dictionary.php?id=',
        word_id,
        '\">',word_name,'</a>',
        IF((EXISTS (SELECT 1 FROM definitions WHERE definitions.word_id = words.word_id)),
            CONCAT(' (“',REPLACE((
                SELECT def_value FROM definitions WHERE definitions.word_id = words.word_id LIMIT 1
            ),'\n','”, “'),'”)'),
            ''
        )
    )
    FROM words WHERE word_id = (?)";

/************************
 * FORMS
 ************************/

$sql_stmts['form_id->word_id']= "SELECT word_id FROM forms WHERE form_id = (?)";
$sql_stmts['word_id->form_id']= "SELECT form_id FROM forms WHERE word_id = (?)";
$sql_stmts['word_id,form_tag->form_id']= "SELECT form_id FROM forms WHERE word_id = (?) AND form_tag = (?)";
$sql_stmts['form_tag,word_id->form_id']= "SELECT form_id FROM forms WHERE form_tag = (?) AND word_id = (?)";
$sql_stmts['word_id,form_tag,form_value->form_id']= "SELECT form_id FROM forms WHERE word_id = (?) AND form_tag = (?) AND form_value = (?)";
$sql_stmts['form_tag,word_id,form_value->form_id']= "SELECT form_id FROM forms WHERE form_tag = (?) AND word_id = (?) AND form_value = (?)";
$sql_stmts['word_id,form_value,form_tag->form_id']= "SELECT form_id FROM forms WHERE word_id = (?) AND form_value = (?) AND form_tag = (?)";
$sql_stmts['form_value,word_id,form_tag->form_id']= "SELECT form_id FROM forms WHERE form_value = (?) AND word_id = (?) AND form_tag = (?)";
$sql_stmts['form_tag,form_value,word_id->form_id']= "SELECT form_id FROM forms WHERE form_tag = (?) AND form_value = (?) AND word_id = (?)";
$sql_stmts['form_value,form_tag,word_id->form_id']= "SELECT form_id FROM forms WHERE form_value = (?) AND form_tag = (?) AND word_id = (?)";

$sql_stmts['form_tag->form_id']= "SELECT form_id FROM forms WHERE form_tag = (?)";
$sql_stmts['form_id->form_tag']= "SELECT form_tag FROM forms WHERE form_id = (?)";
$sql_stmts['form_id->form_tag=']= "UPDATE forms SET form_tag = (?) WHERE form_id = (?)";
$sql_stmts['form_value->form_id']= "SELECT form_id FROM forms WHERE form_value = (?)";
$sql_stmts['form_id->form_value']= "SELECT form_value FROM forms WHERE form_id = (?)";
$sql_stmts['form_id->form_value=']= "UPDATE forms SET form_value = (?) WHERE form_id = (?)";
;

$sql_stmts['word_id,form_tag,form_value->new in forms']= "INSERT INTO forms (word_id,form_tag,form_value) VALUES (?, ?, ?)";
$sql_stmts['form_tag,word_id,form_value->new in forms']= "INSERT INTO forms (form_tag,word_id,form_value) VALUES (?, ?, ?)";
$sql_stmts['word_id,form_value,form_tag->new in forms']= "INSERT INTO forms (word_id,form_value,form_tag) VALUES (?, ?, ?)";
$sql_stmts['form_value,word_id,form_tag->new in forms']= "INSERT INTO forms (form_value,word_id,form_tag) VALUES (?, ?, ?)";
$sql_stmts['form_tag,form_value,word_id->new in forms']= "INSERT INTO forms (form_tag,form_value,word_id) VALUES (?, ?, ?)";
$sql_stmts['form_value,form_tag,word_id->new in forms']= "INSERT INTO forms (form_value,form_tag,word_id) VALUES (?, ?, ?)";
$sql_stmts['form_id->delete from forms']= "DELETE FROM forms WHERE (form_id = (?))";
/*word_id,form_tag,form_value->new in forms*/


/************************
 * ATTRIBUTES
 ************************/

$sql_stmts['attr_tag->word_id']= "SELECT word_id FROM attributes WHERE attr_tag = (?)";
$sql_stmts['word_id->attr_tag']= "SELECT attr_tag FROM attributes WHERE word_id = (?)";
$sql_stmts['attr_value->word_id']= "SELECT word_id FROM attributes WHERE attr_value = (?)";
$sql_stmts['word_id->attr_value']= "SELECT attr_value FROM attributes WHERE word_id = (?)";
$sql_stmts['attr_tag,attr_value->word_id']= "SELECT word_id FROM attributes WHERE attr_tag = (?) AND attr_value = (?)";
$sql_stmts['attr_value,attr_tag->word_id']= "SELECT word_id FROM attributes WHERE attr_value = (?) AND attr_tag = (?)";
$sql_stmts['word_id->attr_tag,attr_value']= "SELECT attr_tag,attr_value FROM attributes WHERE word_id = (?)";
$sql_stmts['word_id,attr_tag->attr_value']= "SELECT attr_value FROM attributes WHERE word_id = (?) AND attr_tag = (?)";
$sql_stmts['attr_tag,word_id->attr_value']= "SELECT attr_value FROM attributes WHERE attr_tag = (?) AND word_id = (?)";
$sql_stmts['word_id,attr_tag,attr_value->new in attributes']= "INSERT INTO attributes (word_id,attr_tag,attr_value) VALUES (?, ?, ?)";
$sql_stmts['attr_tag,word_id,attr_value->new in attributes']= "INSERT INTO attributes (attr_tag,word_id,attr_value) VALUES (?, ?, ?)";
$sql_stmts['word_id,attr_value,attr_tag->new in attributes']= "INSERT INTO attributes (word_id,attr_value,attr_tag) VALUES (?, ?, ?)";
$sql_stmts['attr_value,word_id,attr_tag->new in attributes']= "INSERT INTO attributes (attr_value,word_id,attr_tag) VALUES (?, ?, ?)";
$sql_stmts['attr_tag,attr_value,word_id->new in attributes']= "INSERT INTO attributes (attr_tag,attr_value,word_id) VALUES (?, ?, ?)";
$sql_stmts['attr_value,attr_tag,word_id->new in attributes']= "INSERT INTO attributes (attr_value,attr_tag,word_id) VALUES (?, ?, ?)";
$sql_stmts['word_id,attr_tag->delete from attributes']= "DELETE FROM attributes WHERE (word_id = (?) AND attr_tag = (?))";


/************************
 * DEFINITIONS
 ************************/

$sql_stmts['word_id->def_id']= "SELECT def_id FROM definitions WHERE word_id = (?)";
$sql_stmts['def_id->word_id']= "SELECT word_id FROM definitions WHERE def_id = (?)";
$sql_stmts['form_tag->def_id']= "SELECT def_id FROM definitions WHERE form_tag = (?)";
$sql_stmts['def_id->form_tag']= "SELECT form_tag FROM definitions WHERE def_id = (?)";
$sql_stmts['word_id,form_tag->def_id']= "SELECT def_id FROM definitions WHERE word_id = (?) AND form_tag = (?)";
$sql_stmts['form_tag,word_id->def_id']= "SELECT def_id FROM definitions WHERE form_tag = (?) AND word_id = (?)";
$sql_stmts['def_id->word_id,form_tag']= "SELECT word_id,form_tag FROM definitions WHERE def_id = (?)";

$sql_stmts['word_id,def_value,def_lang->def_id']= "SELECT def_id FROM definitions WHERE word_id = (?) AND def_value = (?) AND def_lang = (?)";
$sql_stmts['def_value,word_id,def_lang->def_id']= "SELECT def_id FROM definitions WHERE def_value = (?) AND word_id = (?) AND def_lang = (?)";
$sql_stmts['word_id,def_lang,def_value->def_id']= "SELECT def_id FROM definitions WHERE word_id = (?) AND def_lang = (?) AND def_value = (?)";
$sql_stmts['def_lang,word_id,def_value->def_id']= "SELECT def_id FROM definitions WHERE def_lang = (?) AND word_id = (?) AND def_value = (?)";
$sql_stmts['def_value,def_lang,word_id->def_id']= "SELECT def_id FROM definitions WHERE def_value = (?) AND def_lang = (?) AND word_id = (?)";
$sql_stmts['def_lang,def_value,word_id->def_id']= "SELECT def_id FROM definitions WHERE def_lang = (?) AND def_value = (?) AND word_id = (?)";
$sql_stmts['def_id->form_tag=']= "UPDATE definitions SET form_tag = (?) WHERE def_id = (?)";

$sql_stmts['def_sense->def_id']= "SELECT def_id FROM definitions WHERE def_sense = (?)";
$sql_stmts['def_id->def_sense']= "SELECT def_sense FROM definitions WHERE def_id = (?)";
$sql_stmts['def_id->def_sense=']= "UPDATE definitions SET def_sense = (?) WHERE def_id = (?)";
$sql_stmts['def_lang->def_id']= "SELECT def_id FROM definitions WHERE def_lang = (?)";
$sql_stmts['def_id->def_lang']= "SELECT def_lang FROM definitions WHERE def_id = (?)";
$sql_stmts['def_id->def_lang=']= "UPDATE definitions SET def_lang = (?) WHERE def_id = (?)";
$sql_stmts['def_src->def_id']= "SELECT def_id FROM definitions WHERE def_src = (?)";
$sql_stmts['def_id->def_src']= "SELECT def_src FROM definitions WHERE def_id = (?)";
$sql_stmts['def_id->def_src=']= "UPDATE definitions SET def_src = (?) WHERE def_id = (?)";
$sql_stmts['def_type->def_id']= "SELECT def_id FROM definitions WHERE def_type = (?)";
$sql_stmts['def_id->def_type']= "SELECT def_type FROM definitions WHERE def_id = (?)";
$sql_stmts['def_id->def_type=']= "UPDATE definitions SET def_type = (?) WHERE def_id = (?)";
$sql_stmts['def_value->def_id']= "SELECT def_id FROM definitions WHERE def_value = (?)";
$sql_stmts['def_id->def_value']= "SELECT def_value FROM definitions WHERE def_id = (?)";
$sql_stmts['def_id->def_value=']= "UPDATE definitions SET def_value = (?) WHERE def_id = (?)";
;

$sql_stmts['word_id,def_value,def_lang->new in definitions']= "INSERT INTO definitions (word_id,def_value,def_lang) VALUES (?, ?, ?)";
$sql_stmts['def_value,word_id,def_lang->new in definitions']= "INSERT INTO definitions (def_value,word_id,def_lang) VALUES (?, ?, ?)";
$sql_stmts['word_id,def_lang,def_value->new in definitions']= "INSERT INTO definitions (word_id,def_lang,def_value) VALUES (?, ?, ?)";
$sql_stmts['def_lang,word_id,def_value->new in definitions']= "INSERT INTO definitions (def_lang,word_id,def_value) VALUES (?, ?, ?)";
$sql_stmts['def_value,def_lang,word_id->new in definitions']= "INSERT INTO definitions (def_value,def_lang,word_id) VALUES (?, ?, ?)";
$sql_stmts['def_lang,def_value,word_id->new in definitions']= "INSERT INTO definitions (def_lang,def_value,word_id) VALUES (?, ?, ?)";
$sql_stmts['word_id,def_value,def_lang,form_tag->new in definitions']= "INSERT INTO definitions (word_id,def_value,def_lang,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,word_id,def_lang,form_tag->new in definitions']= "INSERT INTO definitions (def_value,word_id,def_lang,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,def_lang,def_value,form_tag->new in definitions']= "INSERT INTO definitions (word_id,def_lang,def_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,word_id,def_value,form_tag->new in definitions']= "INSERT INTO definitions (def_lang,word_id,def_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,def_lang,word_id,form_tag->new in definitions']= "INSERT INTO definitions (def_value,def_lang,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,def_value,word_id,form_tag->new in definitions']= "INSERT INTO definitions (def_lang,def_value,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,def_value,form_tag,def_lang->new in definitions']= "INSERT INTO definitions (word_id,def_value,form_tag,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,word_id,form_tag,def_lang->new in definitions']= "INSERT INTO definitions (def_value,word_id,form_tag,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,def_value,def_lang->new in definitions']= "INSERT INTO definitions (word_id,form_tag,def_value,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,def_value,def_lang->new in definitions']= "INSERT INTO definitions (form_tag,word_id,def_value,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,form_tag,word_id,def_lang->new in definitions']= "INSERT INTO definitions (def_value,form_tag,word_id,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,def_value,word_id,def_lang->new in definitions']= "INSERT INTO definitions (form_tag,def_value,word_id,def_lang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,def_lang,form_tag,def_value->new in definitions']= "INSERT INTO definitions (word_id,def_lang,form_tag,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,word_id,form_tag,def_value->new in definitions']= "INSERT INTO definitions (def_lang,word_id,form_tag,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,def_lang,def_value->new in definitions']= "INSERT INTO definitions (word_id,form_tag,def_lang,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,def_lang,def_value->new in definitions']= "INSERT INTO definitions (form_tag,word_id,def_lang,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,form_tag,word_id,def_value->new in definitions']= "INSERT INTO definitions (def_lang,form_tag,word_id,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,def_lang,word_id,def_value->new in definitions']= "INSERT INTO definitions (form_tag,def_lang,word_id,def_value) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,def_lang,form_tag,word_id->new in definitions']= "INSERT INTO definitions (def_value,def_lang,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,def_value,form_tag,word_id->new in definitions']= "INSERT INTO definitions (def_lang,def_value,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['def_value,form_tag,def_lang,word_id->new in definitions']= "INSERT INTO definitions (def_value,form_tag,def_lang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,def_value,def_lang,word_id->new in definitions']= "INSERT INTO definitions (form_tag,def_value,def_lang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['def_lang,form_tag,def_value,word_id->new in definitions']= "INSERT INTO definitions (def_lang,form_tag,def_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,def_lang,def_value,word_id->new in definitions']= "INSERT INTO definitions (form_tag,def_lang,def_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['def_id->delete from definitions']= "DELETE FROM definitions WHERE (def_id = (?))";

/************************
 * PRONUNCIATIONS
 ************************/

$sql_stmts['word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?)";
$sql_stmts['pron_id->word_id']= "SELECT word_id FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['form_tag->pron_id']= "SELECT pron_id FROM pronunciations WHERE form_tag = (?)";
$sql_stmts['pron_id->form_tag']= "SELECT form_tag FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['word_id,form_tag->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?) AND form_tag = (?)";
$sql_stmts['form_tag,word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE form_tag = (?) AND word_id = (?)";
$sql_stmts['pron_id->word_id,form_tag']= "SELECT word_id,form_tag FROM pronunciations WHERE pron_id = (?)";

$sql_stmts['word_id,pron_value,pron_sublang->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?) AND pron_value = (?) AND pron_sublang = (?)";
$sql_stmts['pron_value,word_id,pron_sublang->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_value = (?) AND word_id = (?) AND pron_sublang = (?)";
$sql_stmts['word_id,pron_sublang,pron_value->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?) AND pron_sublang = (?) AND pron_value = (?)";
$sql_stmts['pron_sublang,word_id,pron_value->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_sublang = (?) AND word_id = (?) AND pron_value = (?)";
$sql_stmts['pron_value,pron_sublang,word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_value = (?) AND pron_sublang = (?) AND word_id = (?)";
$sql_stmts['pron_sublang,pron_value,word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_sublang = (?) AND pron_value = (?) AND word_id = (?)";
$sql_stmts['pron_id->form_tag=']= "UPDATE pronunciations SET form_tag = (?) WHERE pron_id = (?)";

$sql_stmts['pron_sublang->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_sublang = (?)";
$sql_stmts['pron_id->pron_sublang']= "SELECT pron_sublang FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['pron_id->pron_sublang=']= "UPDATE pronunciations SET pron_sublang = (?) WHERE pron_id = (?)";
$sql_stmts['pron_src->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_src = (?)";
$sql_stmts['pron_id->pron_src']= "SELECT pron_src FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['pron_id->pron_src=']= "UPDATE pronunciations SET pron_src = (?) WHERE pron_id = (?)";
$sql_stmts['pron_type->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_type = (?)";
$sql_stmts['pron_id->pron_type']= "SELECT pron_type FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['pron_id->pron_type=']= "UPDATE pronunciations SET pron_type = (?) WHERE pron_id = (?)";
$sql_stmts['pron_value->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_value = (?)";
$sql_stmts['pron_id->pron_value']= "SELECT pron_value FROM pronunciations WHERE pron_id = (?)";
$sql_stmts['pron_id->pron_value=']= "UPDATE pronunciations SET pron_value = (?) WHERE pron_id = (?)";
;

$sql_stmts['word_id,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_sublang) VALUES (?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_sublang) VALUES (?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_value) VALUES (?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_value) VALUES (?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,word_id) VALUES (?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,word_id) VALUES (?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_type) VALUES (?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_type) VALUES (?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_value) VALUES (?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_value) VALUES (?, ?, ?)";
$sql_stmts['pron_value,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,word_id) VALUES (?, ?, ?)";
$sql_stmts['pron_type,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,word_id) VALUES (?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_sublang,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_sublang,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_value,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_value,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,word_id,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,word_id,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_type,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_type,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_value,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_value,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,word_id,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,word_id,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_type,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_type,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_sublang,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_sublang,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,pron_type,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,pron_type,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,pron_sublang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,pron_sublang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_sublang,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_sublang,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,form_tag,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,form_tag,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_value,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_value,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,word_id,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,word_id,pron_sublang) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,form_tag,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,form_tag,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_sublang,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_sublang,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_sublang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_sublang,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_type,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_type,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_value,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,word_id,form_tag) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,form_tag,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,form_tag,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_value,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_value,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,word_id,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,word_id,pron_type) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,form_tag,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,form_tag,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_type,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_type,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,word_id,pron_value) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,form_tag,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_type,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_type,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,pron_value,word_id) VALUES (?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_sublang,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_sublang,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_sublang,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_sublang,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_value,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_value,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_value,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_value,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,word_id,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,word_id,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,word_id,pron_type,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,word_id,pron_type,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_type,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_type,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_type,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_value,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_value,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_value,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_value,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,word_id,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,word_id,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,word_id,pron_sublang,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,word_id,pron_sublang,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_type,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_type,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_type,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_type,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_sublang,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_sublang,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_sublang,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_sublang,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,word_id,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,word_id,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,word_id,pron_value,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,word_id,pron_value,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,pron_type,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,pron_type,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,pron_type,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,pron_type,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,pron_sublang,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,pron_sublang,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,pron_sublang,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,pron_sublang,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,pron_value,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,pron_value,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,pron_value,word_id,form_tag->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,pron_value,word_id,form_tag) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_sublang,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_sublang,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_sublang,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_sublang,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_value,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_value,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_value,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_value,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,word_id,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,word_id,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,word_id,form_tag,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,word_id,form_tag,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,form_tag,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,form_tag,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,form_tag,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,form_tag,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_value,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_value,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_value,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_value,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,word_id,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,word_id,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,word_id,pron_sublang,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,word_id,pron_sublang,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,form_tag,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,form_tag,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,form_tag,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,form_tag,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_sublang,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_sublang,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_sublang,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_sublang,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,word_id,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,word_id,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,word_id,pron_value,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,word_id,pron_value,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,form_tag,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,form_tag,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,form_tag,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,form_tag,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_sublang,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_sublang,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_sublang,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_sublang,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,pron_value,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,pron_value,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,pron_value,word_id,pron_type->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,pron_value,word_id,pron_type) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,pron_type,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,pron_type,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,pron_type,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_value,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_value,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_value,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_value,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,word_id,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,word_id,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,word_id,form_tag,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,word_id,form_tag,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,form_tag,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_value,form_tag,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,word_id,form_tag,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,word_id,form_tag,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_value,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_value,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_value,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_value,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,word_id,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,word_id,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,word_id,pron_type,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,word_id,pron_type,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,form_tag,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,form_tag,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,form_tag,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,form_tag,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_type,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_type,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_type,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_type,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,word_id,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,word_id,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,word_id,pron_value,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,word_id,pron_value,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,form_tag,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,form_tag,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,form_tag,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,form_tag,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_type,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_type,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_type,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_type,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,pron_value,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,pron_value,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,pron_value,word_id,pron_sublang->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,pron_value,word_id,pron_sublang) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,pron_type,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,pron_type,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,pron_type,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,pron_type,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,pron_sublang,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,pron_sublang,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,pron_sublang,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,pron_sublang,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,word_id,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,word_id,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,word_id,form_tag,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,word_id,form_tag,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_sublang,form_tag,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_sublang,form_tag,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,word_id,form_tag,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,word_id,form_tag,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_sublang,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_sublang,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_sublang,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_sublang,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,word_id,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,word_id,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,word_id,pron_type,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,word_id,pron_type,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_type,form_tag,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,pron_type,form_tag,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,word_id,form_tag,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,word_id,form_tag,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,form_tag,pron_type,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (word_id,form_tag,pron_type,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,word_id,pron_type,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,word_id,pron_type,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,word_id,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,word_id,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,word_id,pron_sublang,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,word_id,pron_sublang,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,form_tag,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,form_tag,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,form_tag,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,form_tag,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,pron_type,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,pron_type,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,pron_type,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,pron_type,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,pron_sublang,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,pron_sublang,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,pron_sublang,word_id,pron_value->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,pron_sublang,word_id,pron_value) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,pron_type,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,pron_type,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,pron_type,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,pron_type,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,pron_sublang,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,pron_sublang,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,pron_sublang,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,pron_sublang,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,pron_value,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,pron_value,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,pron_value,form_tag,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,pron_value,form_tag,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_sublang,form_tag,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_sublang,form_tag,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_value,form_tag,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_value,form_tag,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_sublang,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_sublang,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_sublang,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_sublang,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,pron_value,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,pron_value,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,pron_value,pron_type,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,pron_value,pron_type,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,pron_type,form_tag,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,pron_type,form_tag,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_value,form_tag,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_value,form_tag,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_value,form_tag,pron_type,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_value,form_tag,pron_type,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_value,pron_type,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_value,pron_type,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,pron_value,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,pron_value,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,pron_value,pron_sublang,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,pron_value,pron_sublang,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,pron_type,form_tag,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,pron_type,form_tag,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,pron_sublang,form_tag,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,pron_sublang,form_tag,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_sublang,form_tag,pron_type,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_sublang,form_tag,pron_type,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_sublang,pron_type,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_sublang,pron_type,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['pron_type,form_tag,pron_sublang,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (pron_type,form_tag,pron_sublang,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['form_tag,pron_type,pron_sublang,pron_value,word_id->new in pronunciations']= "INSERT INTO pronunciations (form_tag,pron_type,pron_sublang,pron_value,word_id) VALUES (?, ?, ?, ?, ?)";
$sql_stmts['word_id,pron_value,pron_type->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?) AND pron_value = (?) AND pron_type = (?)";
$sql_stmts['pron_value,word_id,pron_type->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_value = (?) AND word_id = (?) AND pron_type = (?)";
$sql_stmts['word_id,pron_type,pron_value->pron_id']= "SELECT pron_id FROM pronunciations WHERE word_id = (?) AND pron_type = (?) AND pron_value = (?)";
$sql_stmts['pron_type,word_id,pron_value->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_type = (?) AND word_id = (?) AND pron_value = (?)";
$sql_stmts['pron_value,pron_type,word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_value = (?) AND pron_type = (?) AND word_id = (?)";
$sql_stmts['pron_type,pron_value,word_id->pron_id']= "SELECT pron_id FROM pronunciations WHERE pron_type = (?) AND pron_value = (?) AND word_id = (?)";
$sql_stmts['pron_id->delete from pronunciations']= "DELETE FROM pronunciations WHERE (pron_id = (?))";

/************************
 * CONNECTIONS
 ************************/

$sql_stmts['from_word_id->to_word_id,connect_type']= "SELECT to_word_id,connect_type FROM connections WHERE from_word_id = (?)";
$sql_stmts['from_word_id,to_word_id,connect_type->new in connections']= "INSERT INTO connections (from_word_id,to_word_id,connect_type) VALUES (?, ?, ?)";
$sql_stmts['to_word_id,from_word_id,connect_type->new in connections']= "INSERT INTO connections (to_word_id,from_word_id,connect_type) VALUES (?, ?, ?)";
$sql_stmts['from_word_id,connect_type,to_word_id->new in connections']= "INSERT INTO connections (from_word_id,connect_type,to_word_id) VALUES (?, ?, ?)";
$sql_stmts['connect_type,from_word_id,to_word_id->new in connections']= "INSERT INTO connections (connect_type,from_word_id,to_word_id) VALUES (?, ?, ?)";
$sql_stmts['to_word_id,connect_type,from_word_id->new in connections']= "INSERT INTO connections (to_word_id,connect_type,from_word_id) VALUES (?, ?, ?)";
$sql_stmts['connect_type,to_word_id,from_word_id->new in connections']= "INSERT INTO connections (connect_type,to_word_id,from_word_id) VALUES (?, ?, ?)";
$sql_stmts['from_word_id,to_word_id,connect_type->delete from connections']= "DELETE FROM connections WHERE (from_word_id = (?) AND to_word_id = (?) AND connect_type = (?))";


/************************
 * QUIZZES
 ************************/

$sql_stmts['quiz_id->user_id']= "SELECT user_id FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->type']= "SELECT type FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->last']= "SELECT last FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->score']= "SELECT score FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->completed']= "SELECT completed FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->out_of']= "SELECT out_of FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->time_started']= "SELECT time_started FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->time_finished']= "SELECT time_finished FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->questions']= "SELECT questions FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->questions=']= "UPDATE quizzes SET questions = (?) WHERE quiz_id = (?)";

$sql_stmts['quiz_id->results']= "SELECT results FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->results=']= "UPDATE quizzes SET results = (?) WHERE quiz_id = (?)";

$sql_stmts['quiz_id->options_n']= "SELECT options_n FROM quizzes WHERE quiz_id = (?)";

$sql_stmts['quiz_id->options_n=']= "UPDATE quizzes SET options_n = (?) WHERE quiz_id = (?)";

$sql_stmts['user_id,last->new in quizzes']= "INSERT INTO quizzes (user_id,last) VALUES (?, ?)";
$sql_stmts['last,user_id->new in quizzes']= "INSERT INTO quizzes (last,user_id) VALUES (?, ?)";

$sql_stmts['user_id,type,last->new in quizzes']= "INSERT INTO quizzes (user_id,type,last) VALUES (?, ?, ?)";
$sql_stmts['type,user_id,last->new in quizzes']= "INSERT INTO quizzes (type,user_id,last) VALUES (?, ?, ?)";
$sql_stmts['user_id,last,type->new in quizzes']= "INSERT INTO quizzes (user_id,last,type) VALUES (?, ?, ?)";
$sql_stmts['last,user_id,type->new in quizzes']= "INSERT INTO quizzes (last,user_id,type) VALUES (?, ?, ?)";
$sql_stmts['type,last,user_id->new in quizzes']= "INSERT INTO quizzes (type,last,user_id) VALUES (?, ?, ?)";
$sql_stmts['last,type,user_id->new in quizzes']= "INSERT INTO quizzes (last,type,user_id) VALUES (?, ?, ?)";

$sql_stmts['user_id->last quiz_id']= "SELECT quiz_id FROM quizzes WHERE user_id = (?)
    ORDER BY quiz_id DESC LIMIT 1";

$sql_stmts['finish quiz']= "UPDATE quizzes SET
    time_finished = CURRENT_TIMESTAMP,
    completed = TRUE
    WHERE quiz_id = (?)";

$sql_stmts['add score']= "UPDATE quizzes SET
    score = score + (?),
    out_of = out_of + (?)
    WHERE quiz_id = (?)";

$sql_stmts['set score']= "UPDATE quizzes SET
    score = (?),
    out_of = (?)
    WHERE quiz_id = (?)";

$sql_stmts['user_id->quiz_id']= "SELECT quiz_id FROM quizzes WHERE user_id = (?)";

$sql_stmts['user_id->quiz_id reversed']= "SELECT quiz_id FROM quizzes
    WHERE user_id = (?)
    ORDER BY quiz_id DESC";

$sql_stmts['quiz_id->delete from quizzes']= "DELETE FROM quizzes WHERE (quiz_id = (?))";


/************************
 * OTHER
 ************************/

$sql_stmts['lang_id->lang_dispname']= "SELECT lang_dispname FROM languages WHERE lang_id = (?)";

/*
 * Find words by word_name and word_lang
 */
$sql_stmts['word_name,word_lang->word_id']= "SELECT word_id FROM words WHERE word_name = (?) AND word_lang = (?)";
$sql_stmts['word_lang,word_name->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_name = (?)";
$sql_stmts['word_name,word_lang,word_spart->word_id']= "SELECT word_id FROM words WHERE word_name = (?) AND word_lang = (?) AND word_spart = (?)";
$sql_stmts['word_lang,word_name,word_spart->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_name = (?) AND word_spart = (?)";
$sql_stmts['word_name,word_spart,word_lang->word_id']= "SELECT word_id FROM words WHERE word_name = (?) AND word_spart = (?) AND word_lang = (?)";
$sql_stmts['word_spart,word_name,word_lang->word_id']= "SELECT word_id FROM words WHERE word_spart = (?) AND word_name = (?) AND word_lang = (?)";
$sql_stmts['word_lang,word_spart,word_name->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_spart = (?) AND word_name = (?)";
$sql_stmts['word_spart,word_lang,word_name->word_id']= "SELECT word_id FROM words WHERE word_spart = (?) AND word_lang = (?) AND word_name = (?)";
$sql_stmts['word_lang,word_spart->word_id']= "SELECT word_id FROM words WHERE word_lang = (?) AND word_spart = (?)";
$sql_stmts['word_spart,word_lang->word_id']= "SELECT word_id FROM words WHERE word_spart = (?) AND word_lang = (?)";
$sql_stmts['word_spart->word_id']= "SELECT word_id FROM words WHERE word_spart = (?)";
$sql_stmts['word_lang->word_id']= "SELECT word_id FROM words WHERE word_lang = (?)";

/*
 * Find languages that use a certain speechpart
 */
$sql_stmts['word_spart->word_langs'] = $sql_stmts['word_id->word_lang'];
foreach (explode('|','(' . 
        $sql_stmts['word_spart->word_id'] . ')') as $arg) {
    $sql_stmts['word_spart->word_langs'] = substr_replace($sql_stmts['word_spart->word_langs'], trim($arg), strpos($sql_stmts['word_spart->word_langs'],'?'), strlen('?'));
};

$sql_stmts['all_sparts']= "SELECT word_spart FROM words";


return $sql_stmts;?>