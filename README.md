### Hexlet tests and linter status:
[![Actions Status](https://github.com/yulia633/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/yulia633/php-project-lvl2/actions)

## CLI Differences Files Hexlet
[![Maintainability](https://api.codeclimate.com/v1/badges/8d73837fc211fc2552d0/maintainability)](https://codeclimate.com/github/yulia633/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/8d73837fc211fc2552d0/test_coverage)](https://codeclimate.com/github/yulia633/php-project-lvl2/test_coverage)
![linter and tests](https://github.com/yulia633/php-project-lvl2/workflows/linter%20and%20tests/badge.svg)

Второй проект из четырёх, в рамках профессии PHP-программист на [Хекслет](https://ru.hexlet.io/professions/php).

#### Описание проекта
В рамках данного проекта необходимо реализовать утилиту для поиска отличий в конфигурационных файлах.

Возможности утилиты:

```
Поддержка разных форматов: json, yaml
Генерация отчетов json, plain, stylish
```

Пример использования:
-----
#### CLI приложение:  
    $ gendiff [--format <fmt>] <pathToFile1> <pathTofile2>
    
Посмотреть описание в командной строке:

    $ gendiff -h
    $ gendiff --help

#### Библиотека:
    use function Differ\Differ\genDiff;
    
    genDiff($pathToFile1, $pathTofile2, $format = 'stylish');

#### Установка
Для глобальной установки выполните команду:
`$ composer global require yulia633/hexlet-project-2`
Для установки в проект как библиотеку выполните команду:
`$ composer require yulia633/hexlet-project-2`

### Как работает пакет

#### Сравнение файлов json и yaml/yml в формате по умолчанию stylish
[![asciicast](https://asciinema.org/a/394025.svg)](https://asciinema.org/a/394025)

#### Сравнение файлов json и yaml/yml в формате stylish
[![asciicast](https://asciinema.org/a/392111.svg)](https://asciinema.org/a/392111)

#### Сравнение файлов json/yaml в формате plain 
[![asciicast](https://asciinema.org/a/392103.svg)](https://asciinema.org/a/392103)

#### Сравнение файлов json/yaml в формате json
[![asciicast](https://asciinema.org/a/392109.svg)](https://asciinema.org/a/392109)
