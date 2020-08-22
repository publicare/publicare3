/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Função utilizada na página de classes, para verificar o tipo de classe selecionada
 * @param int numero - Código do tipodado
 * @returns void
 */
function classeVerificaTipoDado(numero)
{
    var tipo = $("#prop_" + numero + "_tipodado").val();
//    console.log(tipo);
    $("#linha_prop_" + numero + " div.linhabool").hide();
    $("#linha_prop_" + numero + " div.linharef").hide();
    
    $("#prop_" + numero + "_valorpadrao").removeClass("number");
    $("#prop_" + numero + "_valorpadrao").removeClass("dateITA");
    $("#prop_" + numero + "_valorpadrao").prop('readonly', false);
    $("#prop_" + numero + "_valorpadrao").unmask();
    $("#prop_" + numero + "_bol_1").removeClass("required");
    $("#prop_" + numero + "_bol_0").removeClass("required");
    
    // blob
    if (tipo == "1") {
        $("#prop_" + numero + "_valorpadrao").prop('readonly', true);
    }
    // bool
    if (tipo == "2") {
        $("#linha_prop_" + numero + " div.linhabool").show();
        $("#prop_" + numero + "_bol_1").addClass("required");
        $("#prop_" + numero + "_bol_0").addClass("required");
        $("#prop_" + numero + "_valorpadrao").addClass("number");
        $("#prop_" + numero + "_valorpadrao").mask("9");
        
    }
    // data
    if (tipo == "3") {
        $("#prop_" + numero + "_valorpadrao").addClass("dateITA");
        $("#prop_" + numero + "_valorpadrao").mask("99/99/9999");
    }
    // numero
    if (tipo == "4") {
        $("#prop_" + numero + "_valorpadrao").addClass("number");
        $("#prop_" + numero + "_valorpadrao").mask("0#");
    }
    // numero preciso
    if (tipo == "5") {
        $("#prop_" + numero + "_valorpadrao").mask('#.##0,00', { reverse: true });
    }
    // ref objeto
    if (tipo == "6") {
        $("#linha_prop_" + numero + " div.linharef").show();
        $("#prop_" + numero + "_cod_referencia_classe").addClass("required");
        $("#prop_" + numero + "_campo_ref").addClass("required");
    }

    
}

/**
 * Adiciona propriedade na classe
 * @param int cod - Código da propriedade
 * @param string prefixo - Prefixo da propriedade
 * @param string rotulo - Rótulo da propriedade
 * @param int tipodado - Código do tipodado
 * @param string valorpadrao - Texto do valor padrão
 * @param int posicao - Posicao da propriedade
 * @param int seguranca - Código do nível de usuário que poderá alterar o campo
 * @param string descricao - Descricao da propriedade
 * @param bool obrigatorio - Informa se a propriedade é de preenchimento obrigatório
 * @param string rot1bool - Rótulo da opção "1" do boleano
 * @param string rot2bool - Rótulo da opção "0" do boleano
 * @param int cod_classe_ref - Código da classe de referência
 * @param string campo_ref - Propriedade (metadado) que será usado como referência
 * @returns void
 */
