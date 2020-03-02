<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright GPL © 2007
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

class Parse
{

    public $InitCmd;
    public $debug;
    public $operands;
    public $expoperands;
    public $separators;
    public $macros;
    public $types;
    public $cmdArray;
	
    function __construct()
    {
        if (!defined ("_PARSEINITIALIZED"))
        {
            define ("_PARSEINITIALIZED", 1);
            $this->Initialize();
        }

    }

    function Initialize()
    {
		
        $this->InitCmd = '<?php'."\n"
                            .'global $_page;'."\n"
                            .'$_PAGINATIONSTACK_=array();'."\n"
                            .'$_SEQ_PAGINATION_=array();'."\n"
                            .'$_STACK_=array();'."\n"
                            .'$_OBJ_=&$_page->_objeto;'."\n"
                            .'$_LOOPSTACK_ = array();'."\n"
                            .'$_IMAGESTACK_ = array();'."\n"
                            .'$_IMAGESOURCESTACK_ = array();'."\n"
                            .'list($usec, $sec) = explode(" ", microtime());'."\n"
                            .'$_seed=(float) $sec + ((float) $usec * 100000);'."\n"
                            .'mt_srand($_seed);'."\n"
                            .'//x($_OBJ_->Valor($_page, "cod_status"));'."\n"
                            .'$_GETARRAY=$_GET;'."\n"
                            .'?>';
		
        $this->debug = 0;
        
        $this->operands = array ('=', '>', '<', '<=', '>=', '!=');

        $this->expoperands = array (
            "/" => array (
                            'params'=>'v|m|d|n',
                            'output'=>'n',
                    ),
            "*" => array (
                            'params'=>'v|m|d|n',
                            'output'=>'n',
                    ),
            "+" => array (
                            'params'=>'v|m|d|n',
                            'output'=>'n',
                    ),
            "-" => array (
                            'params'=>'v|m|d|n',
                            'output'=>'n',
                    ),
            "%" => array (
                            'params'=>'v|m|d|n',
                            'output'=>'n',
                    ),
            "." => array (
                            'params'=>'v|m|d|s',
                            'output'=>'s',
                    ),

        );
        
        $this->separators = implode ("",array_keys($this->expoperands));
        
        $this->macros = array(
            'DIA' => 'date("d")',
            'MES' => 'date("m")',
            'ANO' => 'date("Y")',
            'DATA'=> 'date("d/m/Y")',
            'ROOT'=> '$_page->config["portal"]["objroot"]',
            'INDICE' => '$_LOOP_["count"]',
            'FIM' => '$_LOOP_["max"]',
            'COD_OBJETO' => '$_OBJ_->Valor("cod_objeto")',
            'COD_PAI' => '$_OBJ_->Valor("cod_pai")',
            'TAGS' => '$_OBJ_->Valor("tags")',
            'QUANTIDADE' => '$_LOOP_["max"]',
        );

        $this->types = array(
            's' => 'string',
            'v' => 'variavel',
            'd' => 'dado',
            'n' => 'numerico',
            'm' => 'macro',
            'o' => 'operador',
        );

        $this->cmdArray = array(
            
            "eco" => array(
                'regex' => '|(.*)|is',
                'output' => '<?php echo (<#1#>); ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>eco</strong> deve ser escrito assim: <strong>&lt;@eco {variavel|string|dado|numero|macro} @&gt;</strong>',
                'itens' => array (
                    1 => 's|v|n|m|d'
                ),
            ),
									
            "ecoe" => array(
                'regex' => '|(.*)|is',
                'output' => '<?php echo htmlentities(<#1#>); ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>ecoe</strong> deve ser escrito assim: <strong>&lt;@ecoe {variavel|string|dado|numero|macro} @&gt;</strong>',
                'itens' => array (
                    1 => 's|v|n|m|d',
                ),
            ),

            "formatadata" => array(
                'regex' => '|(.*)|is',
                'output' => '<?php echo format_data_str(<#P:data#>,<#P:formato#>); ?>',
                'parameters' => 1,
                'helptext' => 'O comando <strong>formatadata</strong> deve ser escrito assim: <strong>&lt;@formatadata valor=[{variavel|string|dado|macro}] formato=[{variavel|string|dado|macro}] @&gt;</strong>',
                'itens' => array (
                    1 => 's|v|n|m|d',
                ),
                'paramitens' => array (
                    'data' => 's|v|m|d',
                    'formato' => 's|v|m|d',
                )
            ),
            
            "eco_limite" => array(
                'regex' => '|(.*)|is',
                'output' => '<?php if (<#P:caracter#>!=""){'."\n"
                        .'$pos=strpos(<#P:texto#>,<#P:caracter#>,<#P:minimo#>)+1;'."\n"
                        .'if ($pos < <#P:minimo#>) $pos = <#P:minimo#>;'."\n"
                        .'echo substr(<#P:texto#>,0,$pos);'."\n"
                    .'} else {'."\n"
                        .'if ((<#P:limite#>!=-1)||(<#P:inicio#>!=0)) {'."\n"
                            .'if (<#P:limite#>!=-1) echo substr(<#P:texto#>,<#P:inicio#>,<#P:limite#>);'."\n"
                            .'else echo substr(<#P:texto#>,<#P:inicio#>);'."\n"
                        .'}'."\n"
                        .'else echo <#P:texto#>;'."\n"
                    .'} ?>',
                'parameters' => 1,
                'helptext' => 'O comando <strong>eco_limite</strong> deve ser escrito assim: <strong>&lt;@eco_limite texto={variavel|string|dado|numero|macro} limite={variavel|numero} inicio={variavel|numero} caracter={variavel|string}@&gt;</strong>',
                'paramitens' => array (
                    'texto' => 's|v|n|m|d',
                    'inicio' => 'v|n',
                    'limite' => 'v|n',
                    'caracter' => 'v|s',
                    'minimo' => 'v|n',
                ),
                'paramforce' => false,
                'paramdefault' => array (
                    'inicio' => 0,
                    'limite' => -1,
                    'minimo' => 0,
                ),
            ),
            
            "=" => array(
                'regex' => '|(.*)|is',
                'output' => '<?php echo (<#1#>); ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>eco</strong> deve ser escrito assim: <strong>&lt;@eco {variavel|string|dado|numero|macro} @&gt;</strong>',
                'itens' => array (
                    1 => 's|v|n|m|d',
                ),
            ),
            
            "var" => array(
                'regex' => '|(\$.*?)\s*=\s*(.*)|is',
                'output' => '<?php <#1#> = <#2#>; ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>var</strong> deve ser escrito assim: <strong>&lt;@var variavel={variavel|string|dado|numero|macro} @&gt;</strong>',
                'itens' => array (
                    1 => 'v',
                    2 => 's|v|d|n|m',
                ),
            ),

            "se" => array (
                'opentag' => 'se',
                'regex' => '$\[(.*?)(==|>=|<=|!=|>|<)(.*)\]$is',
                'output' => '<?php if (<#1#><#2#><#3#>) { ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>se </strong> deve ser escrito assim: <strong>&lt;@se [{variavel|string|dado|numero}{>|<|<=|>=|==|!=}{variavel|string|dado|numero|macro}] @&gt;</strong>.',
                'itens' => array (
                    1 => 's|v|d|n|m',
                    2 => 'o',
                    3 => 's|v|d|n|m',
                ),
            ),
            
            "/se" => array (
                'closetag' => 'se',
                'regex' => '',
                'output' => '<?php } ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>/se </strong> deve ser escrito assim: <strong>&lt;@\se@&gt;</strong>',
                'itens' => false,
            ),

            "senao" => array (
                'closetag' => 'se',
                'opentag' => 'se',
                'regex' => '',
                'output' => '<?php } else { ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>senao</strong> deve ser escrito assim: <strong>&lt;@senao@&gt;</strong>',
                'itens' => false,
            ),

            "repetir" => array (
                'opentag' => 'repetir',
                'regex' => '|(.*)\=\[(.*),(.*)\]|is',
                'output' => '<?php for (<#1#>=<#2#>;<#1#><=<#3#>;<#1#>++) {?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>repetir</strong> deve ser escrito assim: <strong>&lt;@repetir {variavel}={numero inicial},{numero final}@&gt;</strong>',
                'itens' => array (
                    1 => 'v',
                    2 => 'n',
                    3 => 'n',
                )
            ),

            "/repetir" => array (
                'closetag' => 'repetir',
                'regex' => '',
                'output' => '<?php } ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>\repetir </strong> deve ser escrito assim: <strong>&lt;@\repetir@&gt;</strong>',
                'itens' => false,
            ),

            "filhos" => array (
                'opentag' => 'filhos',
                'regex' => '|(.*)|',
                'output' => '<?php if (<#P:nome#>_max = $_OBJ_->PegaListaDeFilhos(<#P:classes#>, <#P:ordem#>, <#P:inicio#>, <#P:limite#>)) {'."\n"
                    .'if (!isset($_LOOP_)) $_LOOP_=array();'."\n"
                    .'array_push($_LOOPSTACK_,$_LOOP_);'."\n"
                    .'$_LOOP_=array();'."\n"
                    .'$_LOOP_["array"]=array();'."\n"
                    .'$_LOOP_["array"]=array();'."\n"
                    .'$_LOOP_["count"]=0;'."\n"
                    .'$_LOOP_["max"]=<#P:nome#>_max;'."\n"
                    .'$_LOOP_["obj"]=$_OBJ_;'."\n"
                    .'array_push($_STACK_,$_OBJ_);'."\n"
                    .'while (<#P:nome#> = $_LOOP_["obj"]->PegaProximoFilho()) {'."\n"
                        .'$_OBJ_ = <#P:nome#>;'."\n"
                        .'$_LOOP_["count"]++;'."\n"
                        .'$_LOOP_["array"][] = $_OBJ_;'."\n"
                    .'?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>filhos</strong> deve ser escrito assim: <strong>&lt;@filhos nome=[{variavel}] classes=[{string}] ordem=[{string}]@&gt;</strong>',
                'paramitens' => array (
                    'nome' => 'v',
                    'classes' => 's',
                    'ordem' => 's',
                    'limite' => 'n',
                    'inicio' => 'n',
                ),
                'paramforce' => false,
                'paramdefault' => array (
                    'nome' => '$var_'.uniqid(""),
                ),
            ),

            "/filhos" => array (
                'closetag' => 'filhos',
                'regex' => '',
                'output' => '<?php  } $_OBJ_=array_pop($_STACK_);'."\n"
                    .'$_LOOP_=array_pop($_LOOPSTACK_);'."\n"
                    .'$_SEMFILHOS=false;'."\n"
                    .'} else {$_SEMFILHOS=true;}?>'."\n",
                'parameters' => false,
                'helptext' => 'O comando <strong>\filhos </strong> deve ser escrito assim: <strong>&lt;@\filhos@&gt;</strong>',
            ),
            
            "semfilhos" => array (
                'regex' => '',
                'output' => '<?php if ($_SEMFILHOS) { ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>semfilhos</strong> deve ser escrito assim: <strong>&lt;@semfilhos@&gt;</strong>',
                'itens' => false,
            ),
            
            "/semfilhos" => array (
                'regex' => '',
                'output' => '<?php } ?>',
                'parameters'=> false,
                'helptext' => 'O comando <strong>/semfilhos</strong> deve ser escrito assim: <strong>&lt;@/semfilhos@&gt;</strong>',
                'itens' => false,
            ),
            
            "aleatorio" => array (
                'opentag' => 'aleatorio',
                'regex' => '|(.*)|',
                'output' => '<?php if (<#P:nome#>_max = $_OBJ_->PegaListaDeFilhos(<#P:classes#>, <#P:ordem#>)) {'."\n"
                        .'if (!isset($_LOOP_)) $_LOOP_=array();'."\n"
                        .'array_push($_LOOPSTACK_,$_LOOP_);'."\n"
                        .'$_LOOP_=array();'."\n"
                        .'$_LOOP_["array"]=array();'."\n"
                        .'$_LOOP_["count"]=0;'."\n"
                        .'$_LOOP_["obj"]=$_OBJ_;'."\n"
                        .'array_push($_STACK_,$_OBJ_);'."\n"
                        .'$array_result=array();'."\n"
                        .'if (<#P:quantidade#> >= <#P:nome#>_max) {'."\n"
                            .'while ($obj=&$_OBJ_->PegaProximoFilho()) $_LOOP_["array"][]=&$obj;'."\n"
                        .'} else {'."\n"
                            .'for ($f=0;$f<<#P:quantidade#>;$f++) {'."\n"
                                .'do {'."\n"
                                    .'$index = mt_rand(0,<#P:nome#>_max-1);'."\n"
                                .'} while (in_array($index,$array_result));'."\n"
                                .'$array_result[]=$index;'."\n"
                                .'$_LOOP_["array"][$index] = $_OBJ_->VaiParaFilho($index);'."\n"
                            .'}'."\n"
                        .'}'."\n"
                        .'$_LOOP_["max"] = count($_LOOP_["array"]);'."\n"
                        .'foreach ($_LOOP_["array"] as <#P:nome#>) {'."\n"
                            .'$_OBJ_ = <#P:nome#>;'."\n"
                            .'$_LOOP_["count"]++;'."\n"
                    .'?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>filhos</strong> deve ser escrito assim: <strong>&lt;@aleatorio nome=[{variavel}] classes=[{string}] ordem=[{string}]@&gt;</strong>',
                'paramitens' => array (
                    'nome' => 'v',
                    'classes' => 's',
                    'ordem' => 's',
                    'quantidade' => 'n',
                ),
                'paramforce' => false,
                'paramdefault' => array (
                    'nome' => '$var_'.uniqid(""),
                    'quantidade' =>'1',
                ),
            ),
            
            "/aleatorio" => array (
                'closetag' => 'aleatorio',
                'regex' => '',
                'output' => '<?php }'."\n"
                        .'$_LOOP_=array_pop($_LOOPSTACK_);'."\n"
                        .'$_OBJ_=array_pop($_STACK_);'."\n"
                    .'} '."\n"
                    .'?>'."\n",
                'parameters' => false,
                'helptext' => 'O comando <strong>/aleatorio </strong> deve ser escrito assim: <strong>&lt;@\filhos@&gt;</strong>',
            ),
            
            "localizar" => array (
                'opentag' => 'localizar',
                'regex' => '|(.*)|',
                'output' => '<?php <#P:nome#>_array = $_page->_adminobjeto->LocalizarObjetos(<#P:classes#>,<#P:condicao#>,<#P:ordem#>,<#P:inicio#>,<#P:limite#>,<#P:pai#>,<#P:niveis#>,false,<#P:like#>,<#P:ilike#>,<#P:tags#>);'."\n"
                    .'if (<#P:nome#>_max = count(<#P:nome#>_array)) {'."\n"
                        .'if (!isset($_LOOP_)) $_LOOP_=array();'."\n"
                        .'array_push($_LOOPSTACK_,$_LOOP_);'."\n"
                        .'$_LOOP_=array();'."\n"
                        .'$_LOOP_["array"]=array();'."\n"
                        .'$_LOOP_["count"]=0;'."\n"
                        .'$_LOOP_["max"]=<#P:nome#>_max;'."\n"
                        .'$_LOOP_["obj"]=$_OBJ_;'."\n"
                        .'array_push($_STACK_,$_OBJ_);'."\n"
                        .'foreach (<#P:nome#>_array as <#P:nome#>) {'."\n"
                            .'$_OBJ_ = <#P:nome#>;'."\n"
                            .'$_LOOP_["count"]++;'."\n"
                            .'$_LOOP_["array"][] = $_OBJ_;'."\n"
                    .'?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>Localizar</strong> deve ser escrito assim: <strong>&lt;@localizar nome=[{variavel}] classes=[{string}] ordem=[{string}] niveis=[{numero}] like=[{string},{string}] ilike=[{string_minuscula},{string_minuscula}] tags=[{string},{string}] @&gt;</strong>',
                'paramitens' => array (
                    'nome' => 'v',
                    'condicao' => 's',
                    'classes' => 's',
                    'ordem' => 's',
                    'limite' => 'n',
                    'inicio' => 'n',
                    'pai' => 'n',
                    'niveis' => 'n',
                    'like' => 's',
                    'ilike' => 's',
                    'tags' => 's',
                ),
                'paramforce' => false,
                'paramdefault' => array (
                    'nome' => '$var_'.uniqid(""),
                    'pai' => '-1',
                ),
            ),
            
            "/localizar" => array (
                'closetag' => 'localizar',
                'regex' => '',
                'output' => '<?php } $_OBJ_=array_pop($_STACK_);'."\n"
                        .'$_LOOP_=array_pop($_LOOPSTACK_);'."\n"
                        .'$_SEMRESULTADOS=false;'."\n"
                    .'} else {$_SEMRESULTADOS=true;}?>'."\n",
                'parameters' => false,
                'helptext' => 'O comando <strong>\filhos </strong> deve ser escrito assim: <strong>&lt;@\filhos@&gt;</strong>',
            ),
            
            "naolocalizado" => array (
                'regex' => '',
                'output' => '<?php if ($_SEMRESULTADOS) { ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>naolocalizado</strong> deve ser escrito assim: <strong>&lt;@naolocalizado@&gt;</strong>',
                'itens' => false,
            ),
            
            "/naolocalizado" => array (
                'regex' => '',
                'output' => '<?php } ?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>/naolocalizado</strong> deve ser escrito assim: <strong>&lt;@/naolocalizado@&gt;</strong>',
                'itens' => false,
            ),
            
            "localizaraleatorio" => array (
                'opentag' => 'localizaraleatorio',
                'regex' => '|(.*)|',
                'output' => '<?php <#P:nome#>_array = $_page->_adminobjeto->LocalizarObjetos(<#P:classes#>,<#P:condicao#>,<#P:ordem#>,-1,-1,<#P:pai#>,<#P:niveis#>);'."\n"
                    .'if (<#P:nome#>_max = count(<#P:nome#>_array)) {'."\n"
                        .'if (!isset($_LOOP_)) $_LOOP_=array();'."\n"
                        .'array_push($_STACK_,$_OBJ_);'."\n"
                        .'array_push ($_LOOPSTACK_,$_LOOP_);'."\n"
                        .'$_LOOP_=array();'."\n"
                        .'$_LOOP_["array"]=array();'."\n"
                        .'$_LOOP_["count"]=0;'."\n"
                        .'$_LOOP_["obj"]=$_OBJ_;'."\n"
                        .'if (<#P:quantidade#> >= <#P:nome#>_max) { '."\n"
                            .'$_LOOP_["array"]=<#P:nome#>_array;'."\n"
                        .'} else {'."\n"
                            .'$array_result=array();'."\n"
                            .'for ($f=0;$f<<#P:quantidade#>;$f++) {'."\n"
                                .'do {'."\n"
                                    .'$index=mt_rand(0,<#P:nome#>_max-1);'."\n"
                                .'} while (in_array($index,$array_result));'."\n"
                                .'$array_result[]=$index;'."\n"
                                .'$_LOOP_["array"][$index]=<#P:nome#>_array[$index];'."\n"
                            .'}'."\n"
                        .'}'."\n"
                        .'$_LOOP_["max"]=count($_LOOP_["array"]);'."\n"
                        .'foreach ($_LOOP_["array"] as $_OBJ_) {'."\n"
                            .'$_LOOP_["count"]++;'."\n"
                    .'?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>Localizaraleatorio</strong> deve ser escrito assim: <strong>&lt;@localizaaleatorio nome=[{variavel}] classes=[{string}] ordem=[{string}]@&gt;</strong>',
                'paramitens' => array (
                    'nome' => 'v',
                    'condicao' => 's',
                    'classes' => 's',
                    'ordem' => 's',
                    'quantidade' => 'n',
                    'pai' => 'n',
                    'niveis' => 'n',
                ),
                'paramforce' => false,
                'paramdefault' => array (
                    'nome' => '$var_'.uniqid(""),
                    'pai' => '-1',
                    'quantidade' => '1',
                ),
            ),
            
            "/localizaraleatorio" => array (
                'closetag' => 'localizaraleatorio',
                'regex' => '',
                'output' => '<?php } '."\n"
                            .'$_LOOP_=array_pop($_LOOPSTACK_);'."\n"
                            .'$_OBJ_=array_pop($_STACK_);'."\n"
                        .'}'."\n"
                    .'?>'."\n",
                'parameters' => false,
                'helptext' => 'O comando <strong>\filhos </strong> deve ser escrito assim: <strong>&lt;@\filhos@&gt;</strong>',
            ),
            
            "usarobjeto" => array (
                'opentag' => 'objeto',
                'regex' => '|(.*)|',
                'output' => '<?php if (<#P:titulo#>!="") $_tmp_=$_page->_adminobjeto->CriarObjeto(<#P:titulo#>);'."\n"
                    .'else { if (<#P:cod_objeto#>!=-1) $_tmp_=$_page->_adminobjeto->CriarObjeto(<#P:cod_objeto#>); }'."\n"
                    .'if ($_tmp_) {'."\n"
                        .'array_push($_STACK_,$_OBJ_);'."\n"
                        .'$_OBJ_=$_tmp_;'."\n"
                    .'?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>usarobjeto</strong> deve ser escrito assim: <strong>&lt;@usarobjeto titulo=[{string}]@&gt; ou &lt;@usarobjeto cod_objeto=[{numero}]@&gt; </strong>',
                'paramitens' => array (
                    'titulo' => 's',
                    'cod_objeto' => 'n'
                ),
            ),
            
            "/usarobjeto" => array (
                'closetag' => 'objeto',
                'regex' => '',
                'output' => '<?php $_OBJ_=array_pop($_STACK_);'."\n"
                    .' } ?>'."\n",
                'parameters'=> false,
                'helptext' => 'O comando <strong>\usarobjeto</strong> deve ser escrito assim: <strong>&lt;@\usarobjeto@&gt;</strong>',
            ),
            
            "incluimenu" => array(
                'regex' => '',
                'output' => '<?php if ($_page->_usuario->EstaLogado(_PERFIL_RESTRITO)) include ("includes/menu_publicare.php")?>',
                'parameters' => false,
                'helptext' => 'O comando <strong>incluimenu</strong> deve ser escrito assim: <strong>&lt;@incluimenu@&gt;</strong>',
            ),
            
            "menu" => array (
                'opentag' => 'menu',
                'regex' => '',
                'output' => '<?php if ($_SESSION["usuario"]["cod_usuario"]) {'."\n"
                        .'$_MENU = $_page->_usuario->Menu();'."\n"
                        .'foreach ($_MENU as $_OPCAO) {?>'."\n",
                'parameters' => false,
                'paramitens' => false,
                'itens' => false,
            ),
            
            "/menu" => array (
                'closetag' => 'menu',
                'regex' => '',
                'output' => '<?php }} ?>'."\n",
                'parameters' => 0,
                'paramitens' => false
            ),
            
            "acao" => array (
                'regex' => '',
                'output' => '<?php echo $_OPCAO["acao"];?>'."\n",
                'parameters' => 0,
                'paramitens' => false
            ),
            
            "script" => array (
                'regex' => '',
                'output' => '<?php echo "/index.php".$_OPCAO["script"]."/".$GLOBALS["cod_objeto"].".html";?>'."\n",
                'parameters' => 0,
                'paramitens' => false
            ),
            
            "incluir" => array (
                'regex' => '|(.*)|',
                'output' => '<?php $_page->_parser->Start($_SERVER["DOCUMENT_ROOT"].<#P:arquivo#>);?>'."\n",
                'parameters' => 1,
                'helptext' => 'O comando <strong>incluir</strong> deve ser escrito assim: <strong>&lt;@incluir arquivo=[{string}]@&gt;</strong>',
                'paramitens' => array (
                    'arquivo'  => 's',
                ),
            ),
            
            "protegido" => array (
                'regex' => '|(.*)|',
                //'output' => '<?php (<#P:pele#>) ? $tmpDir = "/html/skin/".<#P:pele#> : $tmpDir = "/html/template"; '."\n"
                //.'$extensao=(file_exists($_SERVER["DOCUMENT_ROOT"].$tmpDir."/view_".<#P:view#>.".php"))?"php":"pbl";'."\n"
                //.'($_page->_usuario->EstaLogadoMilitarizado()===true) ? $_page->_parser->Start($_SERVER["DOCUMENT_ROOT"].$tmpDir."/view_".<#P:view#>.".".$extensao) : $_page->_parser->Start($_SERVER["DOCUMENT_ROOT"]."/html/template/view_protegido.pbl");',
                'output' => '<?php (<#P:pele#>) ? $tmpDir = "/html/skin/".<#P:pele#> : $tmpDir = "/html/template"; '."\n"
                    .'$extensao=(file_exists($_SERVER["DOCUMENT_ROOT"].$tmpDir."/view_".<#P:view#>.".php"))?"php":"pbl";'."\n"
                    .'($_page->_usuario->EstaLogadoMilitarizado()===true) ? $_page->_parser->Start($_SERVER["DOCUMENT_ROOT"].$tmpDir."/view_".<#P:view#>.".".$extensao) : $_page->_parser->Start($_SERVER["DOCUMENT_ROOT"]."/html/template/view_protegido.php"); ?>',
                'parameters'=> 1,
                'helptext' => 'O comando <strong>protegido</strong> deve ser escrito assim: <strong>&lt;@protegido view=[{nome_de_arquivo}] pele=[{prefixo_pele}]@&gt;</strong>',
                'paramitens' => array (
                    'view' => 's',
                    'pele' => 's',
                ),
            ),
            
            "usarblob" => array (
                'opentag'=>'blob',
                'regex' => '|(.*)|',
                'output' => '<?php '."\n"
                    .'global $_BLOBTAMANHO, $_BLOBLINK, $_BLOBDOWNLOAD, $_BLOBVIEW, $_BLOBTIPO, $_THUMBVIEW, $_BLOBICONE;'."\n"
                    .'if ($_OBJ_->TamanhoBlob(<#P:nome#>)) {'."\n"
                    .'$_BLOBTAMANHO = $_OBJ_->TamanhoBlob(<#P:nome#>);'."\n"
                    .'$_BLOBLINK = $_OBJ_->LinkBlob(<#P:nome#>);'."\n"
                    .'$_BLOBDOWNLOAD = $_OBJ_->DownloadBlob(<#P:nome#>);'."\n"
                    .'$_BLOBVIEW = $_OBJ_->ExibirBlob(<#P:nome#>, <#P:largura#>, <#P:altura#>);'."\n"
                    .'$_BLOBTIPO = $_OBJ_->TipoBlob(<#P:nome#>);'."\n"
                    .'$_THUMBVIEW = $_OBJ_->ExibirThumb(<#P:nome#>);'."\n"
                    .'$_BLOBICONE = $_OBJ_->IconeBlob(<#P:nome#>);'."\n"
                    .'?>',
                'parameters'=> 1,
                'helptext' => 'O comando <strong>usarblob</strong> deve ser escrito assim: <strong>&lt;@usarblob nome=[{string}] largura=[{integer}] altura=[{integer}] @&gt;</strong>',
                'paramitens' => array (
                    'nome'  => 's',
                    'largura' => 'i',
                    'altura' => 'i',
                ),
                'paramforce'=>false,
                'paramdefault' => array (
                    'largura' => '0',
                    'altura'=>'0',
                ),
            ),

            "/usarblob" => array (
                'closetag'=>'blob',
                'regex' => '',
                'output' => '<?php } ?>'."\n",
                'parameters'=> false,
                'helptext' => 'O comando <strong>\usarblob</strong> deve ser escrito assim: <strong>&lt;@\usarblob@&gt;</strong>',
            ),

            "linkblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBLINK; ?>'."\n",
                'parameters'=> false,
            ),
									
            "downloadblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBDOWNLOAD; ?>'."\n",
                'parameters'=> false,
            ),
            
