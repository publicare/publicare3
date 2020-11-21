<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
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
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

if (isset($_GET["naoincluirheader"]))
{
    $qrCode = new QrCode($this->container["config"]->portal["url"]."/".$this->container["objeto"]->valor("url_amigavel"));
    $qrCode->setSize(800);
    $qrCode->setMargin(10); 

    // Set advanced options
    $qrCode->setWriterByName('png');
    $qrCode->setEncoding('UTF-8');
    $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
    $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
    $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
    // $qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER());
    // xd(__DIR__.'/../../assets/blobs/ic_agenda.gif');
    // $qrCode->setLogoPath(__DIR__.'/../../assets/imagens/sort_asc.png');
    // $qrCode->setLogoSize(150, 200);
    $qrCode->setValidateResult(false);

    // Round block sizes to improve readability and make the blocks sharper in pixel based outputs (like png).
    // There are three approaches:
    $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
    $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
    $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary

    // Set additional writer options (SvgWriter example)
    $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

    // Directly output the QR code
    header('Content-Type: '.$qrCode->getContentType());
    echo $qrCode->writeString();

    // Generate a data URI to include image data inline (i.e. inside an <img> tag)
    $dataUri = $qrCode->writeDataUri();
    // QRcode::png($this->container["config"]->portal["url"]."/".$this->container["objeto"]->valor("url_amigavel"), false, QR_ECLEVEL_H, 20);
    exit();
}
$classname = $this->container["objeto"]->valor("prefixoclasse");
$classe = $this->container["classe"]->pegarInfo($this->container["objeto"]->valor("cod_classe"));
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3><b>QRCode</b></h3>
        <p class="padding-top10">
            <strong>QRcode para</strong>: <?php echo($this->container["objeto"]->valor("titulo")) ?> (<?php echo($this->container["objeto"]->valor("cod_objeto")) ?>) - <strong><?php echo($this->container["config"]->portal["url"]."/".$this->container["objeto"]->valor("url_amigavel")); ?></strong><br />
            <strong>Classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>) [<?php echo($classe["classe"]["prefixo"]); ?>]<br />
            <strong>Vers&atilde;o</strong>: <?php echo($this->container["objeto"]->valor("versao")) ?>
        </p>
    </div>
    
        <div class="panel-body">
			
            <!-- === Objeto === -->
            <div class="panel panel-info modelo_propriedade">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($this->container["objeto"]->valor("titulo")); ?></h3></div>
                        <div class="col-sm-3 text-right titulo-icones">
                                <a href="<?php echo($this->container["config"]->portal["url"]); ?><?php echo($this->container["objeto"]->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
                        </div>
                    </div>
                </div>

                <div class="panel-body">									   
                    <label for="message" class="padding-bottom10"><?php echo($this->container["config"]->portal["url"]."/".$this->container["objeto"]->valor("url_amigavel")); ?></label><br />
                    <img src="do/qrcode/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html?naoincluirheader" style="width: 50%;" />
                </div>
            </div>
            <!-- === Final === Objeto === -->

        </div>
        <div class="panel-footer" style="text-align: right;">
            <a onclick="history.back()" class="btn btn-success">Voltar</a>
        </div> 
</div>