function classeAdicionarPropriedade(cod, prefixo, rotulo, tipodado, valorpadrao, posicao, seguranca, descricao, obrigatorio, rot1bool, rot2bool, cod_classe_ref, campo_ref)
{
    if (typeof cod == "undefined") cod = 0;
    if (typeof prefixo == "undefined") prefixo = "";
    if (typeof rotulo == "undefined") rotulo = "";
    if (typeof tipodado == "undefined") tipodado = "";
    if (typeof valorpadrao == "undefined") valorpadrao = "";
    if (typeof posicao == "undefined") posicao = "";
    if (typeof seguranca == "undefined") seguranca = "";
    if (typeof descricao == "undefined") descricao = "";
    if (typeof obrigatorio == "undefined") obrigatorio = "0";
    if (typeof rot1bool == "undefined") rot1bool = "";
    if (typeof rot2bool == "undefined") rot2bool = "";
    if (typeof cod_classe_ref == "undefined") cod_classe_ref = "0";
    if (typeof campo_ref == "undefined") campo_ref = "";
    
    var numero = parseInt($("#numeroPropriedades").val(), 10);
    numero++;
    $("#numeroPropriedades").val(numero);
    numero = numero + "";
    
    var formulario = $("div.modelo_propriedade").clone(true);
    
    formulario[0].getElementsByClassName("panel-heading")[0].getElementsByClassName("numero")[0].innerHTML = numero;
    if (cod > 0) formulario[0].getElementsByClassName("panel-heading")[0].getElementsByClassName("codigo")[0].innerHTML = "(código: " + cod + ")";
    formulario[0].id = "linha_prop_" + numero;
    formulario.removeClass("modelo_propriedade");
    
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("ativa")[0].name = "prop_" + numero + "_ativa";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("ativa")[0].id = "prop_" + numero + "_ativa";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("ativa")[0].value = "1";
    
    // prefixo
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_nome";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_nome";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_nome";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("nomeatual")[0].name = "prop_" + numero + "_nomeatual";
    formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("nomeatual")[0].id = "prop_" + numero + "_nomeatual";
    if (prefixo != "")
    {
        formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("nomeatual")[0].value = prefixo;
        formulario[0].getElementsByClassName("col_1_1")[0].getElementsByClassName("campo")[0].value = prefixo;
    }
    
    // rotulo
    formulario[0].getElementsByClassName("col_1_2")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_rotulo";
    formulario[0].getElementsByClassName("col_1_2")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_rotulo";
    formulario[0].getElementsByClassName("col_1_2")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_rotulo";
    if (rotulo != "")
    {
        formulario[0].getElementsByClassName("col_1_2")[0].getElementsByClassName("campo")[0].value = rotulo;
    }
    
    // tipodado
    formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_tipodado";
    formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].setAttribute("onchange", "classeVerificaTipoDado('" + numero + "')");
    formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_tipodado";
    formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_tipodado";
    if (tipodado != "")
    {
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_tipodado2";
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_tipodado2";
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].value = tipodado;
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo")[0].disabled = true;
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo2")[0].name = "prop_" + numero + "_tipodado";
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo2")[0].id = "prop_" + numero + "_tipodado";
        formulario[0].getElementsByClassName("col_1_3")[0].getElementsByClassName("campo2")[0].value = tipodado;
    }
    
    // valorpadrao
    formulario[0].getElementsByClassName("col_4_1")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_valorpadrao";
    formulario[0].getElementsByClassName("col_4_1")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_valorpadrao";
    formulario[0].getElementsByClassName("col_4_1")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_valorpadrao";
    if (valorpadrao != "")
    {
        formulario[0].getElementsByClassName("col_4_1")[0].getElementsByClassName("campo")[0].value = valorpadrao;
    }
    
    // posicao
    formulario[0].getElementsByClassName("col_4_2")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_posicao";
    formulario[0].getElementsByClassName("col_4_2")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_posicao";
    formulario[0].getElementsByClassName("col_4_2")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_posicao";
    if (posicao != "")
    {
        formulario[0].getElementsByClassName("col_4_2")[0].getElementsByClassName("campo")[0].value = posicao;
    }
    else
    {
        formulario[0].getElementsByClassName("col_4_2")[0].getElementsByClassName("campo")[0].value = numero;
    }
    
    // seguranca
    formulario[0].getElementsByClassName("col_4_3")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_seguranca";
    formulario[0].getElementsByClassName("col_4_3")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_seguranca";
    formulario[0].getElementsByClassName("col_4_3")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_seguranca";
    if (seguranca != "")
    {
        formulario[0].getElementsByClassName("col_4_3")[0].getElementsByClassName("campo")[0].value = seguranca;
    }
    
    // descricao
    formulario[0].getElementsByClassName("col_5_1")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_descricao";
    formulario[0].getElementsByClassName("col_5_1")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_descricao";
    formulario[0].getElementsByClassName("col_5_1")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_descricao";
    if (descricao != "")
    {
        formulario[0].getElementsByClassName("col_5_1")[0].getElementsByClassName("campo")[0].innerHTML = descricao;
    }
    
    // obrigatorio
    formulario[0].getElementsByClassName("col_5_2")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_obrigatorio";
    formulario[0].getElementsByClassName("col_5_2")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_obrigatorio";
    if (obrigatorio != "0")
    {
        formulario[0].getElementsByClassName("col_5_2")[0].getElementsByClassName("campo")[0].checked = true;
    }
    
    // rotulo 1 booleano
    formulario[0].getElementsByClassName("col_2_2")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_bol_1";
    formulario[0].getElementsByClassName("col_2_2")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_bol_1";
    formulario[0].getElementsByClassName("col_2_2")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_bol_1";
    if (rot1bool != "")
    {
        formulario[0].getElementsByClassName("col_2_2")[0].getElementsByClassName("campo")[0].value = rot1bool;
    }
    
    // rotulo 2 booleano
    formulario[0].getElementsByClassName("col_2_3")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_bol_0";
    formulario[0].getElementsByClassName("col_2_3")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_bol_0";
    formulario[0].getElementsByClassName("col_2_3")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_bol_0";
    if (rot2bool != "")
    {
        formulario[0].getElementsByClassName("col_2_3")[0].getElementsByClassName("campo")[0].value = rot2bool;
    }
    
    // classe referencia
    formulario[0].getElementsByClassName("col_3_1")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_cod_referencia_classe";
    formulario[0].getElementsByClassName("col_3_1")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_cod_referencia_classe";
    formulario[0].getElementsByClassName("col_3_1")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_cod_referencia_classe";
    if (cod_classe_ref != "")
    {
        formulario[0].getElementsByClassName("col_3_1")[0].getElementsByClassName("campo")[0].value = cod_classe_ref;
    }
    
    // campo de referencia
    formulario[0].getElementsByClassName("col_3_2")[0].getElementsByClassName("titulo")[0].htmlFor = "prop_" + numero + "_campo_ref";
    formulario[0].getElementsByClassName("col_3_2")[0].getElementsByClassName("campo")[0].name = "prop_" + numero + "_campo_ref";
    formulario[0].getElementsByClassName("col_3_2")[0].getElementsByClassName("campo")[0].id = "prop_" + numero + "_campo_ref";
    if (campo_ref != "")
    {
        formulario[0].getElementsByClassName("col_3_2")[0].getElementsByClassName("campo")[0].value = campo_ref;
    }
    
    // container_alert
    formulario[0].getElementsByClassName("container_alert")[0].id = "container_" + numero + "_alert";
    
    formulario[0].getElementsByClassName("btnapagar")[0].setAttribute("onclick", "classeApagarPropriedade('" + numero + "')");
    
    document.getElementById("container_propriedades").appendChild(formulario[0]);
    formulario.show();
    
    classeVerificaTipoDado(numero);
}

