<?php
require_once('../../../../inc/init.php');
require_once dirname(__DIR__).'/registration.php';

use LondonFencing\registration as REG;

if (isset($_GET['s']) && is_numeric($_GET['s']) && isset($_GET['r'])){
    $reg = new REG\Registration($db);
    $regNfo = $reg->getSavedRegistration($_GET['s'],$_GET['r']);
    
    if (isset($regNfo['isRegistered']) && (int)$regNfo['isRegistered'] == 1){
?>
<!doctype html>
<html>
    <head>
        <title>London Fencing Club</title>
        <style type="text/css" media="all">
            body {font-family: Arial, Verdana, Sans-serif; font-size:14px; width:740px;}
            div{width:640px;display:block;margin:0 0 5px 50px}
            label {line-height: 24px;display:inline-block;width: 230px; font-weight:bold;}
            div.sig{margin:40px 0 10px 50px}
            #head{font-size: 18px;text-transform: uppercase;text-align:center;font-weight:bold}
            #footer{margin-top:20px;text-align:center}
            .termsTitle, .termsForm{text-transform: uppercase; font-weight: bold; text-align: center}
            #aprint{text-align: center;
                background: #4a762b; 
                border: none;
                 -moz-box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                -webkit-box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                box-shadow: 1px 1px 5px rgba(0,0,0,0.12);
                font-size: 12px;
                color: #FFF;
                padding: 8px 20px;
                border-radius: 3px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
                font-style: italic;
                margin-top: 10px;
                line-height: normal; 
                cursor:pointer}
        </style>
        <style type="text/css"media="print">
            #termsRefs{
                page-break-before: always;
            }
            #aprint{display:none;}
        </style>
        <!--[if lte IE7]>
        <style type="css">
                label {display:inline;}
        </style>
        <![endif] -->
    </head>    
    <body>
        <a id="aprint" onclick="window.print()" class="btnStyle">Print</a>
        <div id="head">London Fencing Club Registration: <?php echo $regNfo['sessionName'];?></div>
        <div class="terms">
        </div>
    <div>
        <label>First Name: </label><?php echo $regNfo["firstName"];?>
    </div>
    <div>
        <label>Last Name: </label><?php echo $regNfo["lastName"];?>
    </div>
    <div>
        <label>Birth Date: </label><?php echo date('Y-m-d',$regNfo["birthDate"]);?>
    </div>
    <div>
        <label>Gender: </label><?php echo $regNfo["gender"];?>
    </div>
    <div>
        <label>Address: </label><?php echo $regNfo["address"];?>
    </div>
    <div>
        <label>Unit/Apt: </label><?php echo $regNfo["address2"];?>
    </div>
    <div>
        <label>City: </label><?php echo $regNfo["city"];?>
    </div>
    <div>
        <label>Province: </label><?php  echo $regNfo["province"]; ?>
    </div>
    <div>
        <label>Postal Code: </label><?php echo $regNfo["postalCode"];?>
    </div>
    <div>
        <label>Phone Number: </label><?php echo $regNfo["phoneNumber"];?>
    </div>
    <div>
        <label>Email: </label><?php echo $regNfo["email"];?>
    </div>
    <div>
        <label>Parent/Guardian: </label><?php echo (trim($regNfo["parentName"]) == '' ? 'N/A' : $regNfo["parentName"]); ?>
    </div>
    <div>
        <label>Emergency Contact: </label><?php echo $regNfo["emergencyContact"];?>
    </div>
    <div>
        <label>Emergency Phone: </label><?php echo $regNfo["emergencyPhone"];?>
    </div>
    <div class="sig">
        <label>Signature: </label>_____________________________________________
    </div>
        <div class="sig">
        <label>Date Signed: </label>______________________________________________
    </div>
    </div>
    <div class="sig">
        <label>Fee: </label>$<?php echo number_format($regNfo['fee'],2);?><br /><br />Please make cheques payable to: London Fencing Club
    </div>
    <div id="footer"><img src="/src/LondonFencing/registration/assets/img/printLogo.png" /></div>
    <div id="termsRefs">
    <?php
    if ((date('U') - $regNfo["birthDate"])/(60*60*24*365) < 18 || trim($regNfo["parentName"]) != ''){
        $terms = str_replace('%DATE%', date('F j, Y', $regNfo['eventStart']), file_get_contents(__DIR__.'/minorTerms.php'));
        $terms = str_replace('%MINORNAME%', $regNfo['firstName'].' '.$regNfo['lastName'], $terms);
        echo str_replace('%PARENTNAME%', $regNfo['parentName'], $terms);
    }
    else{
        $terms = str_replace('%DATE%', date('F j, Y', $regNfo['eventStart']), file_get_contents(__DIR__.'/adultTerms.php'));
        echo str_replace('%NAME%', $regNfo['firstName'].' '.$regNfo['lastName'], $terms);
    }
    ?>
    </body>
</html>
<?php
    }
    else{
        header('location:http://'.$_SERVER["SERVER_NAME"]);
    }
}
else{
    header('location:http://'.$_SERVER["SERVER_NAME"]);
}
