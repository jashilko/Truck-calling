import json
import os
from datetime import date
import datetime
import conf
import time
from shutil import move
import logging



dir_current = os.path.dirname(__file__) + '/'
#dir_current = '/home/Track-calling/Truck-calling/TruckScript/'

print("Current dir - " + dir_current)
path = dir_current + 'settings.ini'
dir_source = conf.get_setting(path, 'Dirs', 'dir_source')
dir_store = conf.get_setting(path, 'Dirs', 'dir_store')


date_log = datetime.datetime.now().strftime("%Y-%m-%d")
#logmode = conf.get_setting(path, 'General', 'logmode')
logmode = 'INFO'
if logmode == 'INFO':
    logging.basicConfig(filename = dir_current + "log/" + date_log + "-log.txt", level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
elif logmode == 'DEBUG':
    logging.basicConfig(filename=dir_current + "log/" + date_log + "-log.txt", level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')
else:
    logging.basicConfig(filename=+ "log/" + date_log + "-log.txt", level=logging.ERROR, format='%(asctime)s - %(levelname)s - %(message)s')



if not os.path.isdir(dir_store):
    os.mkdir(dir_store)

def check_status():
    '''
    Проверка статуса был ли сегодня запуск
    '''
    start_date = conf.get_setting(path, 'CreateCallFiles', 'Start')
    status = conf.get_setting(path, 'CreateCallFiles', 'Status')
    logging.debug('Start date in config - {}'.format(start_date))
    logging.debug('Status in config - {}'.format(status))

    if status == "Off" and start_date != str(date.today()):
        logging.debug('Program didnt start today')
        return "Off"
    elif status == "Working":
        if start_date == str(date.today()):
            logging.debug('Program is already running...')
            return "Working"
        else:
            conf.update_setting(path, 'CreateCallFiles', 'Status', "Off")
            logging.error('Program was breaking {}. Now lets start..'.format(start_date))
            return "Off"
    else:
        logging.debug('Program finished working today')
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
    :return True - можно запускать создание файлов, False - нельзя
    """
    try:
        with open(file_sch, "r") as read_file:
            data = json.load(read_file)
            dayOfWeek = datetime.datetime.today().weekday()
            curhour = datetime.time(datetime.datetime.now().hour)
            schhour = datetime.time(int(data[days_of_week[dayOfWeek]][:2]))
            logging.debug('Time in schedule - ' + data[days_of_week[dayOfWeek]])
            if data[days_of_week[dayOfWeek]][3:5] == "00" and data[days_of_week[dayOfWeek]][:2] == "00":
                logging.info('The time is 00:00. No work today')
                return False
            if schhour == curhour:
                logging.info('Time to work now. Schedule time: {}'.format(data[days_of_week[dayOfWeek]]))
                return True
            else:
                logging.debug('Now: {} , Schedule Time: {}'.format(str(datetime.datetime.now()), data[days_of_week[dayOfWeek]]))
                return False
    except Exception as e:
        logging.error("Error with get json-schedule: %s" % str(e))
        #print("Ошибка при получении json-файла расписания: %s" % str(e))


def create_call_files():
    '''
    Создаем файлы из json-списка телефонов

    '''
    try:
        with open(file_phones, "r") as read_file:
            data = json.load(read_file)
    except Exception as e:
        logging.error("Error with get json phones list: %s" % str(e))
        #print("Ошибка при получении json-файла со списком телефонов: %s" % str(e))

    count_standart = 0
    count_context = 0
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
                    count_standart += 1
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
                    count_context += 1
        except Exception as e:
            logging.error("Error with saving files in store dir: %s" % str(e))
            #print("Ошибка при сохранении файлов в store: %s" % str(e))
    logging.info("Created files: Standart - {}, Context - {}".format(count_standart, count_context))

def move_call_files():
    conf.update_setting(path, 'CreateCallFiles', 'Status', "Working")
    bundle = int(conf.get_setting(path, 'CreateCallFiles', 'Bundle'))
    timeout = int(conf.get_setting(path, 'CreateCallFiles', 'Timeout'))
    dir_target = conf.get_setting(path, 'Dirs', 'dir_target')
    count_for_logs = 0
    try:
        if not os.path.isdir(dir_target):
            os.mkdir(dir_target)
        count_file = 0
        for file in os.listdir(dir_store):
            if os.path.isfile(os.path.join(dir_store, file)):
                move(os.path.join(dir_store, file), os.path.join(dir_target, file))
                count_for_logs += 1
                count_file += 1
                if count_file == bundle:
                    time.sleep(timeout)
                    count_file = 0
        logging.info("Moved {} files".format(count_for_logs))
    except Exception as e:
        logging.error("Error moving files in target: %s" % str(e))
        #print("Error moving files in target: %s" % str(e))
    finally:
        conf.update_setting(path, 'CreateCallFiles', 'Status', "Off")
        conf.update_setting(path, 'CreateCallFiles', 'Start', str(date.today()))


if __name__ == "__main__":
    logging.debug("-")
    logging.debug("Start work")
    if check_status() == "Off":
        if getSchedule():
            create_call_files()
            move_call_files()
