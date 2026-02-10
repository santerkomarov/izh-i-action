<?php

/**
 * Экран для вывода в HTML.
 */
function html(String $string): String {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

