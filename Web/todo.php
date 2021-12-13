<?php
// Список телефонов
$todoName = htmlspecialchars($_POST['todo']);
$todoName = trim($todoName);

$todoContext = htmlspecialchars($_POST['context']);
$todoContext = trim($todoContext);

$jsonArray = array();

//Если файл существует - получаем его содержимое
if (file_exists('todo.json')) {
    $json = file_get_contents('todo.json');
    $jsonArray = json_decode($json, true);
}
// Делаем запись в файл
if ($todoName) {
    $set = array($todoName, $todoContext);
    $jsonArray[] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// Удаление записи
$key = @$_POST['todo_name'];
if (isset($_POST['del'])) {
    unset($jsonArray[$key]);
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// Редактирование
if (isset($_POST['save'])) {
    $set = array(@$_POST['title'],  @$_POST['context']);
    $jsonArray[$key] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// ----------------------------
// Расписание
// ----------------------------
$jsonsch = array();

//Если файл существует - получаем его содержимое
if (file_exists('schedule.json')) {
    $jsons = file_get_contents('schedule.json');
    $jsonsch = json_decode($jsons, true);
}

if(isset($_POST["sch"])){
     
    $sch = $_POST["sch"];
    $jsonsch["1"] = $sch[0];
    $jsonsch["2"] = $sch[1];
    $jsonsch["3"] = $sch[2];
    $jsonsch["4"] = $sch[3];
    $jsonsch["5"] = $sch[4];
    $jsonsch["6"] = $sch[5];
    $jsonsch["7"] = $sch[6];

 
    file_put_contents('schedule.json', json_encode($jsonsch, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <title>Phones list</title>
    <style>

    </style>
</head>

<body>
    <section>
        <div class="container-fluid mt-3">
            <div class="row justify-content-center">

                <div class="col-9">
                    <button class="btn btn-success mb-1" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus-circle"></i></button>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">№</th>
                                <th scope="col">Телефон</th>
                                <th scope="col">Контекст</th>
                                <th scope="col">Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($jsonArray as $key => $todo) : ?>
                                <tr>
                                    <th scope="row"><?php echo $key + 1; ?></th>
                                    <td><?php echo $todo[0]; ?></td>
                                    <td><?php echo $todo[1]; ?></td>

                                    <td>
                                        <button type="submit" class="btn btn-sm btn-success" data-toggle="modal" data-target="#edit<?php echo $key; ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete<?php echo $key; ?>"><i class="fas fa-trash-alt"></i></button>
                                        <!--Modal delete-->
                                        <div class="modal fade" id="delete<?php echo $key; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Вы хотите удалить запись №<?php echo $key + 1; ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body ml-auto">
                                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                            <div class="input-group">
                                                                <input type="hidden" name="todo_name" value="<?php echo $key; ?>">
                                                            </div>
                                                            <button class="btn btn-danger del" name="del">Удалить</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Modal delete-->
                                        <!--Modal Edit-->
                                        <div class="modal fade" id="edit<?php echo $key; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Изменить запись</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form action="" method="post" class="mt-2">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="title" value="<?php echo $todo[0]; ?>" placeholder="Телефон вводить с 79..">
                                                                <input type="text" class="form-control" name="context" value="<?php echo $todo[1]; ?>" placeholder="Контекст">
                                                            </div>
                                                            <input type="hidden" name="todo_name" value="<?php echo $key; ?>">
                                                            <div class="modal-footer">
                                                                <button type="submit" name="save" class="btn btn-sm btn-success p-1 pt-0" data-target="#edit<?php echo $key; ?>">Обновить</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Modal Edit-->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-3">
                    <button style="visibility: hidden" class="btn btn-success mb-1" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus-circle"></i></button>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <table class="table table-bordered">
                            <thead class="table-info">
                                <tr>
                                    <th scope="col">День недели</th>
                                    <th scope="col">Время запуска</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($jsonsch as $keysch => $sch) : ?>
                                    <tr>
                                        <td><?php switch ($keysch) {
                                                case "1":
                                                    echo 'Пн';
                                                    break;
                                                case "2":
                                                    echo 'Вт';
                                                    break;
                                                case "3":
                                                    echo 'Ср';
                                                    break;
                                                case "4":
                                                    echo 'Чт';
                                                    break;
                                                case "5":
                                                    echo 'Пт';
                                                    break;
                                                case "6":
                                                    echo 'Сб';
                                                    break;
                                                case "7":
                                                    echo 'Вс';
                                                    break;
                                                default:
                                                    echo "Битый файл";
                                                    break;
                                            }; ?></td>
                                        <td><input type="time" name="sch[]" id="inputMDEx1" class="form-control" value=<?php echo $sch; ?> min="00:00" max="23:59"></td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>
                        <input type="submit" class="btn btn-info" value="Сохранить расписание">
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!--Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить запись</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="input-group">
                            <input type="text" class="form-control" name="todo" placeholder="Телефон вводить с 79..">
                            <input type="text" class="form-control" name="context" placeholder="Контекст">
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary send" data-send="1">Создать</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!--Modal-->

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
    </script>
</body>

</html>