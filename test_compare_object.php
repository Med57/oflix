<?php
class Testcompare {
    public $test = "toto";
}

$one = new Testcompare();
$two = new Testcompare();


var_dump($one === $two); // comparaison d'instance => false
var_dump($one == $two); // comparaison moins stricte => true
$two->test = "truc";
var_dump($one == $two); // comparaison moins stricte => false
