# Виталик: Обзвон грузовиков

## Задачи
1. Ввод информации через веб-интерфейс
    * Создание списка номеров телефонов и поля контекст (todo.json) , начинающихся с 89..; редактирование и удаление элементов в списке
    * Изменение и сохранение расписания запуска (schedule.json) в формате
    ```
        Пн: 09-00
        Вт: 09-00
        Ср: 09-00
        Чт: 09-00
        Пт: 10:36
        Сб: 13:17
        Вс: 00:00 - не запускается
    ```

2. Автоматическое формирование файлов для Астериска, позволяющее обзванивать телефоны. 
* Формат обычных файлов:
```
Channel: local/89161111111@avtoobzvon
CallerID:
MaxRetries: 1
RetryTime: 60
WaitTime: 300
Context: avtoobzvon
Extension: play
Priority: 1
```

* Формат файлов с добавочным номером:
```
Channel: local/89502222222@avtoobzvon-ivr
CallerID: 6122103
MaxRetries: 0
RetryTime: 60
WaitTime: 300
Setvar: dtmf=1
Setvar: CALLEE=89502222222
Context: avtoobzvon-ivr
Extension: play
Priority: 1
```
* Файлы должны копироваться в директорию астериска пачками с задержками между пачек

## Файл настроек
```
[Dirs]
; Директория с веб-страницей, куда сохраняются todo.json и schedule.json . Должна быть создана
dir_source = Z:\home\localhost\www\

; Временная директория для хранения call-файлов. Должна быть создана
dir_store = store

; Директория, откуда Астериск берет call-файлы
dir_target = target

; Директория сохранения логов работы программы. Должна быть создана
dir_logs = log

; Имя файла со списком телефонов
file_phones = todo.json

; Имя файла с расписание запуска. 
file_sch = schedule.json

[CreateCallFiles]
; Off - Программа не запущена. Ещё не отработала сегодня или уже отработала. 
status = Off

; Дата последней отработки программы. Если = сегодня, то сегодня запуск уже был и сегодня его уже не будет. 
start = 2022-01-24

; Размер пачки
bundle = 2

; Задержка в секундах между пачками
timeout = 10
```

## Ссылки, которые помогли 
* [Модуль Logging](https://webdevblog.ru/logging-v-python/)
* [Хоткеи mc](https://scabere.livejournal.com/83643.html)
* [Установка Python 3.6 из исходников](https://adminwin.ru/ustanovka-python-3-6-na-centos/)
* [Попытка замены зеркал при ошибке: Cannot find a valid baseurl в CentOS 6](https://xost.su/support/cannot-find-a-valid-baseurl-centos-6)
* [Хоткеи mcedit](https://any-key.net/mcedit-hotkeys/)
* [CRON](https://www.digitalocean.com/community/tutorials/how-to-use-cron-to-automate-tasks-centos-8-ru)
* [Ещё CRON](https://blog.sedicomm.com/2017/07/24/kak-dobavit-zadanie-v-planirovshhik-cron-v-linux-unix/)
* [vi](https://docs.altlinux.org/ru-RU/archive/2.3/html-single/junior/alt-docs-extras-linuxnovice/ch02s10.html)