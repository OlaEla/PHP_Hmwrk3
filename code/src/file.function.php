<?php

// function readAllFunction(string $address) : string {

// Функция для поиска людей с сегодняшним днем рождения
function checkBirthdaysFunction(array $config): string {
    $address = $config['storage']['address'];

    if (!file_exists($address) || !is_readable($address)) {
        return handleError("Файл не существует или недоступен для чтения");
    }

    $fileContents = file($address, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $today = date('d-m'); // Текущая дата в формате ДД-ММ
    $found = false;
    $result = "";

    foreach ($fileContents as $line) {
        $data = explode(', ', $line);
        if (count($data) == 2) {
            $name = $data[0];
            $birthDate = $data[1];
            $birthDayMonth = substr($birthDate, 0, 5);

            if ($birthDayMonth === $today) {
                $result .= "$name празднует день рождения сегодня! 🎉\n";
                $found = true;
            }
        }
    }

    if (!$found) {
        $result = "Сегодня нет дней рождения.\n";
    }

    return $result;
}

// Функция для удаления строки по имени или дате
function deleteLineFunction(array $config): string {
    $address = $config['storage']['address'];

    if (!file_exists($address) || !is_readable($address)) {
        return handleError("Файл не существует или недоступен для чтения");
    }

    $fileContents = file($address, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $searchTerm = readline("Введите имя или дату для удаления: ");
    $found = false;
    $newContents = [];

    foreach ($fileContents as $line) {
        if (strpos($line, $searchTerm) === false) {
            $newContents[] = $line;
        } else {
            $found = true;
        }
    }

    if ($found) {
        file_put_contents($address, implode(PHP_EOL, $newContents) . PHP_EOL);
        return "Запись успешно удалена.\n";
    } else {
        return "Запись не найдена.\n";
    }
}

function readAllFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        
        $contents = ''; 
    
        while (!feof($file)) {
            $contents .= fread($file, 100);
        }
        
        fclose($file);
        return $contents;
    }
    else {
        return handleError("Файл не существует");
    }
}

// function addFunction(string $address) : string {
/*function addFunction(array $config) : string {
    $address = $config['storage']['address'];

    $name = readline("Введите имя: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");
    $data = $name . ", " . $date . "\r\n";

    $fileHandler = fopen($address, 'a');

    if(fwrite($fileHandler, $data)){
        return "Запись $data добавлена в файл $address"; 
    }
    else {
        return handleError("Произошла ошибка записи. Данные не сохранены");
    }

    fclose($fileHandler);
}*/

// Функция добавления записи в файл
function addFunction(array $config): string {
    $address = $config['storage']['address'];


    $name = readline("Введите имя: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");


    if (validateName($name) && validateDate($date)) {
        $data = $name . ", " . $date . "\r\n";


        $fileHandler = fopen($address, 'a');

        if (fwrite($fileHandler, $data)) {
            return "Запись \"$data\" добавлена в файл $address\n";
        } else {
            return handleError("Произошла ошибка записи. Данные не сохранены.");
        }

        fclose($fileHandler);
    } else {
        return handleError("Введена некорректная информация. Проверьте имя и дату.");
    }
}

// Функция для валидации имени
function validateName(string $name): bool {
    // Проверка, что имя не пустое и состоит только из букв, пробелов и дефисов
    if (empty(trim($name))) {
        return false; // Имя не может быть пустым
    }

    if (!preg_match("/^[а-яА-ЯёЁ\- ]+$/u", $name)) {
        return false; // Имя должно содержать только буквы, пробелы и дефис
    }

    return true;
}

// Функция для валидации даты
function validateDate(string $date): bool {
    // Разбиваем дату на день, месяц и год
    $dateBlocks = explode("-", $date);

    // Проверка формата ДД-ММ-ГГГГ
    if (count($dateBlocks) != 3 || strlen($dateBlocks[2]) != 4) {
        return false;
    }

    // Приводим части даты к числовому типу
    $day = (int)$dateBlocks[0];
    $month = (int)$dateBlocks[1];
    $year = (int)$dateBlocks[2];

    // Проверка существования даты
    if (!checkdate($month, $day, $year)) {
        return false;
    }

    // Проверка, что год не больше текущего
    if ($year > date('Y')) {
        return false;
    }

    return true;
}

// function clearFunction(string $address) : string {
function clearFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");
        
        fwrite($file, '');
        
        fclose($file);
        return "Файл очищен";
    }
    else {
        return handleError("Файл не существует");
    }
}

function helpFunction() {
    return handleHelp();
}

function readConfig(string $configAddress): array|false{
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!is_dir($profilesDirectoryAddress)){
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if(count($files) > 2){
        foreach($files as $file){
            if(in_array($file, ['.', '..']))
                continue;
            
            $result .= $file . "\r\n";
        }
    }
    else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!isset($_SERVER['argv'][2])){
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if(!file_exists($profileFileName)){
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}