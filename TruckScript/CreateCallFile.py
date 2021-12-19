import json
import os
from datetime import date

# Читаем JSON
dir_source = 'C:\Kostya\Development\Виталик-Грузовики\Truck calling\Web'
file_phones= os.path.join(dir_source, 'todo.json')
try:
    with open(file_phones, "r") as read_file:
        data = json.load(read_file)
except Exception as e:
    print("Ошибка при получении файла со списком телефонов: %s" % str(e))

#print(data["0"]["1"])

today = date.today()
for key, value in data.items():
    one_file_name = str(value['0']) + "-{}{}{}".format(today.year, today.month, today.day) + '.call'
    print(one_file_name)
