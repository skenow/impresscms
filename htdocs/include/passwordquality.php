<?php
##################################################################################################
# Medidor de Qualidade de Senhas
# Tipo: Core Hack
# Descri��o: Este hack cria um medidor de qualidade das senhas digitadas pelo usu�rio na hora do
# cadastro ou edi��o do perfil. Ele s� habilita o envio do formul�rio caso a senha digitada estiver
# dentro do padr�o definido na administra��o (Sistema=>Prefer�ncias=>Configura��o dos usu�rios).
# Este hack n�o altera o formul�rio de cadastro/edi��o de usu�rios da administra��o pois � de se
# que o administrador do site que necessita usar um hack deste use senhas seguras na hora de criar
# os usu�rios.
##################################################################################################
# Rodrigo Pereira Lima aka TheRplima
# therplima@gmail.com
# �ltima Atualiza��o: 16/09/2006
# Veja o hack funcionando aqui http://rwbanner.brinfo.com.br/register.php
##################################################################################################

function get_rnd_iv($iv_len)
{
   $iv = '';
   while ($iv_len-- > 0) {
      $iv .= chr(mt_rand() & 0xff);
   }
   return $iv;
}

function md5_encrypt($plain_text, $password, $iv_len = 16)
{
   $plain_text .= "x13";
   $n = strlen($plain_text);
   if ($n % 16) $plain_text .= str_repeat("{TEXTO}", 16 - ($n % 16));
   $i = 0;
   $enc_text = get_rnd_iv($iv_len);
   $iv = substr($password ^ $enc_text, 0, 512);
   while ($i < $n) {
      $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
      $enc_text .= $block;
      $iv = substr($block . $iv, 0, 512) ^ $password;
      $i += 16;
   }

   return base64_encode($enc_text);
}
function md5_decrypt($enc_text, $password, $iv_len = 16)
{
   $enc_text = base64_decode($enc_text);
   $n = strlen($enc_text);
   $i = $iv_len;
   $plain_text = '';
   $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
   while ($i < $n) {
      $block = substr($enc_text, $i, 16);
      $plain_text .= $block ^ pack('H*', md5($iv));
      $iv = substr($block . $iv, 0, 512) ^ $password;
      $i += 16;
   }
   return preg_replace('/\x13\x00*$/', '', $plain_text);
}

$senha = 'TheRplima';
$has_javascript = (isset($_GET['par']))?md5_decrypt($_GET['par'],$senha):null;
if(!isset($has_javascript)) {
  $valor_cript1 = md5_encrypt('ok_has_javascript', $senha);
  if ($_SERVER['QUERY_STRING'] == ""){
    echo '
    <script language="javascript">
    <!--
    window.location=window.location+"?par='.$valor_cript1.'";
    // -->
    </script>';
  }else{
    echo '
    <script language="javascript">
    <!--
    window.location="?par='.$valor_cript1.'"+"&'.$_SERVER['QUERY_STRING'].'";
    // -->
    </script>';
  }
  redirect_header('index.php',3,_US_REGFORM_NOJAVASCRIPT);
}
$config_handler =& xoops_gethandler('config');
$passConfig =& $config_handler->getConfigsByCat(2);
global $xoopsConfig;
echo '<script type="text/javascript" src="'.XOOPS_URL.'/include/passwordquality.js"></script>';

include_once XOOPS_ROOT_PATH."/modules/system/language/".$xoopsConfig['language']."/admin/preferences.php";
$tipo = explode("/",$_SERVER['PHP_SELF']);
if ($tipo[count($tipo)-1] == 'register.php'){
  $passField = 'pass';
  $tipo = 1;
  $tipo1 = 1;
}else{
  $passField = 'password';
  $tipo = $xoopsUser->getVar('uname');
  $tipo1 = $xoopsUser->getVar('email');
}
echo '<script type="text/javascript">
//Texto dos nomes dos n�veis de qualidade
var qualityName1 = "'._MD_AM_PASSLEVEL1.'";
var qualityName2 = "'._MD_AM_PASSLEVEL2.'";
var qualityName3 = "'._MD_AM_PASSLEVEL3.'";
var qualityName4 = "'._MD_AM_PASSLEVEL4.'";
var qualityName5 = "'._MD_AM_PASSLEVEL5.'";
var qualityName6 = "'._MD_AM_PASSLEVEL6.'";

