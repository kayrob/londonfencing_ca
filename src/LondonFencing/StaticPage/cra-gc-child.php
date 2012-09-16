<?php
define('FPDF_FONTPATH',dirname(dirname(dirname(__DIR__))).'/vendors/font/');
$url = "http://".$_SERVER["SERVER_NAME"];
require_once(dirname(dirname(dirname(__DIR__))).'/vendors/html2fpdf/html2fpdf.php');
ob_start();
?>
<html>
    <body>
<table>
        <tr><td colspan="3"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="30px" /></td></tr>
    <tr>
        <td width="33%">&nbsp;</td>
        <td width="33%"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/logo_grn.jpg" alt="" width="176px" height="80px" /></td>
        <td width="33%">&nbsp;</td>
    </tr>
    <tr><td align="right"><b>LONDON FENCING CLUB</b></td><td colspan="2">c/o 1037 Viscount Road, London, Ontario, N6K 1H5</td></tr>
    <tr><td colspan="3"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
</table>
<table>
    <tr><td width="50%"><b>OFFICIAL RECEIPT FOR TAX PURPOSES:</b></td><td width="50%"><b>AMOUNT RECEIVED:</b></td></tr>
    <tr><td>Issued: %DOI%</td><td>$%AMT% CDN</td></tr>
    <tr><td>Tax Year: %YEAR%</td><td>100% Eligible for Children's Tax Credit</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr><td width="50%"><b>PAYMENT FROM:</b></td><td width="50%"><b>RECEIVED FOR:</b></td></tr>
    <tr><td>%NAME%</td><td>Child's Name: %CHILD%</td></tr>
    <tr><td>%ADDRESS%</td><td>Child's Birth Date: %DOB%</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr><td colspan="2"><b>FEE TYPE</b>: %FEETYPE%</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr>
        <td>&nbsp;</td>
        <td><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/tax_img2.jpg" alt="" width="139px" height="27px" /></td>
    </tr>
    <tr><td>&nbsp;</td><td>Signing Officer, Andrea Csiba</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>
<p>----------------------------------------------------------------------------------------------------------------------------------------------</p>
<table>
        <tr><td colspan="3"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="5 px" /></td></tr>
    <tr>
        <td width="33%">&nbsp;</td>
        <td width="33%"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/logo_grn.jpg" alt="" width="176px" height="80px" /></td>
        <td width="33%">&nbsp;</td>
    </tr>
    <tr><td align="right"><b>LONDON FENCING CLUB</b></td><td colspan="2">c/o 1037 Viscount Road, London, Ontario, N6K 1H5</td></tr>
    <tr><td colspan="3"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
</table>
<table>
    <tr><td width="50%"><b>OFFICIAL RECEIPT FOR TAX PURPOSES:</b></td><td width="50%"><b>AMOUNT RECEIVED:</b></td></tr>
    <tr><td>Issued: %DOI%</td><td>$%AMT% CDN</td></tr>
    <tr><td>Tax Year: %YEAR%</td><td>100% Eligible for Children's Tax Credit</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr><td width="50%"><b>PAYMENT FROM:</b></td><td width="50%"><b>RECEIVED FOR:</b></td></tr>
    <tr><td>%NAME%</td><td>Child's Name: %CHILD%</td></tr>
    <tr><td>%ADDRESS%</td><td>Child's Birth Date: %DOB%</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr><td colspan="2"><b>FEE TYPE</b>: %FEETYPE%</td></tr>
    <tr><td colspan="2"><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/spacer.jpg" alt="" width="5px" height="8px" /></td></tr>
    <tr>
        <td>&nbsp;</td>
        <td><img src="<?php echo $url;?>/src/LondonFencing/StaticPage/assets/img/tax_img2.jpg" alt="" width="139px" height="27px" /></td>
    </tr>
    <tr><td>&nbsp;</td><td>Signing Officer, Andrea Csiba</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>
<p>----------------------------------------------------------------------------------------------------------------------------------------------</p>
</body>
</html>
<?php
    $content = ob_get_contents();
    ob_end_clean();
    try
    {
        $pdf = new HTML2FPDF();
        $pdf->SetTopMargin(1);
        $pdf->AddPage();
        $pdf->WriteHTML($content);
        $pdf->Output('test.pdf','D');
    }
    catch(Exception $e) {
        echo $e->getMessage();
        exit;
    }

