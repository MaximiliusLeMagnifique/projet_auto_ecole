<?php

function validateDate($date, $format = 'd/m/Y H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}