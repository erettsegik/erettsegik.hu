<?php

function prepareLatexElements($string) {
    $string = str_replace("[latex inline]", "<span class='latex-container' data-displaymode='inline'>", $string);
    $string = str_replace("[latex]", "<span class='latex-container' data-displaymode='block'>", $string);
    $string = str_replace("[/latex]", "</span>", $string);
    return $string;
}
