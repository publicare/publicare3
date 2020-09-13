<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo submeter.php
 * @copyright MIT © 2020
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * 
 * Copyright (c) 2020 Publicare
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
?>

<form action="do/submeter_post/<? echo $this->container["objeto"]->valor($page, "cod_objeto");?>.html" method="post">
	<div class="pblAlinhamentoTabelas">
	<TABLE WIDTH=500 BORDER=0 CELLPADDING=0 CELLSPACING=8 class="pblTabelaGeral">
	<TR>
		<TD>
			<img border=0 src="html/imagens/portalimages/peca3.gif" ALT="" align="left"><font class="pblTituloBox">Solicitar publica&ccedil;&atilde;o</font><br>
			<font class="pblTextoForm"><? echo $this->container["objeto"]->valor($page, "titulo")?></font>
		</td>

		<TD width="120" class="pblAlinhamentoBotoes">
					<a class="ABranco" href="index.php/content/view/<? echo $this->container["objeto"]->valor($page, "cod_objeto")?>.html"><img border=0 src="/html/imagens/portalimages/exibir.png" ALT="Exibir Objeto" hspace="2"></a>
					<a href="#" onclick="history.back()"><img border=0 src="html/imagens/portalimages/voltar2.gif" ALT="Voltar"></a>
				</TD>
	</TR>


	<tr>
		<td class="pblFormTitle" colspan="2">
			<P>Coment&aacute;rios<br>
			<textarea name="message" class="pblInputForm" rows=5 cols=59 ><? echo $message ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="pblFormTitle"align="right" colspan="2">
			<input type="submit" class="pblBotaoForm" name="submit" value="Gravar">	
		</td>
	</tr>
	
	<tr><td colspan="2"><p class="pblAssinatura"><?php echo _VERSIONPROG; ?></p></td></tr>
	</table>
</form>