/**
 * Exibe alerta para apagar propriedades da classe
 * @param int linha - Número da propriedade
 * @returns void
 */
function classeApagarPropriedade(linha)
{
    if (typeof linha == "undefined") linha = 0;
    if (linha != 0)
    {
        var alert = $("div.modelo_apagar").clone(true);
        alert.removeClass("modelo_apagar");
        
        alert[0].getElementsByClassName("apagar")[0].setAttribute("onclick", "classeConfirmaApagarPropriedade('" + linha + "')");
        
        document.getElementById("container_" + linha + "_alert").appendChild(alert[0]);
        alert.show();
    }
}

/**
 * Confirma a função de apagar propriedade
 * @param int linha - Número da propriedade
 * @returns void
 */
function classeConfirmaApagarPropriedade(linha)
{
    $("#prop_" + linha + "_ativa").val("0");
    $("#linha_prop_" + linha).hide();
}

/**
 * Exibe alerta para apagar classe
 * @returns void
 */
function classeApagarClasse()
{
    var alert = $("div.modeloapagarclasse").clone(true);
    alert.removeClass("modeloapagarclasse");
    document.getElementById("container_apagarclasse").appendChild(alert[0]);
    alert.show();
}

/**
 * Confirma apagar classe
 * @returns void
 */
