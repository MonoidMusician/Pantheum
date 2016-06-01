<header>
	<h1 data-i18n>Help</h1>
</header>
<article>
<a href="quiz.php">
<h2 id="quizzes">Quizzes</h2></a>
<p>There are many quizzes available, grouped by category. For some, you can select how many pages of questions you want to do.
For quizzes with simple input boxes, if you select “Score quizzes by <b>question</b>”, next to each input field will be displayed an indicator to check your answer before you click submit. (Scoring answers by page means to hide these indicators.)
<p>After you click “Start the Quiz” you will be presented with your first page. Input your answers before clicking “Submit” to check them. Your answers will be scored and the results shown to you. Click “Next” to receive the next page, or “Results” to see your results, scored per page, and finish the quiz. Click on a page number to review your answers for that page.
<p>If you <a href="login.php">log in</a>, your quizzes will be saved so that you can review or resume them later.
You can view your quizzes <a href="quizzes.php">here</a>. Otherwise your results will be lost.

<h3>Latin III</h3>
<p>For verb practice, try <a href="quiz.php?id=synopsis-latinIII">Latin III Synopsis</a>, <a href="quiz.php?id=synopsis-latinIII-translations">Synopsis + Translations</a>, or <a href="quiz.php?id=conjugate-tense">Conjugate Verb Tense</a> quizzes to test your knowledge of verb charts!
For synopses, you may choose what person and number to test – 1s, 2s, 3s, 1p, 2p, 3p, or random. This will cover all the moods, tenses, and voices we know!
For the verb tense conjugation, choose what mood and tense to test (e.g. Future Indicative, Imperfect Subjunctive). This will cover active and passive moods for all persons, singular and plural.


<a href="dictionary.php"><h2 id="dictionary">Dictionary</h2></a>
<p>There are many search fields available:
<dl>
	<dt>Name</dt>
	<dd>Search by the lemma form of a word. For verbs, this is the first principal part (typically the first person singular active indicative); nouns, the nominative singular; adjectives, the masculine nominative singular; etc.</dd>
	<dt>Form</dt>
	<dd>Search for a form in a word’s inflection. For example, searching for the form <a href="dictionary.php?form=amātī&lang=la">amātī</a> will display <a href="dictionary.php?name=amō&lang=la">amō</a>.</dd>
	<dt>Language</dt>
	<dd>Show words from the selected language(s). (Default: <sup>[la]</sup>Latin.)</dd>
	<dt>Part of Speech</dt>
	<dd>Limit words to certain part(s) of speech.</dd>
	<dt>Definition</dt>
	<dd>Search for words that contain a definition.</dd>
	<dt>Attribute</dt>
	<dd>Filter words by selected attributes. See next section.</dd>
</dl>

<h3>Attributes</h3>
<p>Attributes provide additional information for a word. Some commonly used attributes include:
<dl>
	<dt>clc-stage=</dt>
	<dd>What stage of the Cambridge Latin Course features the word in its vocabulary list.</dd>
	<dd>Acceptable values: any number <a href="dictionary.php?attr=clc-stage%3D1&lang=la">1</a>–<a href="dictionary.php?attr=clc-stage%3D48&lang=la">48</a>.</dd>
	<dt>transitive=</dt>
	<dd>Whether the verb is <a href="dictionary.php?attr=transitive%3Dtrue&lang=la&spart=verb">transitive</a> (accepts a direct object) or <a href="dictionary.php?attr=transitive%false&lang=la&spart=verb">not</a>. N.B. Verbs listed as <a href="dictionary.php?attr=case%3Ddative&lang=la&spart=verb">+DAT</a> are (almost always) not transitive, especially if they are not inflected in the passive; deponent verbs, however, may still be transitive, although they lack a passive voice.</dd>
	<dd>Acceptable values: true, false.</dd>
</dl>


<a href="user.php"><h2 id="account">User Accounts</h2></a>
<p>In your <a href="user.php">User Control Panel</a>, under <a href="user.php#preferences">Preferences</a>, you may select the order you want your cases to be displayed in. Drag and drop to reorder them. The default order is voc., nom., acc., abl., dat., gen., loc., but another common way is nom., gen., dat., acc., abl., voc., loc.
</article>
