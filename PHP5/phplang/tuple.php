<?php
    require_once('/var/www/latin/config.php');
    sro('/PHP5/phplang/word_types.php');
    class tuple implements IteratorAggregate
    {
        private $tuple;
        private $attribs;
        function __construct($attribs, $tuple=[]) {
            $this->attribs = $attribs;
            $this->tuple = $tuple;
        }
        // Required definition of interface IteratorAggregate
        public function getIterator() {
            return $this->yarra;
        }
        public function fill_array($attribs, $simple) {
            global $types;
            if (is_string($attribs))
                $attribs = $types[$attribs]->attribs;
            $ret = array_fill_keys(array_keys($attribs), NULL);
            foreach ($simple as $value) {
                if ($value === null) continue;
                $idx = false;
                foreach ($attribs as $key => $values) {
                    $idx = array_search($value, $values);
                    if ($idx !== false) {
                        $ret[$key] = $value;
                        break;
                    }
                }
                if ($idx === false) throw new Exception("Bad value");
            }
            return $ret;
        }
    }
?>