var passField = "'.$passField.'";
var tipo = "'.$tipo.'";
var tipo1 = "'.$tipo1.'";

//Obtendo informa��es de configura��o do xoops
var minpass = "'.$passConfig['minpass'].'";
var pass_level = "'.$passConfig['pass_level'].'";
</script>';

//Campo senha do formul�rio mais barra de progresso
if ($passField == 'pass'){
  //Regras Regex para filtrar senha digitada
  $reg_form->addElement(new XoopsFormHidden("regex",'[^0-9]'));      //Regex para filrar somente os digitos num�ricos da string
  $reg_form->addElement(new XoopsFormHidden("regex3",'([0-9])\1+')); //Regex para filrar somente os digitos num�ricos repetidos e em sequ�ncia da string
  $reg_form->addElement(new XoopsFormHidden("regex1",'[0-9a-zA-Z]'));//Regex para filtrar os s�mbolos da string
  $reg_form->addElement(new XoopsFormHidden("regex4",'(\W)\1+'));    //Regex para filtrar os s�mbolos repetidos e em sequ�ncia da string
  $reg_form->addElement(new XoopsFormHidden("regex2",'[^A-Z]'));     //Regex para filtrar as letras mai�sculas da string
  $reg_form->addElement(new XoopsFormHidden("regex5",'([A-Z])\1+')); //Regex para filtrar as letras mai�sculas repetidas e em sequ�ncia da string

  $pass_tray = new XoopsFormElementTray(_US_PASSWORD, '');
  $pass_tray->setDescription(_US_REGFORM_WARNING);
  $pass_inp = new XoopsFormPassword('', $passField, 10, 72, $myts->htmlSpecialChars($pass));
		if ( defined('_ADM_USE_RTL') && _ADM_USE_RTL ){
  $pass_inp->setExtra('style="float:right;"');
	   } else {
  $pass_inp->setExtra('style="float:left;"');
           }
  $pass_tray->addElement($pass_inp, true);
		if ( defined('_ADM_USE_RTL') && _ADM_USE_RTL ){
  $div_progress = new XoopsFormLabel('',' <script language="javascript" src="'.XOOPS_URL.'/include/percent_bar_rtl.js"></script>');
	   } else {
  $div_progress = new XoopsFormLabel('',' <script language="javascript" src="'.XOOPS_URL.'/include/percent_bar.js"></script>');
           }
  $pass_tray->addElement($div_progress);
  $reg_form->addElement($pass_tray);
}else{
  //Regras Regex para filtrar senha digitada
  $form->addElement(new XoopsFormHidden("regex",'[^0-9]'));      //Regex para filrar somente os digitos num�ricos da string
  $form->addElement(new XoopsFormHidden("regex3",'([0-9])\1+')); //Regex para filrar somente os digitos num�ricos repetidos e em sequ�ncia da string
  $form->addElement(new XoopsFormHidden("regex1",'[0-9a-zA-Z]'));//Regex para filtrar os s�mbolos da string
  $form->addElement(new XoopsFormHidden("regex4",'(\W)\1+'));    //Regex para filtrar os s�mbolos repetidos e em sequ�ncia da string
  $form->addElement(new XoopsFormHidden("regex2",'[^A-Z]'));     //Regex para filtrar as letras mai�sculas da string
  $form->addElement(new XoopsFormHidden("regex5",'([A-Z])\1+')); //Regex para filtrar as letras mai�sculas repetidas e em sequ�ncia da string

  $pwd_text = new XoopsFormElementTray('', '');
  $pass_inp = new XoopsFormPassword('', $passField, 10, 72);
		if ( defined('_ADM_USE_RTL') && _ADM_USE_RTL ){
  $div_progress = new XoopsFormLabel('','<script language="javascript" src="'.XOOPS_URL.'/include/percent_bar_rtl.js"></script>');
	   } else {
  $div_progress = new XoopsFormLabel('','<script language="javascript" src="'.XOOPS_URL.'/include/percent_bar.js"></script>');
           }
  $pwd_text->addElement($pass_inp);
  $pwd_text->addElement($div_progress);
}
?>