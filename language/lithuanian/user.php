<?php
// $Id: user.php,v 1.3 2007/05/05 11:12:43 catzwolf Exp $
// %%%%%%		File Name user.php 		%%%%%
define( '_US_CREATEACCOUNT', '<b>Užsiregistruoti</b>' );
define( '_US_CREATEACCOUNTTEXT', 'Užsiregistruokite, jei norite naudotis visais šio tinklalapio siūlomais malonumais.' );
define( '_US_CREATEACCOUNTSIGNUP', '<a href="register.php">Sukurti naują vartotojo praskyrą.</a>.' );
define('_US_LOSTPASSWORD','Pamiršote slaptažodį?');
define( '_US_LOSTPASSTEXT', 'Jei pametėte slaptažodį nusiraminkite, tiesiog vadovaukitės nurodytomis instrukcijomis.' );
define( '_US_PASSWORDRETRIEVAL', 'Atgauti slaptažodį' );
define('_US_NOPROBLEM','Nėra problemų. Paprasčiausiai įveskit el. pašto adresą kuris yra surištas su jūsų sąskaita.');
define( '_US_LOSTCLICK', 'Paspauskite bet kurį klavišą tik vieną kartą.' );
define( '_US_PERSONAL', 'Personal' );
// define( '_US_NOTIFICATIONS', 'Notifications' );
define( '_US_BIRTHDATE', 'Gimimo data' );
define( '_US_LOGIN', 'Prisijungimas:' );
define( '_US_IAMOVER', 'Aš sutinku, kad įvesta data yra tikrasis mano amžius.' );
define( '_US_AOUTVTEAD', 'leisti kitiems vartotojams matyti nuosavą elektroninio pašto adresą' );
define( '_US_YOUREMAIL', 'Užsiregistruotas elektroninio pašto adreas: ' );
define( '_US_SENDPASSWORD', 'Siųsti slaptažodį' );
define( '_US_LOGGEDOUT', 'Jūs esate dabar atsijungęs(-usi)' );
define('_US_THANKYOUFORVISIT','Ačiū, kad apsilankėte mūsų svetainėje!');
define('_US_INCORRECTLOGIN','Neteisingas prisiregistravimas!');
define('_US_LOGGINGU','Ačiū už prisiregistravimą, %s.');
define( '_US_REGCHECK', '<b>Ragistracijos patikrinimas:</b><br /><div><small>Įveskite ženklus, kuriuos matote paveikslėlyje.</small></div>' );
define( '_US_LOGINUSINGDETAILS', 'Įveskite savo prisijungimo detales' );
define( '_US_REMEBERME', 'Prisiminti mane' );
define( '_US_LOGINANON', 'Anonimiškai prisijungti' );
define( '_US_BROWSERCOOKIES', 'Prašome įsitikinti, kad jūsų naudojama naryklė palaiko sausainėlius (<i>cookies</i>).' );
define( '_US_LOGINNOTICE', 'Norėdami pakeisti informaciją, kurią įvedėte registravimosi metu, turite pirmiausia prisijungti su duomenimis, kuriuos nurodėte registracijos metu.' );
define( '_US_DISCLAIMER', 'Pasižadėjimas' );
define( '_US_LOGINENTER', 'Prisijungimo vardas' );
define( '_US_LOGINPASSWORD', 'Slaptažodis' );
define( '_US_LOGINBUTTON', 'Prisijungimas' );
define( '_US_LOGINDETAILS', 'Prisijungimo detalės' );
define( '_US_LANGUAGE', 'Kalba:' );
define( '_US_THEME', 'Tema:' );
// 2001-11-17 ADD
define('_US_NOACTTPADM','Pažymėtas vartotojas buvo deaktyvuotas arba dar nėra aktyvuotas.<br />Prašome susisiekti su administratoriumi dėl detalesnės informacijos.');
define('_US_ACTKEYNOT','Neteisingas aktyvavimo raktas !');
define( '_US_ACTKEYFAILED', 'Nepavyko aktyvavimas. Prašom susisiekti su svetainės prižiūrėtoju!' );
define('_US_ACONTACT','Nustatyta sąskaita jau aktyvuota!');
define('_US_ACTLOGIN','Jūsų sąskaita aktyvuota. Prašome prisijungti su registracijos slaptažodžiu.');
define('_US_NOPERMISS','Atsiprašome, jūs neturite leidimo atlikti šį veiksmą!');
define('_US_SURETODEL','Ar jūs tikrai norite ištrinti savo sąskaitą?');
define('_US_REMOVEINFO','Tai ištrins iš mūsų duomenų bazės visą jūsų informaciją.');
define('_US_BEENDELED','Jūsų sąskaita buvo panaikinta.');
// %%%%%%		File Name register.php 		%%%%%

