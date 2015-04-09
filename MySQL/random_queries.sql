set @id1 = 116; set @id2 = 127;
select distinct form_tag from forms where exists (select 1 from forms as f where word_id = @id1 and f.form_tag = forms.form_tag) XOR exists (select 1 from forms as f where word_id = @id2 and f.form_tag = 
forms.form_tag);

