DONT DELETE THE TABLE;
ITS DATA IS VALUEEABLE;

ASDFIODSAF ;
ASDFJ SA ;
/*
INSERT INTO languages (lang_id, lang_dispname) VALUES ('la', 'Latin');
INSERT INTO languages (lang_id, lang_dispname) VALUES ('en', 'English');
INSERT INTO lang_aliases (lang_id, lang_name) VALUES ('la', 'lingua latina');
INSERT INTO words (word_lang, word_name, word_spart) VALUES ('la', 'amicus', 'noun');
INSERT INTO forms (word_id, form_tag, form_value) VALUES (
    (SELECT word_id FROM words WHERE word_name LIKE 'amicus' AND word_lang LIKE 'la'), 'nominative/singular', 'amicus'
);
INSERT INTO forms (word_id, form_tag, form_value) VALUES (
    (SELECT word_id FROM words WHERE word_name LIKE 'amicus' AND word_lang LIKE 'la'), 'plural', NULL
);
INSERT INTO definitions (form_id, def_lang, def_type, def_value) VALUES (
    (SELECT form_id FROM forms  WHERE word_id = (
        SELECT word_id FROM words WHERE word_name LIKE 'amicus' AND word_lang LIKE 'la'
    ) AND form_tag LIKE 'plural'),
    'en', 'translation', 'friends'
);
INSERT INTO pronunciations (form_id, pron_type, pronunciation) VALUES (
    (SELECT form_id FROM forms WHERE word_id = (
        SELECT word_id FROM words WHERE word_name LIKE 'amicus' AND word_lang LIKE 'la'
    ) AND form_tag LIKE 'nominative/singular'),
    'IPA', 'a\'mi:.cus'
);
INSERT INTO attributes (word_id, attr_tag, attr_value) VALUES (
    (SELECT word_id FROM words WHERE word_name LIKE 'amicus' AND word_lang LIKE 'la'),
    'type-of-person-noun', 'true'
);
INSERT INTO words (word_lang, word_name, word_spart) VALUES ('la', 'verbero', 'verb');
INSERT INTO words (word_lang, word_name, word_spart) VALUES ('la', 'fessus', 'adjective');
*/
