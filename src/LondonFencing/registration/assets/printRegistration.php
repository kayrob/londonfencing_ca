<?php
require_once('../../../../inc/init.php');
require_once dirname(__DIR__).'/Registration.php';
use LondonFencing\Registration as REG;
if (isset($_GET['s']) && is_numeric($_GET['s']) && isset($_GET['r'])){
    $reg = new REG\Registration($db);
    $regNfo = $reg->getSavedRegistration($_GET['s'],$_GET['r']);
    
    if (isset($regNfo['isRegistered']) && (int)$regNfo['isRegistered'] == 1){
?>
<!doctype html>
<html>
    <head>
        <title>London Fencing Club</title>
        <style type="text/css">
            body {font-family: Arial, Verdana, Sans-serif; font-size:14px; width:740px;}
            div{width:640px;display:block;margin:0 0 5px 50px}
            label {line-height: 24px;display:inline-block;width: 230px; font-weight:bold;}
            div.sig{margin:40px 0 10px 50px}
            #head{font-size: 18px;text-transform: uppercase;text-align:center;font-weight:bold}
            #footer{margin-top:20px;text-align:center}
        </style>
        <!--[if lte IE7]>
        <style type="css">
                label {display:inline;}
        </style>
        <![endif] -->
    </head>    
    <body>
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
