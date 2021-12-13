<?php
$todoName = htmlspecialchars($_POST['todo']);
$todoName = trim($todoName);

$todoContext = htmlspecialchars($_POST['context']);
$todoContext = trim($todoContext);

$jsonArray = array();

//Если файл существует - получаем его содержимое
if (file_exists('todo.json')){
    $json = file_get_contents('todo.json');
    $jsonArray = json_decode($json, true);
}
// Делаем запись в файл
if ($todoName){
    $set = array($todoName, $todoContext);
    $jsonArray[] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: '. $_SERVER['HTTP_REFERER']);

}

// Удаление записи
$key = @$_POST['todo_name'];
if (isset($_POST['del'])){
    unset($jsonArray[$key]);
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: '. $_SERVER['HTTP_REFERER']);

}

// Редактирование
if (isset($_POST['save'])){
    $set = array(@$_POST['title'],  @$_POST['context']);   
    $jsonArray[$key] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: '. $_SERVER['HTTP_REFERER']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <title>Phones list</title>
    <style>

    </style>
</head>
<body>
<section>
    <div class="container mt-3">
        <div class="row justify-content-center">

            <div class="col-12">
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
                foreach ($jsonArray as $key => $todo): ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1 ;?></th>
                        <td><?php echo $todo[0]; ?></td>
                        <td><?php echo $todo[1]; ?></td>

                        <td>
                            <button type="submit" class="btn btn-sm btn-success" data-toggle="modal" data-target="#edit<?php echo $key;?>"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete<?php echo $key;?>"><i class="fas fa-trash-alt"></i></button>
                            <!--Modal delete-->
                            <div class="modal fade" id="delete<?php echo $key;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Вы хотите удалить запись №<?php echo $key + 1 ;?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body ml-auto">
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post">
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
                            <div class="modal fade" id="edit<?php echo $key;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                <input type="text" class="form-control" name="context" value="<?php echo $todo[1]; ?>"placeholder="Контекст" >
                                            </div>
                                            <input type="hidden" name="todo_name" value="<?php echo $key;?>">
                                            <div class="modal-footer">
                                                <button type="submit" name="save" class="btn btn-sm btn-success p-1 pt-0" data-target="#edit<?php echo $key;?>">Обновить</button>
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
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post">
                    <div class="input-group">
                    <input type="text" class="form-control" name="todo" placeholder="Телефон вводить с 79..">
                    <input type="text" class="form-control" name="context" placeholder="Контекст">
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary send" data-send="1">Создать</button>
            </div> </form>
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