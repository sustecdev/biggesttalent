<?
include("config.php");
include("functions.php");

if($_POST)
{
    $uid=$_SESSION['puid'];
    
    $passcode = '1234';
    $methodcode = 'aes128';
    
    $ivcode = "1234567812345678";
    
    if($_POST['pin']=='')
    {
        $err="PIN does not match.";
        exit;
    }
    
    $short_pin = $_POST['pin'];
    
    $pin = getSingleValue("pi_account","where uid='$uid'","pin");
    
    $pinn =  openssl_decrypt($pin, $methodcode, $passcode,false,$ivcode);
    $u = 0;
    
    
    
    if($_SESSION["skey"]!='')
    {
        
        
        foreach($_SESSION["skey"] as $i=>$v)
        {
            //echo $data;exit();
            if($pinn[$i]!=$short_pin[$u])
            {
                
                //return -1;
                $err="PIN does not match.";
            }
            $u++;
        }
    }
    elseif(empty($_SESSION["skey"]))
    {
        $match = explode(",",$_POST["pin"]);
        $match1 = array();
        foreach($match as $i=>$key)
        {
            $match1[$key]  = $key;
        }
        
        foreach($match1 as $i=>$v)
        {
            //echo $data;exit();
            if($pinn[$i]!=$short_pin[$u])
            {
                $err="PIN does not match.";
                //return -1;
            }
            $u++;
        }
    }
    else
    {
        //return -1;
        $err="PIN does not match.";
    }
    
    if($err=='')
    {
        ?>
    <form id="frm" action="auth.php" method="post">
    	<input type="hidden" name="uid" value="<?=$_SESSION['puid']?>">
        <input type="hidden" name="password" value="<?=$_SESSION['ppwd']?>">
    </form>
    <script>
		document.getElementById('frm').submit();
	</script>
    <?
	exit;
	}
}



