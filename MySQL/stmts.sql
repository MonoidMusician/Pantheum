/************************
 * WORDS
 ************************/

/*
 * Find the word's name
 */
{{{defineselect|
    table=words&to=word_name&from=word_id
}}};
/*
 * Converse: find words by name
 */
{{{defineselect|
    table=words&to=word_id&from=word_name
}}};

/*
 * Find the word's inflection cache
 */
{{{defineselect|
    table=words&to=inflection_cache&from=word_id
}}};

/*
 * Find the word's language
 */
{{{defineselect|
    table=words&to=word_lang&from=word_id
}}};
/*
 * Converse: find words by language
 */
{{{defineselect|
    table=words&to=word_id&from=word_lang
}}};

/*
 * Find the word's part of speech
 */
{{{defineselect|
    table=words&to=word_spart&from=word_id
}}};
/*
 * Converse: find words by part of speech
 */
{{{defineselect|
    table=words&to=word_id&from=word_spart
}}};

/*
 * When was the word updated last?
 */
{{{defineselect|
    table=words&to=last_changed&from=word_id
}}};

/*
 * Make a new word
 */
{{{defineinsert|
    table=words&from=word_name,word_lang,word_spart}}};
{{{defineselect|
    to=word_id&table=words&from=word_name,word_lang,word_spart}}};

/*
 * Delete a word
 */
{{{definedelete|
    table=words&from=word_id}}};

{{{defineupdate|
    table=words&from=word_id&to=word_name}}};
{{{defineupdate|
    table=words&from=word_id&to=inflection_cache}}};

{{{define|word_id->word_info|
    SELECT CONCAT(
        -- '#',word_id,
        ': ',word_name,
        ' (',REPLACE((
            SELECT def_value from definitions where definitions.word_id = words.word_id LIMIT 1
        ),'\n',''),')'
    )
    FROM words WHERE word_id = (?)
}}};

{{{define|word_id->word_info_formatted|
    SELECT CONCAT(
        -- '#',word_id,': ',
        '<a class="word-ref format-word-',
        word_lang,
        '" href="dictionary.php?id=',
        word_id,
        '">',word_name,'</a>',
        IF((EXISTS (SELECT 1 FROM definitions WHERE definitions.word_id = words.word_id)),
            CONCAT(' (“',REPLACE((
                SELECT def_value FROM definitions WHERE definitions.word_id = words.word_id LIMIT 1
            ),'\n','”, “'),'”)'),
            ''
        )
    )
    FROM words WHERE word_id = (?)
}}};

/************************
 * FORMS
 ************************/

{{{defineselect|
    table=forms&to=word_id&from=form_id
}}};
{{{defineselect|
    table=forms&to=form_id&from=word_id
}}};
{{{defineselect|
    table=forms&to=form_id&from=word_id,form_tag
}}};
{{{defineselect|
    table=forms&to=form_id&from=word_id,form_tag,form_value
}}};

{{{attrs|
    table=forms&pre=form_&bi=tag,value
}}};

{{{defineinsert|
    table=forms&from=word_id,form_tag,form_value}}};
{{{definedelete|
    table=forms&from=form_id}}};
/*word_id,form_tag,form_value->new in forms*/


/************************
 * ATTRIBUTES
 ************************/

{{{defineselect|
    table=attributes&to=word_id&from=attr_tag
}}};
{{{defineselect|
    table=attributes&to=attr_tag&from=word_id
}}};
{{{defineselect|
    table=attributes&to=word_id&from=attr_value
}}};
{{{defineselect|
    table=attributes&to=attr_value&from=word_id
}}};
{{{defineselect|
    table=attributes&to=word_id&from=attr_tag,attr_value
}}};
{{{defineselect|
    table=attributes&to=attr_tag,attr_value&from=word_id
}}};
{{{defineselect|
    table=attributes&to=attr_value&from=word_id,attr_tag
}}};
{{{defineinsert|
    table=attributes&from=word_id,attr_tag,attr_value}}};
{{{definedelete|
    table=attributes&from=word_id,attr_tag}}};

{{{define|set attr|
    INSERT INTO attributes
        (word_id, attr_tag, attr_value)
    VALUES
        (?, ?, ?)
    ON DUPLICATE KEY UPDATE
        attr_value = VALUES(attr_value)
}}};

/************************
 * DEFINITIONS
 ************************/

{{{defineselect|
    table=definitions&to=def_id&from=word_id
}}};
{{{defineselect|
    table=definitions&to=word_id&from=def_id
}}};
{{{defineselect|
    table=definitions&to=def_id&from=form_tag
}}};
{{{defineselect|
    table=definitions&to=form_tag&from=def_id
}}};
{{{defineselect|
    table=definitions&to=def_id&from=word_id,form_tag
}}};
{{{defineselect|
    table=definitions&to=word_id,form_tag&from=def_id
}}};

{{{defineselect|
    table=definitions&to=def_id&from=word_id,def_value,def_lang
}}};
{{{defineupdate|
    table=definitions&to=form_tag&from=def_id
}}};

