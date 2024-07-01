<?php

// Токен
  const TOKEN = '7219087234:AAGA2h4h0jVoSD8cS_JtUny2GCQSA04Bo_U';

  // ID чата
  const CHATID = '679990604';

  // Массив допустимых значений типа файла. Популярные типы файлов можно посмотреть тут: https://docs.w3cub.com/http/basics_of_http/mime_types/complete_list_of_mime_types
  $types = array('image/gif', 'image/png', 'image/jpeg', 'application/pdf');

  // Максимальный размер файла в килобайтах
  // 1048576; // 1 МБ
  $size = 1073741824; // 1 ГБ

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $fileSendStatus = '';
  $textSendStatus = '';
  $msgs = [];
  
  // Проверяем не пусты ли поля
  if (!empty($_POST['type']) && !empty($_POST['brand']) && !empty($_POST['price']) && !empty($_POST['phone']) && !empty($_POST['model']) && !empty($_POST['state']) && !empty($_POST['age-size'])) {
    
    // Если не пустые, то валидируем эти поля и сохраняем и добавляем в тело сообщения.
    $txt = "";

        // Вид товара
        if (isset($_POST['theme']) && !empty($_POST['theme'])) {
          $txt .= "Товар: " . strip_tags(trim(urlencode($_POST['theme']))) . "%0A";
      }

      // Вид экипировки
      if (isset($_POST['type']) && !empty($_POST['type'])) {
        $txt .= "Вид экипировки: " . strip_tags(trim(urlencode($_POST['type']))) . "%0A";
    }

    // Вид экипировки
    if (isset($_POST['brand']) && !empty($_POST['brand'])) {
      $txt .= "Бренд: " . strip_tags(trim(urlencode($_POST['brand']))) . "%0A";
  }

    // Модель
    if (isset($_POST['model']) && !empty($_POST['model'])) {
      $txt .= "Модель: " . strip_tags(trim(urlencode($_POST['model']))) . "%0A";
  }

    // Характеристики
    if (isset($_POST['state']) && !empty($_POST['state'])) {
      $txt .= "Состояние: " . strip_tags(trim(urlencode($_POST['state']))) . "%0A";
  }

  // Характеристики
  if (isset($_POST['age-size']) && !empty($_POST['age-size'])) {
    $txt .= "Возраст: " . strip_tags(trim(urlencode($_POST['age-size']))) . "%0A";
  }

  // Характеристики
  if (isset($_POST['char-stick-bend']) && !empty($_POST['char-stick-bend'])) {
    $txt .= "Сторона загиба крюка: " . strip_tags(trim(urlencode($_POST['char-stick-bend']))) . "%0A";
  }

  // Характеристики
  if (isset($_POST['char-stick-n']) && !empty($_POST['char-stick-n'])) {
    $txt .= "Номер загиба крюка: " . strip_tags(trim(urlencode($_POST['char-stick-n']))) . "%0A";
  }

  // Характеристики
  if (isset($_POST['stick-flex']) && !empty($_POST['stick-flex'])) {
    $txt .= "Flex: " . strip_tags(trim(urlencode($_POST['stick-flex']))) . "%0A";
  }

      // Цена
      if (isset($_POST['price']) && !empty($_POST['price'])) {
        $txt .= "Цена: " . strip_tags(trim(urlencode($_POST['price']))) . "%0A";
  }

    // Город
    if (isset($_POST['location']) && !empty($_POST['location'])) {
      $txt .= "Город: " . strip_tags(trim(urlencode($_POST['location']))) . "%0A";
  }

    // Номер телефона
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $txt .= "Номер телефона: " . strip_tags(trim(urlencode($_POST['phone']))) . "%0A";
  }

    // Описание
  if (isset($_POST['description']) && !empty($_POST['description'])) {
    $txt .= "Описание: " . strip_tags(trim(urlencode($_POST['description']))) . "%0A";
  }

    $textSendStatus = @file_get_contents('https://api.telegram.org/bot'. TOKEN .'/sendMessage?chat_id=' . CHATID . '&parse_mode=html&text=' . $txt); 

    if( isset(json_decode($textSendStatus)->{'ok'}) && json_decode($textSendStatus)->{'ok'} ) {
      if (!empty($_FILES['files']['tmp_name'])) {
    
          $urlFile =  "https://api.telegram.org/bot" . TOKEN . "/sendMediaGroup";
          
          // Путь загрузки файлов
          $path = $_SERVER['DOCUMENT_ROOT'] . '/telegramform/tmp/';
          
          // Загрузка файла и вывод сообщения
          $mediaData = [];
          $postContent = [
            'chat_id' => CHATID,
          ];
      
          for ($ct = 0; $ct < count($_FILES['files']['tmp_name']); $ct++) {
            if ($_FILES['files']['name'][$ct] && @copy($_FILES['files']['tmp_name'][$ct], $path . $_FILES['files']['name'][$ct])) {
              if ($_FILES['files']['size'][$ct] < $size && in_array($_FILES['files']['type'][$ct], $types)) {
                $filePath = $path . $_FILES['files']['name'][$ct];
                $postContent[$_FILES['files']['name'][$ct]] = new CURLFile(realpath($filePath));
                $mediaData[] = ['type' => 'document', 'media' => 'attach://'. $_FILES['files']['name'][$ct]];
              }
            }
          }
      
          $postContent['media'] = json_encode($mediaData);
      
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
          curl_setopt($curl, CURLOPT_URL, $urlFile);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $postContent);
          $fileSendStatus = curl_exec($curl);
          curl_close($curl);
          $files = glob($path.'*');
          foreach($files as $file){
            if(is_file($file))
              unlink($file);
          }
      }
      echo json_encode('SUCCESS');
    } else {
      echo json_encode('ERROR');
      // 
      // echo json_decode($textSendStatus);
    }
  } else {
    echo json_encode('NOTVALID');
  }
} else {
  header("Location: /");
}
