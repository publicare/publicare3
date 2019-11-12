<?php
$fonte = isset($_GET["nm"])?$_GET["nm"]:"";
$vnome = preg_split("[\.]", $fonte);

if ($fonte != "")
{
    $fon = new Includes(array($fonte), "font", $vnome[count($vnome)-1]);
    $fon->imprimeResultado();
}

exit();