import json
import os
from datetime import date
import datetime
import conf
import time
from shutil import move


dir_current = os.path.dirname(__file__) + '/'
print("Current dir - " + dir_current)
path = dir_current + 'settings.ini'
dir_source = conf.get_setting(path, 'Dirs', 'dir_source')
dir_store = conf.get_setting(path, 'Dirs', 'dir_store')


if not os.path.isdir(dir_store):
    os.mkdir(dir_store)

def check_status():
    start_date = conf.get_setting(path, 'CreateCallFiles', 'Start')
    status = conf.get_setting(path, 'CreateCallFiles', 'Status')
    if status == "Off" and start_date != str(date.today()):
        return "Off"
    elif status == "Working":
        if start_date == str(date.today()):
            return "Working"
        else:
            conf.update_setting(path, 'CreateCallFiles', 'Status', "Off")
            return "Off"
    else:
        return ""


file_phones= os.path.join(dir_source, 'todo.json')
file_sch = os.path.join(dir_source, 'schedule.json')

days_of_week = {
    0: "1",
    1: "2",
    2: "3",
    3: "4",
    4: "5",
    5: "6",
    6: "7"
}

def getSchedule():
    """
    Получаем расписание из json-файла
    """
    try:
        with open(file_sch, "r") as read_file:
            data = json.load(read_file)
    except Exception as e:
        print("Ошибка при получении файла со списком телефонов: %s" % str(e))
    dayOfWeek = datetime.datetime.today().weekday()
    curhour = datetime.time(datetime.datetime.now().hour)
    schhour = datetime.time(int(data[days_of_week[dayOfWeek]][:2]))
    if schhour == curhour:
        return True
    else:
        return False



def create_call_files():
    try:
        with open(file_phones, "r") as read_file:
            data = json.load(read_file)
    except Exception as e:
        print("Ошибка при получении файла со списком телефонов: %s" % str(e))

    today = date.today()
    for key, value in data.items():
        one_file_name = str(value['0']) + "-{}{}{}".format(today.year, today.month, today.day) + '.call'
        try:
            one_file_name = os.path.join(dir_store, one_file_name)
            with open(one_file_name, "w") as file:
                if str(value['1']) == "": # Без контекста
                    file.write("Channel: local/{}@avtoobzvon\n".format(value['0']))
                    file.write("CallerID:\n")
                    file.write("MaxRetries: 1\n")
                    file.write("RetryTime: 60\n")
                    file.write("WaitTime: 300\n")
                    file.write("Context: avtoobzvon\n")
                    file.write("Extension: play\n")
                    file.write("Priority: 1")
                else:
                    file.write("Channel: local/{}@avtoobzvon-ivr\n".format(value['0']))
                    file.write("CallerID: 6122103\n")
                    file.write("MaxRetries: 0\n")
                    file.write("RetryTime: 60\n")
                    file.write("WaitTime: 300\n")
                    file.write("Setvar: dtmf={}\n".format(value['1']))
                    file.write("Setvar: CALLEE={}\n".format(value['0']))
                    file.write("Context: avtoobzvon-ivr\n")
                    file.write("Extension: play\n")
                    file.write("Priority: 1")
        except Exception as e:
            print("Ошибка при сохранении файлов в store: %s" % str(e))


def move_call_files():
    conf.update_setting(path, 'CreateCallFiles', 'Status', "Working")
    bundle = int(conf.get_setting(path, 'CreateCallFiles', 'Bundle'))
    timeout = int(conf.get_setting(path, 'CreateCallFiles', 'Timeout'))
    dir_target = conf.get_setting(path, 'Dirs', 'dir_target')
    try:
        if not os.path.isdir(dir_target):
            os.mkdir(dir_target)
        count_file = 0
        for file in os.listdir(dir_store):
            if os.path.isfile(os.path.join(dir_store, file)):
                move(os.path.join(dir_store, file), os.path.join(dir_target, file))
                count_file += 1
                if count_file == bundle:
                    time.sleep(timeout)
                    count_file = 0
    except Exception as e:
        print("Ошибка при перемещении файлов в target: %s" % str(e))
    finally:
        conf.update_setting(path, 'CreateCallFiles', 'Status', "Off")
        conf.update_setting(path, 'CreateCallFiles', 'Start', str(date.today()))


if __name__ == "__main__":
    if check_status() == "Off":
        if getSchedule():
            create_call_files()
            move_call_files()
