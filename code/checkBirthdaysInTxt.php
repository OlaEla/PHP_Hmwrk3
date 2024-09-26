<?php

/*Задача 2

Поиск по файлу.
Когда мы научились сохранять в файле данные, нам может
быть интересно не только чтение,
но и поиск по нему. Например, нам надо проверить,
кого нужно поздравить сегодня с днем рождения среди пользователей,
хранящихся в формате:

Василий Васильев, 05-06-1992

И здесь нам на помощь снова приходят циклы. Понадобится цикл,
который будет построчно читать файл и искать совпадения в дате.
Для обработки строки пригодится функция explode,
а для получения текущей даты – date.*/


// Функция для поиска пользователей, у которых сегодня день рождения
function checkBirthdaysInTxt($filePath)
{
    // Устанавливаем текущую дату в формате ДД-ММ
    $today = date("d-m");

    // Проверка, существует ли файл
    if (!file_exists($filePath)) {
        echo "Файл $filePath не найден!\n";
        return;
    }

    // Открытие файла для чтения
    $fileHandler = fopen($filePath, 'r');

    // Флаг, указывающий на то, были ли найдены дни рождения
    $foundBirthday = false;

    // Чтение файла построчно
    while ($line = fgets($fileHandler)) {
        // Удаляем возможные пробелы и переносы строк
        $line = trim($line);

        // Разбиваем строку по запятой
        $data = explode(", ", $line);

        if (count($data) == 2) {
            $name = $data[0];   // Имя пользователя
            $birthdate = $data[1]; // Дата рождения

            // Извлекаем только день и месяц из даты рождения
            $birthDayMonth = substr($birthdate, 0, 5); // Первые 5 символов — день и месяц (ДД-ММ)

            // Сравниваем с сегодняшней датой
            if ($birthDayMonth == $today) {
                echo "Сегодня день рождения у: $name!\n";
                $foundBirthday = true; // Устанавливаем флаг в true, если найдено совпадение
            }
        }
    }

    // Закрытие файла
    fclose($fileHandler);

    // Если ни одного дня рождения не найдено, выводим сообщение
    if (!$foundBirthday) {
        echo "Дней рождения сегодня нет.\n";
    }
}

// Пример использования функции для TXT файла
checkBirthdaysInTxt('/code/birthdays.txt');