            "tamanhoblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBTAMANHO; ?>'."\n",
                'parameters'=> false,
            ),
									
            "iconeblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBICONE; ?>'."\n",
                'parameters'=> false,
            ),
            
            "tipoblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBTIPO; ?>'."\n",
                'parameters'=> false,
            ),

            "srcblob" => array (
                'regex' => '',
                'output' => '<?php echo $_BLOBVIEW; ?>'."\n",
                'parameters'=> false,
            ),

            "iconeclasse" => array (
                'regex' => '',
                'output' => '<?php  '."\n"
                    .'if ($_OBJ_->Valor("prefixoclasse")=="arquivo") '."\n"
                    .'{'."\n"
                    .'  echo "<img src=\"".$_OBJ_->IconeBlob("conteudo")."\" border=\"0\" align=\"absmiddle\" title=\"".$_OBJ_->Valor("classe")."\" />";'."\n"
                    .'} else {'."\n"
                    .'  echo "<img src=\"/blob/iconeclasse?nome=".$_OBJ_->Valor("prefixoclasse")."\" border=\"0\" align=\"absmiddle\" title=\"".$_OBJ_->Valor("classe")."\" />";'."\n"
                    .'} ?>'."\n",
                'parameters'=> false,
            ),
            
            "srcthumb" => array (
                'regex' => '',
                'output' => '<?php echo $_THUMBVIEW; ?>'."\n",
                'parameters'=> false,
            ),
            
            "executar" => array (
                'regex' => '|(.*)|',
                'parameters'=>1,
                'paramitens' => array (
                    'objeto' => 's|v',
                    'parametros' => 's|v',
                ),
                'paramforce' => true,
                'output' => '<?php include_once ($_SERVER["DOCUMENT_ROOT"]."/html/objects/<#P:objeto#>.php");'."\n"
                    .'eval ("object_<#P:objeto#>(\'".<#P:parametros#>."\');");'."\n?>"
            ),
            
            "temfilho" => array (
                'opentag'=>'temfilho',
                'regex' => '|(.*)|',
                'output' => '<?php if (<#P:cod_objeto#>!="") $_tmp_=$_page->_adminobjeto->PegaNumFilhos(<#P:cod_objeto#>);'."\n"
                    .'if ($_tmp_ > 0) {'."\n"
                    .'?>'."\n",
                'parameters'=> 1,
                'helptext' => 'O comando <strong>usarobjeto</strong> deve ser escrito assim: <strong>&lt;@usarobjeto cod_objeto=[{numero}]@&gt; </strong>',
                'paramitens' => array (
                    'cod_objeto'=>'n'
                ),
            ),
            
            "/temfilho" => array (
                'closetag'=>'temfilho',
                'regex' => '',
                'output' => '<?php'."\n"
                    .' } ?>'."\n",
                'parameters'=> false,
                'helptext' => 'O comando <strong>\temfilho</strong> deve ser escrito assim: <strong>&lt;@\temfilho@&gt;</strong>',
            ),
            
        );
    }

	function Start($file,$type=0)
	{
		$this->tags=array();
		$buffer = "";
//                
//                ob_flush();
//                flush();
		
		if ($this->debug)
		{
			echo "start arquivo: ".$file." - ".date("d/m/Y H:i:s")."<br>";
//                        
		}

		if ($type==0)
		{
                    $buffer = file_get_contents($file);
                    if (!$buffer) return false;
		}
		else $buffer=$file;

		$out='';
		$this->ErrorMessage='';
		$this->showcode=0;
		if (preg_match('|(.*?)<@debug_on@>(.*)|is',$buffer,$item))
		{
			$this->showcode=1;
			$buffer=$item[1].$item[2];
		}

		$bufferlines=explode("\n",$buffer);
		foreach ($bufferlines as $key=>$line)
		{
			$this->line=$line;
			$this->key=$key;
			while (preg_match('$(.*?)\<@\s*(.*?)(?:\s+(.*?)|)@\>(.*)$is',$line,$cmd))
			{
//				print_r($cmd);
//				echo '<br>';
				if ($this->debug)
				{
					echo "<pre><BR>line: $line<BR>";
					echo "<BR>com: $cmd[2]<BR>";
					var_dump($cmd);

					echo '<BR><HR><P></pre>';
				}
				$line = trim($cmd[1]).$this->ParseCommand($cmd[2],$cmd[3]).trim($cmd[4]);
			}
			$out .="\n".$line;
		}
		$this->CheckTags();
		if ($this->showcode)
		{
			echo '<P>';
			$str = $this->InitCmd.$out;
			$dbg=explode("\n",$str);
			foreach ($dbg as $key=>$line)
			{
				echo ($key+1).':&nbsp;';
				echo htmlspecialchars($line);
				echo '<br>';
			}
		}
	/*	echo "?>".$this->InitCmd.$out;
exit;*/
		/*eval ("?>");*/
//                ob_flush();
//                flush();
		eval ("?>".$this->InitCmd.$out);
//                ob_flush();
//                flush();
		//return $this->InitCmd.$out;
	}

	function CheckTags()
	{
		if ($this->debug)
		{
			echo '<hr><bR>Tags<BR>';
			var_dump ($this->tags);
			echo '<br><hr>';
		}
		foreach ($this->tags as $tagname=>$tag)
		{
			if (count($tag))
			{
				$this->Error("Comando <strong>".$tagname."</strong> n&atilde;o foi fechado.",0);
				echo $this->cmdArray[$key]['closeerror'];
				exit;
			}
		}
	}

	function Error($msg,$showhelp=true)
	{
		echo "<B>Publicare Script Debuger</b><br>";

		echo "<em>Erro na linha ".$this->key.":</em><br><font color=blue>".htmlspecialchars($this->line).'</font><br>'.$msg.'<br>';
		if ($showhelp)
			echo $this->command['helptext'];
		exit;
	}

	function ParseCommand($cmd,$buffer)
	{
		if ($this->debug)
		{
			echo "Comando: $cmd<br>Buffer: $buffer<br>";
		}
		$buffer=trim($buffer);
		$this->command=$this->cmdArray[$cmd];
		if (is_array($this->command))
		{
			//Closes TAGS
			if (isset($this->command['closetag']))
			{
				//echo "AQui";
				//var_dump($this->tags[$this->command['closetag']]);
				//echo "close:".$this->command['closetag']."<BR>";
				if ((!is_array($this->tags[$this->command['closetag']])) || (!array_pop ($this->tags[$this->command['closetag']])))
				{
					$this->Error ("O comando <font color=blue><strong>".$cmd."</strong></font> deve ser precedido de um comando <font color=blue><strong>".$this->command['closetag']."</strong></font>.",false);
				}
			}

			//Open TAGS
			if (isset($this->command['opentag']))
			{
				//echo "Aqui";
				//echo "open:".$this->command['opentag']."<BR>";
				$this->tags[$this->command['opentag']][]=$this->command['opentag'];
			}
			if ($this->debug)
			{
				echo 'Regex: '.$this->command['regex'].'<br>';
			}

			//Has REGEX
			//dump ($this->command['regex']);
			if ($this->command['regex'])
			{
				if (preg_match($this->command['regex'],$buffer,$item))
				{
					if ($this->debug)
						var_dump ($item);

					for ($f=1;$f<count($item);$f++)
					{
						if ($f == $this->command['parameters'])
							$outparam=$this->ParseParams($item[$f]);
						else
							$outitem[$f]=$this->ParseValue($f,$item[$f]);
					}

					if ($this->debug)
						var_dump ($outitem);
					//var_dump($outparam);
					$output=$this->command['output'];
					if ($this->debug)
						echo "output:".htmlspecialchars($output)."<br>";
					while(preg_match('|(.*?)\<#(.*?)#\>(.*)|is',$output,$item))
					{
						if ($this->debug)
							var_dump($item);
						if (is_numeric($item[2]))
						{
							$output = $item[1].$outitem[$item[2]].$item[3];
						}
						else
						{
							//echo "<br>Localizei param: ".$item[2].' - '. substr($item[2],2,strlen($item[2])-2).'<br>';

							$output = $item[1].$outparam[substr($item[2],2,strlen($item[2])-2)].$item[3];
						}
					}

					if ($this->debug)
						echo '<P>';
					return $output;
				}
				else
				{
					$this->Error("Comando <font color=blue><strong>'$cmd'</strong></font> n&atilde;o identificado");
				}
			}
			else
			{
				return $this->command['output'];
			}
		}
		//No REGEX
		else
			$this->Error("Comando <font color=blue><strong>'$cmd'</strong></font> n&atilde;o identificado");

	}

	function ParseParams($buffer)
	{
		$buffer=trim ($buffer);
		while (preg_match("|(.*?)\=\s*\[(.*?)\](.*)|is",$buffer,$item))
		{
			//var_dump($buffer);
			$item[1]=trim($item[1]);
			//echo $item[2];
			$value=$this->Evaluate($item[2],$this->command['paramitens'][$item[1]]);
			if (!$this->command['paramitens'][$item[1]])
			{
				$this->Error("Par&acirc;metro <font color=blue><strong>".$item[1]."</strong></font> n&atilde;o identificado.");
			}
			else
			{
				$out[$item[1]]=$value;
			}
			$buffer = $item[3];
		}

		if (isset($this->command['paramforce']) && is_array($this->command['paramforce']))
		{
			foreach ($this->command['paramforce'] as $param)
			{
				if (!isset($out[$param]))
				{
					$this->Error("O par&acirc;metro <font color=blue><strong>$param</strong></font> &eacute; obrigat&oacute;rio.");
				}
			}
		}
		$out=$this->AddDefaultParams($out);
		//echo '<BR>Saida do AddDefault<BR>';
		//var_dump($out);
		//echo '<p>';
		return $out;
	}

	function AddDefaultParams($array)
	{
		//echo 'Default<BR>';
		//var_dump($array);
		//echo '<p>';
		if (is_array($this->command['paramitens']))
		{
			foreach ($this->command['paramitens'] as $param=>$val)
			{
				//echo "ParamITEM: $param<BR>";
				if (!isset($array[$param]))
				{
					if (isset($this->command['paramdefault'][$param]))
					{
						$array[$param]=$this->command['paramdefault'][$param];
					}
					else
					{
						if ($val=='n')
							$array[$param]=-1;
						else
							$array[$param]='""';
					}
				}
			}
		}
		//echo 'Out Default<BR>';
		//var_dump($array);
		//echo '<p>';

		return $array;
	}

	function ParseValue($pos,$value)
	{
		$value=trim($value);
		$check=$this->command['itens'];
		if ($check=='')
		{
			$this->Error("Item <font color=blue><strong>$value</strong></font> na posi&ccedil;&atilde;o <font color=blue><strong>'$pos'</strong></font> n&atilde;o existe na defini&ccedil;&atilde;o da fun&ccedil;&atilde;o");
			return false;
		}
		else
		{
			$value=$this->Evaluate($value,$check[$pos]);
			//echo $value;
			return $value;
		}
	}

	function Evaluate($expression,$type=false)
	{
		$exp=$expression;
		$pout='/(.*?)(\'|")(.*)/is';
		$out = "";

		//echo "E: ".$expression."<BR>";
		$preg=$pout;
		while (strlen($expression))
		{
			if (preg_match($preg,$expression,$passo1))
			{
				if ($preg==$pout)
				{
					//$passo2_start=getmicrotime();
					while (preg_match('|(.*?)([#$%])([a-z0-9_]+)(.*)|is',$passo1[1],$passo2))
					{
						$passo1[1]=$passo2[4];
						$out .= $passo2[1];
						switch ($passo2[2])
						{
							case '#':
								$out .= $this->ParseObjectData("#".$passo2[3]);
								break;
							case '$':
								$out .= '$GLOBALS["'.$passo2[3].'"]';
								break;
							case '%':
								$out .= $this->ParseMacro("%".$passo2[3]);
								break;
						}
					}
					$out .=$passo1[1];

					if (isset($passo1[3]))
					{
						$out .= $passo1[2];
						$preg='|(.*?)([\\\\'.$passo1[2].'])(.*)|is';
					}
					if (isset($passo1[3])) $expression=$passo1[3];
					else $expression="";
				}
				else
				{
					$out .= $passo1[1].$passo1[2];
					if ($passo1[2]=='\\')
					{
						$out .=$passo1[3][0];
						$passo1[3]=substr($passo1[3],1);
					}
					else
					{
						$preg=$pout;
					}
					if (isset($passo1[3])) $expression=$passo1[3];
					else $expression="";
				}
			}
			else
			{
				if ($preg==$pout)
				{
					$pout='|(.*)|';
					$preg=$pout;
				}
				else
				{
					$this->Error ('A express&atilde;o <font color="blue">'.$exp.'</font> tem um n&uacute;mero &iacute;mpar de aspas ou plics');
				}
			}

		}
		//echo "OUT: $out<BR>";
		return $out;
	}

	function ParseObjectData($data)
	{
		$array = explode("#",$data);
		//echo "ARRAY DE DADOS: <BR>";
		//var_dump($array);
		//echo '<BR>';
		if (count ($array)>2)
		{
			return '$'.$array[1].'->Valor("'.$array[2].'");';
		}
		else
		{
//			echo "*$array[1]*";
			return '$_OBJ_->Valor("'.$array[1].'")';
		}
	}

	function ParseMacro ($data)
	{
		return $this->macros[substr($data,1)];
	}
}


?>