include("header.php");
?>
<style>
td {
			background-image: -webkit-linear-gradient(top,#5B5B5B 0,#3E3E3E 100%);
			background-image: -o-linear-gradient(top,#5B5B5B 0,#3E3E3E 100%);
			background-image: -webkit-gradient(linear,left top,left bottom,from(#5B5B5B),to(#3E3E3E));
			background-image: linear-gradient(to bottom,#5B5B5B 0,#3E3E3E 100%);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5B5B5B', endColorstr='#ff3E3E3E', GradientType=0);
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
			background-repeat: repeat-x;
			color:#00A2E8;
			font-weight:bold;
			height:72px;
			font-size:48px;
			cursor:pointer;
			
	}
</style>
    <div class="container" style="width: 100%; margin-top: 65px; max-width: 1320px; background:none;">

        <div class="row" style="padding-top:20px; padding-bottom:20px; font-size:18px;">
            <div class="col-md-12">
                <img src="images/headline.png" style="width:100%; margin:0 auto;" class="img img-responsive">
            </div>

            <div class="col-md-6"><p style="text-align:center; font-size:32px; font-weight:bold; color:#000;">200+ COUNTRIES</p></div>

            <div class="col-md-6"><p style="text-align:center; font-size:32px; font-weight:bold; color:#000;">5,000+ TALENTS</p></div>

            
            
        </div>
    </div>

    <div class="container" style="width: 100%; margin-top: 10px; max-width: 1320px; background: rgba(0, 0, 0, 0.4)">
        <div class="row" style="padding-top:20px; padding-bottom:20px; font-size:18px;">
        <p class="pp" style="font-weight:normal; font-size:16px; color:#fff; text-align:center; margin-top:30px; margin: 0 auto; margin-top:50px;">Logged in as <?='1'.str_pad($_SESSION['puid'],9,'0', STR_PAD_LEFT)?>. You are not <?='1'.str_pad($_SESSION['puid'],9,'0', STR_PAD_LEFT)?>? <a href="safezone.php">Click here</a> to log in</p>

                <div class="col-md-12" style="text-align:center;">
                	
                <?
                                        function getRandFields($str, &$opt=array()){
                                            if(count($opt)>2){
                                                return true;			
                                            }else{
                                                $pos = rand(0,5);
                                                    if(!isset($opt[$pos])){	
                                                    $opt[$pos] = $str[$pos];
                                                    }		
                                                getRandFields($str,$opt);	
                                            }			
                                        }
                                        $data34 = array();
                                        $str = 'ab21cd';
                                        getRandFields($str,$sKey);	
                                        foreach($sKey as $i=>$key){ 
                                            $data34[$i]  = $i;
                                        }
                                        $time = time()+3600*24*365*10;
                                        $_SESSION["skey"] = $data34;
                                        $convert = implode(" ",$data34);
                                        setcookie("skey",$convert,$time,"/",".ivc.travel");
                                        ?>
                                        <?
                                        if($err!='')
                                        {
                                        ?>
                                        <div class="alert alert-danger" style="margin-top:0px;" role="alert"><?=$err?></div>
                                        <?
                                        }
                                        ?>
                                        <p class="pp" style="font-weight:normal; font-size:22px; color:#fff; text-align:center; margin-top:20px;"><?php  echo "ENTER DIGITS";
                                        //print_r($_SESSION["skey"]);
                                        foreach ($_SESSION["skey"] as $b=>$rr){ echo " #".intval($rr+1); } 
                                        echo "<br>OF YOUR MASTER PIN";
                                        ?>
                                        </p>
                                        
                                        <form id="frm" action="" method="post">
                                            <input type="password" name="pin" id="pin" class="form-control" style="width: 355px; margin: 0 auto; height: 60px; border-radius: 30px; border: 2px solid #000; text-align: center; font-size: 16px; background:#FFF; color:#000; font-weight:bold; margin-top:15px;">
                                        </form>
                                        <br>
                        
                                        <table width="348" border="1" cellspacing="0" cellpadding="0" style="margin:0 auto; border-color:#000;">
                                          <tr>
                                            <td style="width:33.33%;" onClick="keypad(this, 1)">1</td>
                                            <td style="width:33.33%;" onClick="keypad(this, 2)">2</td>
                                            <td style="width:33.33%;" onClick="keypad(this, 3)">3</td>
                                          </tr>
                                          <tr>
                                            <td onClick="keypad(this, 4)">4</td>
                                            <td onClick="keypad(this, 5)">5</td>
                                            <td onClick="keypad(this, 6)">6</td>
                                          </tr>
                                          <tr>
                                            <td onClick="keypad(this, 7)">7</td>
                                            <td onClick="keypad(this, 8)">8</td>
                                            <td onClick="keypad(this, 9)">9</td>
                                          </tr>
                                          <tr>
                                            <td onClick="keypad(this, 0)">0</td>
                                            <td onClick="keypad(this, 'b')"><img src="https://safe.zone/images/backspace.png" width="34" height="23"  alt=""/></td>
                                            <td id="tr" onClick="document.getElementById('frm').submit();" style="font-size:16px; color:#21B24C;">NEXT</td>
                                          </tr>
                                        </table>
                                       <!-- <button class="btn btn-lg btn-success btn-block" id="tr" type="button" style="width: 450px; margin: 0 auto; height: 60px; border-radius: 30px; text-align: center; font-size: 22px; background:#F2F2F2; margin-top:10px; border: 2px solid #000; color:#cc0000;" onClick="transfer2();">TRANSFER</button>-->
                
                </div>
        </div>    

    </div>
    <script type="text/javascript">
  function keypad(td, key){
	if(key=='b')
	{
		if($('#pin').val()=='')
		{
			$('#heading').html('LOGIN');
			$('#sec-pin').hide();
			$('#sec-pernum').show();
			$('.hidep').show();
		}
		else
		{
			var txt = $('#pin');
			txt.val(txt.val().slice(0, -1));
		}
	}
	else if(key=='e')
	{
		
	}
	else
	{
		$('#pin').val($('#pin').val()+key);
	}
}
</script>  	
<?
include("footer.php");
?>