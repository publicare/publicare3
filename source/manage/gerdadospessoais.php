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

global $_page;
?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#input_senha').pstrength();
    });
    
    // Checa Validação Senha
    function ChecaValidade(frm)
    {
        var regex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/;

        if (frm.email.value == '')
        {
            alert("O campo email não pode ficar em branco");
            return false;
        }

        if (frm.senha.value != frm.confirma.value)
        {
            alert("Senha diferente da confirmação");
            return false;
        }
        var texto = $("#input_senha_text span").html();

        if (texto == null || texto == "Senha insegura!" || texto == "Muito pequena" || texto == "Média" || texto == "Fraca")
        {
            alert("É necessário que altere também o campo senha utilizando a complexidade mínima de segurança exigida.\n\
                   Ex: Letras maiúsculas, números, caracteres especiais, etc... ");
            return false;
        }
        return true;
    }
</script>

<!-- === Gerenciar Dados Pessoais === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Gerenciar Dados Pessoais</b></h3></div>
    <div class="panel-body">
        
        <!-- === Dados do usuário === -->
        <div class="panel panel-info">
            <div class="panel-heading">Dados do usuário (<?php echo( $_SESSION['usuario']['nome']); ?>)</div>
            <form action="do/gerdadospessoais_post/<?php echo($_page->_objeto->Valor('cod_objeto')); ?>.html" method="POST">
                <input type="hidden" name="cod_usuario" value="<?php echo $_SESSION['usuario']['cod_usuario']; ?>">
                <input type="hidden" name="nomehidden" value="<?php echo $_SESSION['usuario']['nome']; ?>">
                <input type="hidden" name="secao" value="<?php echo $_SESSION['usuario']['secao']; ?>">
                <input type="hidden" name="nome" value="<?php echo $_SESSION['usuario']['nome']; ?>">
                <input type="hidden" name="altera_senha" value="0">
                <div class="panel-body">
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">Usu&aacute;rio</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="text" name="nome" id="InputNome" value="<?php echo $_SESSION['usuario']['nome']; ?>">
                        </div>
                    </div>
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">&Aacute;rea vinculada</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="text" name="secao" id="InputSecao" value=" <?php echo $_SESSION['usuario']['secao']; ?>">
                        </div>
                    </div>
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">E-mail</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="text" name="email" id="InputEmail" value="<?php echo $_SESSION['usuario']['email']; ?>">
                        </div>
                    </div>
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">Ramal(contato)</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="text" name="ramal" id="InputRamal" value="<?php echo $_SESSION['usuario']['ramal']; ?>">
                        </div>
                    </div>
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">Senha</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="password" id="input_senha" name="senha">
                        </div>
                    </div>
                    <div class="tabela row form-group">
                        <label for="InputNome" class="col-md-3 col-form-label">Confirme a Senha</label>
                        <div class="col-md-9">
                            <input class="form-control" required="required" type="password" name="confirma">
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="text-align: right">
                    <input type="submit" name="submit" value="Gravar" class="btn btn-success">
                </div>
            </form>
        </div>
        <!-- === Final === Dados do usuário === -->
        
    </div>
</div>
<!-- === Final === Gerenciar Dados Pessoais === -->