function classeConfirmaApagarClasse()
{
    $("#apagar_classe").val("1");
    $("#formClasse").submit();
}

function classeApagaObjeto(numero)
{
    $("#linha_obj_" + numero).remove();
}

function classeAdicionarObjeto()
{
    var valor = $("#txtPode").val().trim();
    if (valor!="")
    {
        var vvalor = valor.split(",");
        for (var i=0; i<vvalor.length; i++)
        {
            var obj = vvalor[i].trim();
            var codigo = 0;
            var url = "";
            var string = "";
            var contobjs = $("#numeroobjs").val();
            if ($.isNumeric(obj))
            {
                codigo = obj;
            }
            else
            {
                url = obj;
            }
            contobjs++;
            string = '<li id="linha_obj_' + contobjs + '"><input type="hidden" name="objetos[]" value="' + codigo + '" /><input type="hidden" name="objetosurls[]" value="' + url + '" />(' + codigo + ') ...  - (/' + url + ') <a href="#" onclick="classeApagaObjeto(\'' + contobjs + '\'); return false;"><i class="fapbl fapbl-remove" title="Remover"></i></a></li>';
            $("#listaobjetos").append(string);
            $("#numeroobjs").val(contobjs);
        }
        $("#txtPode").val("");
    }
}

/**
 * Carrega <select> de views da tela de criação/edição de objetos
 * @param string campoView - Nome do select de views
 * @param string campoPele - Nome do select de peles
 * @param array listaPeles - Array de peles
 * @param array listaViews - Array de views
 * @param string pastaPele - Pasta onde ficam as peles
 * @param string pastaDefault - Pasta onde ficam as views
 * @param string selecao - Valor da view do objeto
 * @returns void
 */
function objetoCarregaSelectViews(campoView, campoPele, listaPeles, listaViews, pastaPele, pastaDefault, selecao)
{
    if (typeof selecao == "undefined") selecao = "";
    
    $("#" + campoView).empty();
    
    $("#" + campoView).append($('<option>', {
        value: "",
        text : ". selecione ." 
    }));
    
    var cod_pele = $("#" + campoPele).val();
    var prefixo_pele = "";
    var nome_pele = "";
    if (cod_pele != "")
    {
        $.each(listaPeles, function (i, item) {
            if (item.codigo == cod_pele)
            {
                prefixo_pele = item.prefixo;
                nome_pele = item.texto;
                return;
            }
        });
    }
    
    if (prefixo_pele != "")
    {
        $.each(listaViews[prefixo_pele], function (i, item) {
            var valor = pastaPele + prefixo_pele + "/" + item;
            $("#" + campoView).append($('<option>', {
                value: valor,
                text: "(" + nome_pele + ") " + item,
                selected: (valor == selecao)
            }));
        });
    }
    
    $.each(listaViews["default"], function (i, item) {
        var valor = pastaDefault + item;
        $("#" + campoView).append($('<option>', {
            value: valor,
            text : "(default) " + item,
            selected: (valor == selecao)
        }));
    });
}

/**
 * Exibe alerta para apagar propriedades da classe
 * @param int linha - Número da propriedade
 * @returns void
 */
function peleApagar()
{
	var alert = $("div.modelo_apagar").clone(true);
	alert.removeClass("modelo_apagar");
        
	document.getElementById("container_alerta").appendChild(alert[0]);
	alert.show();
}