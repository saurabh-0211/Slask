<?php
session_start();

if (!isset($_SESSION['email'])) {
  $_SESSION['msg'] = "You must log in first";
  header('location: login.php');
}
if (isset($_GET['logout'])) {
  session_destroy();
  unset($_SESSION['email']);
  header("location: login.php");
}
?>

<?php

// Connecting to the database

$errors = "";
$db = mysqli_connect('localhost','root','','slask');
$db2 = mysqli_connect('localhost','root','','slask');

if (isset($_POST['submit'])) {
  $task = $_POST['task'];

  if (isset($_SESSION['email'])){
    $email = $_SESSION['email'];
  }

  if (empty($task)) {
    $errors = "You must add new task";
  }else {
    mysqli_query($db, "INSERT INTO tasks(task, email) VALUES ('$task' , '$email') ");

    header('location: index.php');

  }

}

$email = $_SESSION['email'];

// delete the current tasks
if (isset($_GET['del_task'])) {
  $id = $_GET['del_task'];
  mysqli_query($db,"DELETE FROM tasks WHERE id=$id");
  header('location: index.php');
}

// completed tasks
if (isset($_GET['complete_task'])) {
  $id = $_GET['complete_task'];


  mysqli_query($db2, "INSERT INTO completedtask select * from tasks where email ='$email' and tasks.id = $id ");
  mysqli_query($db,"DELETE FROM tasks WHERE id=$id and email = '$email' ");
  header('location: index.php');
}



// reset the tasks
if (isset($_GET['reset_task'])) {
  $id = $_GET['reset_task'];
  mysqli_query($db,"DELETE FROM tasks where email = '$email' ");
  mysqli_query($db2,"DELETE FROM completedtask where email ='$email' ");
  header('location: index.php');
}

$tasks = mysqli_query($db, "SELECT * FROM tasks where email ='$email' ");
$completedtask = mysqli_query($db2, "SELECT * FROM completedtask where email ='$email' ");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Slask - Don't Slack the Work</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="stylesheets/main.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;800;900&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Section which contains website name & slogan -->
  <div class="d-sm-flex text-center jumbotron justify-content-center bg-primary text-white">
    <h1 class="display-2 font-weight-bold">Slask</h1>
    <h3 class="pt-1  mt-5">&nbsp; - Don't slack the task</h3>
  </div>

  <!-- notification message -->
  <?php if (isset($_SESSION['success'])) : ?>
    <div class="error success" >
      <h3>
        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
      </h3>
    </div>
  <?php endif ?>

  <!-- Header Section with Total & Completed Task -->
  <section id="main">
    <article id="headers">
      <header>
        <div>
          <h3>Total Tasks</h3>
          <?php  ($num = mysqli_num_rows($tasks)) ?>
          <h4 id="total"><?php echo $num; ?></h4>
        </div>

        <div>
          <h3>Completed Tasks</h3>
          <?php  ($num = mysqli_num_rows($completedtask)) ?>
          <h4 id="completed"><?php echo $num; ?></h4>
        </div>
      </header>
    </article>

    <!-- Form part with Add to task button -->
    <div class="container pt-5">
      <div class="row">
        <div class="col-md-10">
          <!-- form class -->
          <form class="list-group shadow-sm" action="index.php" method="post">
            <?php if (isset($errors)) { ?>
              <p class="badges"><?php echo $errors; ?></p>

            <?php } ?>
            <input type="text" name="task" class="list-group-item">
          </div>
          <div class="col-md-2 d-flex ">
            <button type="submit" class="btn btn-primary" name="submit" data-toggle="tooltip" data-placement="top" title="Add New Task">Add Task</button>
          </div>
        </form>
      </div>
    </div>

    <!-- This is the Section where table is created, which includes Task ID, Task Name & Task action -->
    <div class="container mt-4 table-responsive">
      <table class="table table-striped">
        <!-- Name of the columns in the table -->
        <thead>
          <tr>
            <th scope="col-2">SN.</th>
            <th scope="col-8">Task</th>
            <th scope="col-2">Action</th>
          </tr>
        </thead>
        <tbody>

          <!-- body which will be fetched from the database with the action buttons -->
          <?php $i =1; while ($row = mysqli_fetch_array($tasks)) { ?>
            <tr>

              <!-- Fetching the data  -->
              <th scope="row"><?php echo $i; ?></th>
              <td><?php echo $row['task']; ?></td>
              <td>

                <div class="widget-content-right">

                  <!-- check button icon -->
                  <a href="index.php?complete_task=<?php echo $row['id']; ?>">
                    <button type="submit" name="complete" class="border-0 btn-transition btn btn-outline-success" data-toggle="tooltip" data-placement="top" title="Task Completed">
                      <i class="fa fa-check"> </i>
                    </button>
                  </a>

                  <!-- trah button icon -->
                  <a href="index.php?del_task=<?php echo $row['id']; ?>">
                    <button class="border-0 btn-transition btn btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Delete Data">
                      <i class="fa fa-trash"> </i>
                    </button>
                  </a>

                </div>
              </td>
            </tr>
            <?php $i++; } ?>
          </tbody>
        </table>

        <!-- Reset Button -->
        <a href="index.php?reset_task=<?php echo $row['id']; ?>">
          <div class="btn btn-outline-danger btn-transition align-center" data-toggle="tooltip" data-placement="top" title="Reset Task">
            <h5>Reset all</h5>
          </div>
        </a>
      </div>






      <div class="mb-5"></div>

      <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    </body>
    </html>