define( '_US_IAGREE', 'Aš sutinku' );
define( '_US_UNEEDAGREE', 'Atleiskite, tačiau jūs turite sutikti su pasižadėjimu' );
define( '_US_NOREGISTER', 'Atleiskite, tačiau naujų vartotojų registracija šiuo metu yra uždrasuta.' );
define( '_US_CREATEPASSWORD', 'Sugeneruoti slaptažodį<div style="padding-top: 8px;"><span style="font-weight: normal;">Automatiškai sugeneruos slaptažodį. Kur nors užsirašykite, kad nepamirštųmėte.</span></div>' );
define( '_US_REG_FORM_HEADING', 'vartotojo registracijos forma' );
define( '_US_REG_COMPLETE', 'Registracija baigta' );
/*
* Coppa
*/
define( '_US_PLZCONTACT', 'Dėl detalesnės informacijos prašome susisiekti su: %s' );
// %s is username. This is a subject for email
define( '_US_USERKEYFOR', 'vartotojo aktyvacijos raktas %s' );
define( '_US_YOURREGISTERED', 'Ačiū %s už tai, kad užsiregistravote %s.<br /><br />Elektroniniu paštu adresu, kuris buvo nurodytas registracijos metu, buvo išsiųstas laiškas su aktyvacijos nuoroda bei instrukcija kaip ja pasinaudoti. <br /><br />
	Jūs turite pasinaudoti šia aktyvacija per 24 valandas, nes kitaip sukurtas vartotojas bus prarastas.' );
define( '_US_YOURREGMAILNG', 'Ačiū  <b>%s</b> už tai, kad užsiregistravote šioje svetainėje.<br /><br />
	Jūs esate užsiregistravęs(-usi). Visgi, deja dėl kažkokios vidinės klaidos nepavyko išsiųsti jums aktyvacijos elektroninio pašto laišką. Mes atsiprašome už sukeltus nepatogumus ir prašome susisiekti su %s dėl to, kad rankiniu būdu būtų išspręsta ši problema.' );
define( '_US_YOURREGISTERED2', 'Hi %s,<br /><br />Jūs esate jau užsiregistravęs(-usi).
	Prašome palaukti kol svetainės administratorius patvirtins jūsų praskyrą. 
	Jūs gausite elektroniniu paštu žinutę, kai tik jūsų praskyra bus aktyvuota.  Tai gali šiek tiek užtrukti, todėl prašom kantrybės.
	Jei elektroniniu paštu laiško negausite per 24 valandas, susisiekite su %s, kad būtų išspręsta ši problema.' );
// Thank you for registering, MasterIncubus. An email has been dispatched to masterincubus@gameinatrix.com with details on how to activate your account. Click here to return to where you were previously.
// You will receive an email in your inbox. You MUST follow the link in that email before you can post on these forums. Until you do that, you will be told that you do not have permission to post.
// %s is your site name
define( '_US_NEWUSERREGAT', 'Naujo vartotojo registracija %s' );
// %s is a username
define( '_US_HASJUSTREG', '%s ką tik užsiregistravo!' );
define( '_US_INVALIDMAIL', 'KLAIDA: blogas elektroninis paštas!' );
define( '_US_EMAILNOSPACES', 'KLAIDA: elektroninio pašto adresas negali turėti tarpų.' );
define( '_US_INVALIDNICKNAME', 'KLAIDA: blogas rodomasis vardas' );
define( '_US_NICKNAMETOOLONG', 'Prisijungimo arba vardas, skirtas rodymui yra per ilgas. Jis turėtų būti trumpesnis kaip %s ženklų.' );
define( '_US_NICKNAMETOOSHORT', 'Prisijungimo arba vardas, skirtas rodymui yra per trumpas. Jis turėtų būti ilgesnis kaip %s ženklų.' );
define( '_US_NAMERESERVED', 'KLAIDA: Vardas yra reservuotas.' );
define( '_US_NICKNAMENOSPACES', 'Negali būti jokių tarpų prisijungimo ir rodymo varduose.' );
define( '_US_NICKNAMETAKEN', 'KLAIDA: Rodomasis vardas jau egzistuoja.' );
define( '_US_EMAILTAKEN', 'KLAIDA: Elektroninio pašto adresas jau yra užregistruotas.' );
define( '_US_ENTERPWD', 'KLAIDA: Jūs turite nurodyti slaptažodį.' );
define( '_US_SORRYNOTFOUND', 'Atleiskite, jokio nurodyto vartotojo informacijos nepasisekė rasti.' );
define( '_US_LOGINNAMETAKEN', 'KLAIDA: Prisijungimo vardas jau yra' );
define( '_US_LOGINSAME', 'KLAIDA: Prisijungimo ir rodymo vardai turi skirtis (o dabar jie yra tokie patys)' );
define( '_US_REGFORM', 'Ar registracijos duomenys yra geri?' );
define( '_US_INVALIDLOGIN', 'Blogas prisijungimas' );
define( '_US_LOGINNOSPACES', 'KLAIDA: prisijungimo vardas negali turėti tarpų jame.' );
define( '_US_PASSNOTSAME', 'KLAIDA: Abu slaptažodžiai skiriasi. Jie turi būti identiški.' );
define( '_US_PWDTOOSHORT', 'KLAIDA: Atleiskite, jūsų slaptažodis turi būti bent <b>%s</b> ženklū ilgio.' );
// %s is your site name
define( '_US_NEWPWDREQ', '%s naujas slaptažodžio prašymas' );
define( '_US_YOURACCOUNT', 'Jūsų %s praskyra' );
define( '_US_MAILPWDNG', 'mail_password: nepavyksta atnaujinti jūsų įrašo. Prašome susisiekti su administratoriumi.' );
define( '_US_MAILERROR', 'Atleiskite, bet atrodo, mes turime problemų su elektroninio pašto laiškų siuntimu. Prašome susisiekti su administratoriumi. ' );
// %s is a username
define( '_US_PWDMAILED', '%s slaptažodis išsiųstas.' );
define( '_US_CONFMAIL', '%s patvirtinimo laiškas išsiųstas.' );
define( '_US_ACTVMAILNG', 'Nepavyko išsiųsti pranešimo laiško %s' );
define( '_US_ACTVMAILOK', 'Pranešimo laiškas %s išsiųstas.' );
// %%%%%%		File Name userinfo.php 		%%%%%
define( '_US_SELECTNG', 'joks vartotojas nepasirinktas! Prašome eiti atgal ir bandyti dar kartą.' );
//define( '_US_PROFILE_TITLE_HEADING', 'Viewing Profile: ' );
//define( '_US_EDITPROFILE', 'Account Details' );
//define( '_US_AVATAR', 'Avatar' );
//define( '_US_INBOX', 'Messages' );
/*
define( '_US_MEMBERSINCE', 'Member Since' );
define( '_US_RANK', 'Rank' );
define( '_US_POSTS', 'Comments/Posts' );
define( '_US_LASTLOGIN', 'Last Login' );
define( '_US_ALLABOUT', 'All about %s' );
define( '_US_STATISTICS', 'Statistics' );
define( '_US_MYINFO', 'My Info' );
define( '_US_BASICINFO', 'Basic information' );
define( '_US_MOREABOUT', 'More About Me' );
*/
//define( '_US_SHOWALL', 'Show All' );
//define( '_US_SENDPMTO', 'Send PM' );
//define( '_US_SENDEMAIL', 'Send Email' );
//define( '_US_ONLINE', 'Online Status' );
// %%%%%%		File Name edituser.php 		%%%%%
define( '_US_PROFILE', 'Profilis' );
define( '_US_REALNAME', 'Tikras vardas' );
define( '_US_SHOWSIG', 'Visada pasirašyti' );
define( '_US_CDISPLAYMODE', 'Komentarų rodymo režimas' );
define( '_US_CSORTORDER', 'Komentarų rūšiavimo režimas' );
define( '_US_TYPEPASSTWICE', '(įveskite du kartus slaptažodį, norėdami pakeisti)' );
define( '_US_SAVECHANGES', 'Išsaugoti pakeitimus' );
define( '_US_NOEDITRIGHT', "Atleiskite, jūs neturite teisių redaguoti šio vartotojo informaciją." );
define( '_US_PROFUPDATED', 'Jūsų profilis buvo atnaujintas!' );
define( '_US_USECOOKIE', 'Saugoti mano vartotojo vardą vienus metus' );
define( '_US_NO', 'Ne' );

define( '_US_PRESSLOGIN', 'Paspauskite žemiau esantį mygtuką tam, kad prisijungti' );
define( '_US_ADMINNO', 'Vartotojai svetainės administratorių grupėje, negali būti pašalinti' );
define( '_US_GROUPS', 'Vartotojų grupės' );

define( '_US_SUBMISSION_HEAD', 'Vartotojų pateikimas' );
define( '_US_SUBMISSION_HEAD_TEXT', 'Jei norite gali padėti šiai svetainei šiais adresais:' );

// error notices//
define( '_US_ERROR_CANNOTLOGIN', 'Jūs negalite naudoti šio prisijungimo metodo. Naudokite prisijungimo formą norėdami prisijungti.' );
define( '_US_ERROR_ALREADYLOGIN', 'Jūs esate jau prisijungęs(-usi).' );
define( '_US_ERROR_NOTLOGIN', 'Atrodo, jūs buvote neprisijungės(-usi), todėl jūsų atjungti mes negalime.' );


//Register Form
?>