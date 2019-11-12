<?
session_start();
include_once ('jpgraph.php');
include_once ('jpgraph_line.php');

$grafico = new Graph(440,250);
$grafico->SetScale('textlin');
//vaR_dump($graf_data);
foreach ($_SESSION['graf_data']['data'] as $entrada)
{
	$valores[]=$entrada['counter'];
	$labels[]=$entrada['adate'];
}
$valores=array_reverse($valores);
$labels=array_reverse($labels);

//var_dump($valores);

$plotagem[$lin] = new LinePlot($valores);
$plotagem[$lin]->SetColor('red');
$plotagem[$lin]->SetWeight(1);
$plotagem[$lin]->SetCenter();

$plotagem[$lin]->value->SetFont(FF_ARIAL,FS_NORMAL,8);
$grafico->Add($plotagem[$lin]);
$grafico->SetShadow(false);
$grafico->SetFrame(true,'#ffaa11',0);
$grafico->SetMargin(35,35,35,35);
$grafico->SetMarginColor('#ffaa11');
$grafico->xaxis->SetTickLabels($labels);
$grafico->xaxis->SetWeight(1);
$grafico->xaxis->SetColor('white');
$grafico->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);

$grafico->yaxis->SetWeight(1);
$grafico->yaxis->SetColor('white');
$grafico->yaxis->SetFont(FF_ARIAL,FS_NORMAL,7);

$grafico->ygrid->SetFill(true, '#ffaa11@0.30', '#ffaa11@0.20');
$grafico->ygrid->SetColor('#ffaa11@0.50');
//$grafico->SetTickDensity($tick_density);
$grafico->Stroke();

?>