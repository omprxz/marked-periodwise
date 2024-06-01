<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
require('actions/vars.php');
require_once 'actions/conn.php';

$userQuery = "SELECT session, branch FROM users WHERE id = '$sUserId'";
$userResult = mysqli_query($conn, $userQuery);

if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $userSession = $userData['session'];
    $userBranch = $userData['branch'];

    $classQuery = "SELECT id FROM classes WHERE session = '$userSession' AND branch = '$userBranch'";
    $classResult = mysqli_query($conn, $classQuery);

    if ($classResult && mysqli_num_rows($classResult) > 0) {
        $classList = [];
        while ($classData = mysqli_fetch_assoc($classResult)) {
            $classId = $classData['id'];
            $subjectQuery = "SELECT id, name FROM subjects WHERE c_id = '$classId'";
            $subjectResult = mysqli_query($conn, $subjectQuery);
            while ($subjectData = mysqli_fetch_assoc($subjectResult)) {
                $subjectId = $subjectData['id'];
                $subjectName = $subjectData['name'];
                
                $classList[$subjectId] = $subjectName;
            }
        }
    } else {
        $classList = [];
    }
} else {
    $userSession = $userBranch = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="/components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Roboto';
        }

        .form-check-inline {
            display: inline-block;
            margin-right: 1rem;
        }

        .form-check-inline:nth-child(3n) {
            margin-right: 0;
        }

        .subject-container {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <?php 
  $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
  $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
  $role = $roleE['role'];
  if($role == 'student'){
  ?>
    <div class="container position-relative">
        <div class="position-fixed vh-100 vw-100 bg-dark start-0 top-0 opacity-50 justify-content-center align-items-center d-none" id="loadingScreen">
        <div class="spinner-grow text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
        <h1 class="text-center my-2 fw-semibold">Attendance</h1>
        <div class="mb-3">
            <label for="subjects" class="form-label">Select Subjects:</label>
            <div class="checkbox-container d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="all" id="selectAll" checked> <!-- Add checked attribute here -->
                    <label class="form-check-label" for="selectAll">
                        All
                    </label>
                </div>
                <?php foreach ($classList as $subjectId => $subjectName) : ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input subject-checkbox" type="checkbox" name="subjects[]" value="<?php echo $subjectId; ?>" id="subject<?php echo $subjectId; ?>" checked> <!-- Add checked attribute here -->
                        <label class="form-check-label" for="subject<?php echo $subjectId; ?>">
                            <?php echo $subjectName; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="attendance"></div>
    </div>
    <?php
} else {
?>
<div class="alert alert-danger m-4">
  This page is only for students.
</div>
<?php
}
?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#selectAll').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.subject-checkbox').prop('checked', isChecked);
            if (isChecked) {
                var allSubjects = $('.subject-checkbox').map(function() {
                    return $(this).val();
                }).get();
                getAttendance(allSubjects);
            } else {
                $('#attendance').html('<div class="alert alert-warning">No records.</div>');
            }
        });

        $('.subject-checkbox').on('change', function() {
            var selectedSubjects = $('.subject-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if ($('#selectAll').is(':checked')) {
                $('#selectAll').prop('checked', false);
            }

            if (selectedSubjects.length === $('.subject-checkbox').length) {
                $('#selectAll').prop('checked', true);
            }

            if (selectedSubjects.length > 0) {
                getAttendance(selectedSubjects);
            } else {
                $('#attendance').html('<div class="alert alert-warning">No records.</div>');
            }
        });

        function getAttendance(subjects) {
            $.ajax({
                url: 'actions/get_attendance.php',
                type: 'POST',
                data: {
                    subjects: subjects
                },
                beforeSend: function(){
                  $('#loadingScreen').removeClass('d-none')
                  $('#loadingScreen').addClass('d-flex')
                },
                success: function(response) {
                    $('#attendance').html(response);
                    $('#loadingScreen').removeClass('d-flex')
                    $('#loadingScreen').addClass('d-none')
                },
                error: function(xhr, status, error) {
                  $('#loadingScreen').removeClass('d-flex')
                  $('#loadingScreen').addClass('d-none')
                    console.error(error);
                },
                complete: function(){
                  $('#loadingScreen').removeClass('d-flex')
                  $('#loadingScreen').addClass('d-none')
                }
            });
        }
        getAttendance(['all'])
    });
</script>
    <script src="eruda.js" type="text/javascript" charset="utf-8"></script>
</body>

</html>