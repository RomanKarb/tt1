<?php 
$login = 'some_login';
$phone = '1234567890';
$confirm_phone = 'telegram';
$confirm_phone_code = $_GET['code'];

if(file_exists("codes_safe/$confirm_phone_code.verify")) {
    $query = "UPDATE users SET phone='$phone', confirm_phone='$confirm_phone', confirm_phone_code='$confirm_phone_code' WHERE login='$login'";
    if($mysqli->query($query) === TRUE) {
        // Изменения в БД произведены успешно
    } else {
        // Произошла ошибка при изменении значений в БД
    }
    unlink("codes_safe/$confirm_phone_code.verify"); // Удаление файла
}
?>