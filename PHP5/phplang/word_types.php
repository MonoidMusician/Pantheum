<?php
    require_once('/var/www/latin/config.php');
    sro('/PHP5/phplang/tuple.php');
    $numbers = ["singular", "plural"];
    $points_of_view = ["first", "second", "third"];
    $genders = ["feminine", "masculine", "neuter"];
    $degrees = ["positive", "comparative", "superlative"];
    $cases = ["nominative", "accusative", "dative", "ablative",
              "vocative", "genitive", "locative"];
    $tenses = ["present", "imperfect", "perfect", "future",
               "pluperfect", "future-perfect"];
    $declensions = [];
    for ($dn = 1; $dn <= 5; $dn += 1)
        $declensions []= "decl-".$dn;
    class WordType
    {
        public $attribs;
        public $base_idx;
        public $on_short;
        function __construct($attribs, $base_idx=[], $on_short=NULL) {
            $this->attribs = $attribs;
            $this->base_idx = tuple::fill_array($attribs, $base_idx);
        }
    }
    global $types;
    $types = [
        "Noun" => new WordType([
            "case" => $cases,
            "number" => $numbers,
            "gender" => $genders,
            "declension" => $declensions,
            "point-of-view" => $points_of_view,
        ], ["nominative", "singular"]),
    ];
?>

