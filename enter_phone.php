<?php
session_start();

if(isset($_SESSION['login_log_roshkam'])) {
	#echo "true";
} else {
	$r_u = $_GET['redirect_url'];
	echo $r_u . $_SESSION['login_log_roshkam'];
	header("Location: http://base.roservers.com/authorization/login?disable_auth=1&redirect_url=http://base.roservers.com/authorization/telegram/enter_phone?redirect_url=$r_u");
}
?>
<!DOCTYPE html>
<html>
<head>
 <title>Введите номер телефона</title>
 <style>
 body {
    background: linear-gradient(to bottom right, #3498db, #2c3e50);
    font-family: Arial, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
 }
 form {
    width: 100%;
    max-width: 400px;
    background-color: #fff;
    border-radius: 25px;
    padding: 60px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.25);
 }
 input[type=text] {
         font-size: 22px;
         padding: 10px 20px;
         border-radius: 3px;
         border: 1px solid #ddd; /* добавляем серую рамку */
         margin-bottom: 10px;
         width: calc(100% - 42px); /* вычитаем размер кнопки */
         background-color: #f2f2f2;
     border-radius: 10px;
     }
     input[type=submit]{
      background-color: #3498db;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 3px;
      font-size: 22px;
     }
             h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
   text-align: center;
        }
        button[type=submit] {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 3px;
            border: none;
            margin-bottom: 10px;
            width: 100%;
            background-color: #2c3e50;
            color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
        }
            button[type=submit] {
            background-color: #2c3e50;
            color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
        }
        button[type=submit]:hover {
            background-color: #3498db;
        }
 </style>
</head>
<body>
 <form method="post" action="telegram_code.php?redirect_url=<?php echo $_GET['redirect_url']; ?>">
  <h2>Введите номер телефона:</h2>
  <input type="text" id="phone_post" name="phone_post"  placeholder="+1234567890" onchange="this.value=this.value.replace(/[^\d]/g,'')" minlength="9" maxlength="18" required>
  <button type="submit" value="Подключить">Подключить</button>
 </form>
</body>
</html>