{{{attrs|
    table=definitions&pre=def_&bi=sense,lang,src,type,value
}}};

{{{defineinsert|
    table=definitions&from=word_id,def_value,def_lang}}};
{{{defineinsert|
    table=definitions&from=word_id,def_value,def_lang,form_tag}}};
{{{definedelete|
    table=definitions&from=def_id}}};

/************************
 * PRONUNCIATIONS
 ************************/

{{{defineselect|
    table=pronunciations&to=pron_id&from=word_id
}}};
{{{defineselect|
    table=pronunciations&to=word_id&from=pron_id
}}};
{{{defineselect|
    table=pronunciations&to=pron_id&from=form_tag
}}};
{{{defineselect|
    table=pronunciations&to=form_tag&from=pron_id
}}};
{{{defineselect|
    table=pronunciations&to=pron_id&from=word_id,form_tag
}}};
{{{defineselect|
    table=pronunciations&to=word_id,form_tag&from=pron_id
}}};

{{{defineselect|
    table=pronunciations&to=pron_id&from=word_id,pron_value,pron_sublang
}}};
{{{defineupdate|
    table=pronunciations&to=form_tag&from=pron_id
}}};

{{{attrs|
    table=pronunciations&pre=pron_&bi=sublang,src,type,value
}}};

{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_sublang}}};
{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_type}}};
{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_sublang,pron_type}}};
{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_sublang,form_tag}}};
{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_type,form_tag}}};
{{{defineinsert|
    table=pronunciations&from=word_id,pron_value,pron_sublang,pron_type,form_tag}}};
{{{defineselect|
    table=pronunciations&to=pron_id&from=word_id,pron_value,pron_type}}};
{{{definedelete|
    table=pronunciations&from=pron_id}}};

/************************
 * CONNECTIONS
 ************************/

{{{defineselect|
    table=connections&to=to_word_id,connect_type&from=from_word_id
}}};
{{{defineinsert|
    table=connections&from=from_word_id,to_word_id,connect_type
}}};
{{{definedelete|
    table=connections&from=from_word_id,to_word_id,connect_type
}}};


/************************
 * QUIZZES
 ************************/

{{{defineselect|
    table=quizzes&to=user_id&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=type&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=last&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=score&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=completed&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=out_of&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=time_started&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=time_finished&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=questions&from=quiz_id}}};

{{{defineupdate|
    table=quizzes&to=questions&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=results&from=quiz_id}}};

{{{defineupdate|
    table=quizzes&to=results&from=quiz_id}}};

{{{defineselect|
    table=quizzes&to=options_n&from=quiz_id}}};

{{{defineupdate|
    table=quizzes&to=options_n&from=quiz_id}}};

{{{defineinsert|
    table=quizzes&from=user_id,last}}};

{{{defineinsert|
    table=quizzes&from=user_id,type,last}}};

{{{define|user_id->last quiz_id|
    SELECT quiz_id FROM quizzes WHERE user_id = (?)
    ORDER BY quiz_id DESC LIMIT 1}}};

{{{define|finish quiz|
    UPDATE quizzes SET
    time_finished = CURRENT_TIMESTAMP,
    completed = TRUE
    WHERE quiz_id = (?)}}};

{{{define|add score|
    UPDATE quizzes SET
    score = score + (?),
    out_of = out_of + (?)
    WHERE quiz_id = (?)}}};

{{{define|set score|
    UPDATE quizzes SET
    score = (?),
    out_of = (?)
    WHERE quiz_id = (?)}}};

{{{defineselect|
    table=quizzes&to=quiz_id&from=user_id}}};

{{{define|user_id->quiz_id reversed|
    SELECT quiz_id FROM quizzes
    WHERE user_id = (?)
    ORDER BY quiz_id DESC}}};

{{{definedelete|
    table=quizzes&from=quiz_id}}};


{{{define|all quizzes|SELECT quiz_id FROM quizzes}}};
{{{defineselect|table=users&to=username&from=id}}};
{{{compose|
username<-quiz_id=
    username<-id|
        {{{get|user_id<-quiz_id}}}}}};

/************************
 * OTHER
 ************************/

{{{defineselect|
    table=languages&to=lang_dispname&from=lang_id}}};

/*
 * Find words by word_name and word_lang
 */
{{{defineselect|
    table=words&to=word_id&from=word_name,word_lang&unique=true
}}};
{{{defineselect|
    table=words&to=word_id&from=word_name,word_lang,word_spart&unique=true
}}};
{{{defineselect|
    table=words&to=word_id&from=word_lang,word_spart&unique=true
}}};
{{{defineselect|
    table=words&to=word_id&from=word_spart&unique=true
}}};
{{{defineselect|
    table=words&to=word_id&from=word_lang&unique=true
}}};

/*
 * Find languages that use a certain speechpart
 */
{{{compose|
word_langs<-word_spart=
    word_lang<-word_id|
        {{{get|word_id<-word_spart}}}
}}};

{{{define|all_sparts|
SELECT word_spart FROM words
}}};

