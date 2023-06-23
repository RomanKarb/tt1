<!DOCTYPE html>
<html>
<head>
 <title>Оформление формы</title>
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
            width: 40%;
            background-color: #2c3e50;
            color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
        }
		        .submit {
            background-color: #2c3e50;
            color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
        }
        .submit:hover {
            background-color: #3498db;
        }
  .cancel-btn {
   font-size: 16px;
   padding: 10px 40px;
            border-radius: 3px;
            border: none;
            margin-bottom: 10px;
   background-color: grey;
   color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
  }
  .cancel-btn:hover {
   background-color: darkred;
  }
    .button-wrapper {
    display: flex;
    justify-content: space-between;
  }
 </style>
   <script>
    function copyToClipboard(element) {
      element.select();
      document.execCommand("copy");
    }
  </script>
</head>
<body>
 <?php
 
  // Подключение к базе данных
  $conn = mysqli_connect('localhost', 'root', '', 'LocalUsersTest');

  // Запуск сессии

  session_start();
  
  if(isset($_GET['redirect_url'])) {
    if(isset($_GET['redirect_url']) && $_GET['redirect_url'] != "") {
  $redirect_url = $_GET['redirect_url'];
  $_SESSION['redirect_urltg'] = $redirect_url;
    } else {
      $redirect_url = "/authorization/login";
      $_SESSION['redirect_urltg'] = "/authorization/login";
    }
  
  if(isset($_SESSION['login_log_roshkam'])) {
	  
	  
	    if(isset($_SESSION['phone_post'])) {
      $phone_post = $_SESSION['phone_post'];
  } else {
	  if(isset($_POST['phone_post'])) {
  $phone_post = $_POST['phone_post'];
  $_SESSION['phone_post'] = $phone_post;
  } else {
	  header("Location: enter_phone?redirect_url=$redirect_url");
  }
  }
	  
  // Генерация уникального кода

  if(isset($_SESSION['code_tg'])) {
	  $unique_code = $_SESSION['code_tg'];
  } else {
  $unique_code = uniqid();
  $_SESSION['code_tg'] = $unique_code;
  }

  // Запись уникального кода в базу данных

  $login = $_SESSION['login_log_roshkam'];
  $query = "UPDATE users SET confirm_phone_code='$unique_code' WHERE username='$login'";
  mysqli_query($conn, $query);

  // Создание ссылки с уникальным кодом
  $link = "https://t.me/confirm_roauth_bot?start=$unique_code";

  // Обработка нажатия кнопки "Открыть бота"

  if (isset($_POST['open_bot'])) {
   echo "<script>window.open('$link', '_blank')</script>";
  }

  // Обработка нажатия кнопки "Проверить"

if (isset($_POST['check'])) {
    $unique_code_post = $_POST['unique_code_post'];
    $verify_file = "codes_safe/$unique_code_post.verify";
    if (file_exists($verify_file)) {
        $code_file = file_get_contents($verify_file);
        $phone_pattern = "/phone=(\d+)/";
        preg_match($phone_pattern, $code_file, $matches);
        if ($phone_post == $matches[1]) {
            $query = "UPDATE users SET phone='$phone', confirm_phone='telegram', confirm_phone_code='$unique_code' WHERE username='$login'";
            mysqli_query($conn, $query);
            unset($_SESSION['code_tg']);
            $redirect_url = $_SESSION['redirect_urltg'];
            unset($_SESSION['redirect_urltg']);
            echo $redirect_url;
            echo $phone_post;
			$login = $_SESSION['login_log_roshkam'];
			$phone_post = $_SESSION['phone_post'];
			$conn = mysqli_connect('localhost', 'root', '', 'LocalUsersTest');
			$query = "UPDATE users SET phone='$phone_post' WHERE username='$login'";
            mysqli_query($conn, $query);
			unset($_SESSION['phone_post']);
			
            $contents = file_get_contents($verify_file);
            $start = "chat.id=";
            $end = ";c.i";
            $chat_id = substr($contents, strpos($contents, $start) + strlen($start), strpos($contents, $end) - strpos($contents, $start) - strlen($start));
			$conn1 = mysqli_connect('localhost', 'root', '', 'LocalUsersTest');
			$query1 = "UPDATE users SET telegram_chat_id='$chat_id' WHERE username='$login'";
            mysqli_query($conn1, $query1);

            unlink("codes_safe/$unique_code_post.verify");
			unset($_SESSION['phone_post']);
      unset($_SESSION['code_tg']);
			echo $chat_id;
      sleep(1); // ждем 1 секунду
			header("Location: $redirect_url");
        } else {
			unlink("codes_safe/$unique_code_post.verify");
			unset($_SESSION['phone_post']);
      unset($_SESSION['code_tg']);
            header("Location: http://base.roservers.com/authorization/messages.php?message=Несоответствие номеров \"$phone_post\" и \"$matches[1]\"&color=red&color_form=white&title=Несоответствие номера");
        }
   } else {
    echo "<p>Код подтверждения еще не был отправлен в telegram.</p>";
	unlink("codes_safe/$unique_code_post.verify");
	header('Location: http://base.roservers.com/authorization/messages.php?message=Вы не прошли авторизацию в Telegram вернитесь назад&color=red&color_form=white&title=Вы не прошли авторизацию в Telegram');
   }
  }
  } else {
  header("Location: http://base.roservers.com/authorization/login?redirect_url=http://base.roservers.com/authorization/telegram/telegram_code?redirect_url=$redirect_url");
  } 
  } else {
	  header('Location: http://base.roservers.com/authorization/messages.php?message=Отсутствует Redirect Url&color=red&color_form=white&title=Отсутствует Redirect Url');
  }
  
 ?>
<form id="myForm" method="post" action="">
    <input type="hidden" name="unique_code_post" value="<?php echo $unique_code; ?>">
  <input type="hidden" name="phone_post" value="<?php echo $phone; ?>">
    <h2>Нажмите "Открыть бота" или скопируйте адрес в строку поиска для авторизации через Telegram:</h2>
	<input <?php echo $link ?> readonly onclick="copyToClipboard(this)" type="text" value="<?php echo $link ?>">
    <h2 hidden><?php echo $link; ?></h2>
  <div class="button-wrapper">
    <button type="submit" name="open_bot" class="button">Открыть бота</button>
    <button type="submit" name="check" class="button">Проверить</button>
  </div>
</form>
<script>
    // Отправка формы при нажатии на кнопку "Проверить"
    document.querySelector('button[name="check"]').addEventListener('click', function() {
        document.querySelector('#myForm').submit();
    });
</script>
</body>
</html>