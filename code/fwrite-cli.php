<?php

/*$address = '/code/birthdays.txt';

$name = readline("Введите имя: ");
$date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");

if(validate($date)){
    $data = $name . ", " . $date . "\r\n";

    $fileHandler = fopen($address, 'a');
    
    if(fwrite($fileHandler, $data)){
        echo "Запись $data добавлена в файл $address";
    }
    else {
        echo "Произошла ошибка записи. Данные не сохранены";
    }
    
    fclose($fileHandler);
}
else{
    echo "Введена некорректная информация";
}

function validate(string $date): bool {
    $dateBlocks = explode("-", $date);

    if(count($dateBlocks) < 3){
        return false;
    }

    if(isset($dateBlocks[0]) && $dateBlocks[0] > 31) {
        return false;
    }

    if(isset($dateBlocks[1]) && $dateBlocks[0] > 12) {
        return false;
    }

    if(isset($dateBlocks[2]) && $dateBlocks[2] > date('Y')) {
        return false;
    }

    return true;
}*/

/*Задача 1.
Обработка ошибок. Посмотрите на реализацию функции в
файле fwrite-cli.php в исходниках. Может ли пользователь
ввести некорректную информацию (например, дату в виде 12-50-1548)?
Какие еще некорректные данные могут быть введены?
Исправьте это, добавив соответствующие обработки ошибок.*/


/*В данном коде можно улучшить несколько моментов, чтобы предотвратить
ввод некорректной информации. Основные проблемы:

Некорректный ввод даты: Пользователь может ввести даты с неверным
форматом (например, 32-50-1548). Также есть риск ошибки при вводе несуществующих дат,
таких как 30 февраля.

Отсутствие валидации имени: Не учитывается возможность ввода пустого
имени или специальных символов.*/

/*1. Исправляем логику проверки даты:
- Используем функцию checkdate(), которая проверяет существование даты.
- Убедиться, что год не превышает текущий.
- Убедиться, что формат даты строго соответствует ДД-ММ-ГГГГ.
2. Добавить валидацию имени:
- Проверить, что имя не пустое.
- Убедиться, что имя состоит только из букв (можно разрешить пробелы, если у имени несколько частей).*/

$address = '/code/birthdays.txt';

// Чтение имени и даты рождения
$name = readline("Введите имя: ");
$date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");

// Валидация имени и даты
if (validateName($name) && validateDate($date)) {
    $data = $name . ", " . $date . "\r\n";

    // Открытие файла для записи
    $fileHandler = fopen($address, 'a');

    if (fwrite($fileHandler, $data)) {
        echo "Запись $data добавлена в файл $address";
    } else {
        echo "Произошла ошибка записи. Данные не сохранены";
    }

    fclose($fileHandler);
} else {
    echo "Введена некорректная информация. Проверьте имя и дату.";
}

// Функция для валидации имени
function validateName(string $name): bool
{
    // Проверка, что имя не пустое и состоит только из букв или пробелов
    if (empty(trim($name))) {
        return false; // Имя не может быть пустым
    }

    if (!preg_match("/^[а-яА-ЯёЁ\- ]+$/u", $name)) {
        return false; // Имя должно содержать только буквы, пробелы и дефис
    }

    return true;
}

// Функция для валидации даты
function validateDate(string $date): bool
{
    // Разбиваем дату на день, месяц и год
    $dateBlocks = explode("-", $date);

    // Проверка формата ДД-ММ-ГГГГ
    if (count($dateBlocks) != 3 || strlen($dateBlocks[2]) != 4) {
        return false; // Неверный формат
    }

    // Приводим части даты к числовому типу
    $day = (int)$dateBlocks[0];
    $month = (int)$dateBlocks[1];
    $year = (int)$dateBlocks[2];

    // Проверка существования даты
    if (!checkdate($month, $day, $year)) {
        return false; // Дата не существует
    }

    // Проверка, что год не больше текущего
    if ($year > date('Y')) {
        return false; // Год не может быть больше текущего
    }

    return true;
}
