<?php
session_start();
session_regenerate_id();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'phones.php';


if ((!isset($_SESSION['user1'])) && ($_GET['mode'] != "login")) // if there is no valid session
{
    //header("Location: sms-kos.php?mode=login");
    header("Location: https://$host$uri/$extra?mode=login");
}
if (isset($_GET['mode']))
{
    $mode = $_GET['mode'];
}
else
{
    $mode = "main";
}

switch ($mode)
{
    case "login":
    {
        function auth($username, $password)
        {
            
            //user => password
            $users = array(
                'admin' => 'admin'
            );
            //---------------------
            
            if (!isset($users[$username]) or $users[$username] != $password)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        function renderForm()
        {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head><title>�������</title>
<link rel="stylesheet" type="text/css" href="styles.css">
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

</head><body class="text-center" style="align-items: center;justify-content: center;background-color: #f5f5f5;">
                               
                                <form class="mb-3" id="newrecord" action="" method="post">
                                <div class="center-wrapper">
                                <div class="form-label"><h2>����</h2></div>
                                <div class="form-label">������������: </div> <input class="form-control" type="text" name="username" value="" /><br/>
                                <div class="form-label">������: </div> <input class="form-control" type="password" name="password" value="" /><br/>
                                <div class="submit-container">
                                <input type="submit" class="btn btn-primary" name="login" value="��">
                                </div>
                                </div>
                                </form>


                                <form>



<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        </body>

<?php
        }
        session_start();
        if (isset($_POST['login']))
        {
            if (isset($_POST['username']) && isset($_POST['password']))
            {
                if (auth($_POST['username'], $_POST['password']))
                {
                    // auth okay, setup session
                    $_SESSION['user1'] = $_POST['username'];
                    // redirect to required page
                    //header("Location: sms-kos.php?mode=main&action=view");
                    header("Location: https://$host$uri/$extra?mode=main");
                }
                else
                {
                    // didn't auth go back to loginform
                    //header("Location: sms-kos.php?mode=login");
                    header("Location: https://$host$uri/$extra?mode=login");
                    
                }
            }
            else
            {
                header("Location: https://$host$uri/$extra?mode=login");
            }
        }
        else
        {
            renderForm();
        }
        break;
        
    } 
    case "main": 
    {

// ������ ���������
$todoName = htmlspecialchars($_POST['todo']);
$todoName = trim($todoName);

$todoContext = htmlspecialchars($_POST['context']);
$todoContext = trim($todoContext);

$jsonArray = array();

//���� ���� ���������� - �������� ��� ����������
if (file_exists('todo.json')) {
    $json = file_get_contents('todo.json');
    $jsonArray = json_decode($json, true);
}
// ������ ������ � ����
if ($todoName) {
    $set = array($todoName, $todoContext);
    $jsonArray[] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// �������� ������
$key = @$_POST['todo_name'];
if (isset($_POST['del'])) {
    unset($jsonArray[$key]);
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// ��������������
if (isset($_POST['save'])) {
    $set = array(@$_POST['title'],  @$_POST['context']);
    $jsonArray[$key] = $set;
    file_put_contents('todo.json', json_encode($jsonArray, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK));
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

// ----------------------------
// ����������
// ----------------------------
$jsonsch = array();

//���� ���� ���������� - �������� ��� ����������
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
    <title>������ ���������</title>
    <style>

    </style>
</head>

<body style="background-color: #f5f5f5;">
    <section>
        <div class="container mt-3">
            <div class="row justify-content-center">

                <div class="col-9">
                    <button class="btn btn-outline-success mb-1" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus-circle"></i></button>
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 10%">�</th>
                                <th scope="col" style="width: 50%">�������</th>
                                <th scope="col"style="width: 10%">����������</th>
                                <th scope="col" style="width: 20%">��������</th>
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
                                        <button type="submit" class="btn btn-sm btn-light" data-toggle="modal" data-target="#edit<?php echo $key; ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#delete<?php echo $key; ?>"><i class="fas fa-trash-alt"></i></button>
                                        <!--Modal delete-->
                                        <div class="modal fade" id="delete<?php echo $key; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">�� ������ ������� ������ �<?php echo $key + 1; ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body ml-auto">
                                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                            <div class="input-group">
                                                                <input type="hidden" name="todo_name" value="<?php echo $key; ?>">
                                                            </div>
                                                            <button class="btn btn-danger del" name="del">�������</button>
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
                                                        <h5 class="modal-title" id="exampleModalLabel">�������� ������</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form action="" method="post" class="mt-2">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="title" value="<?php echo $todo[0]; ?>" placeholder="������� ������� � 89..">
                                                                <input type="text" class="form-control" name="context" value="<?php echo $todo[1]; ?>" placeholder="����������">
                                                            </div>
                                                            <input type="hidden" name="todo_name" value="<?php echo $key; ?>">
                                                            <div class="modal-footer">
                                                                <button type="submit" name="save" class="btn btn-sm btn-success p-1 pt-0" data-target="#edit<?php echo $key; ?>">��������</button>
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
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">���� ������</th>
                                    <th scope="col">����� �������</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($jsonsch as $keysch => $sch) : ?>
                                    <tr>
                                        <td><?php switch ($keysch) {
                                                case "1":
                                                    echo '��';
                                                    break;
                                                case "2":
                                                    echo '��';
                                                    break;
                                                case "3":
                                                    echo '��';
                                                    break;
                                                case "4":
                                                    echo '��';
                                                    break;
                                                case "5":
                                                    echo '��';
                                                    break;
                                                case "6":
                                                    echo '��';
                                                    break;
                                                case "7":
                                                    echo '��';
                                                    break;
                                                default:
                                                    echo "����� ����";
                                                    break;
                                            }; ?></td>
                                        <td><input style="background-color: #f5f5f5;" type="time" name="sch[]" id="inputMDEx1" class="form-control form-control-sm" value=<?php echo $sch; ?> min="00:00" max="23:59"></td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>
                        <input type="submit" class="btn btn-outline-success " value="��������� ����������">
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
                    <h5 class="modal-title" id="exampleModalLabel">�������� ������</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="input-group">
                            <input type="text" class="form-control" name="todo" placeholder="������� ������� � 89..">
                            <input type="text" class="form-control" name="context" placeholder="����������">
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary send" data-send="1">�������</button>
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
<?php 
break;
}
}
?>

