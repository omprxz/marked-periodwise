<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
require('actions/conn.php');

$marks_query = "SELECT * FROM marks WHERE s_id = '$sUserId'";
$marks_result = mysqli_query($conn, $marks_query);

$has_marks = mysqli_num_rows($marks_result) > 0;

$subject_query = "SELECT id, subject FROM subjects";
$subject_result = mysqli_query($conn, $subject_query);
$subjects = array();
while ($row = mysqli_fetch_assoc($subject_result)) {
    $subjects[$row['id']] = $row['subject'];
}

$user_query = "SELECT name, branch, semester, session, roll FROM users WHERE id = '$sUserId'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

$semester_ordinal = ordinal($user['semester']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" type="text/css" media="all" />
    <link href="/components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'nav.php'; ?>
    <?php 
    $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
    $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
    $role = $roleE['role'];
    if($role == 'student'){
    ?>
    <div class="container-fluid">
        <h1 class="text-center mt-3">Marks</h1>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="border p-3">
                        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                        <p><strong>Branch:</strong> <?php echo $user['branch']; ?></p>
                        <p><strong>Semester:</strong> <?php echo $semester_ordinal; ?></p>
                        <p><strong>Session:</strong> <?php echo $user['session']; ?></p>
                        <p><strong>Roll No.:</strong> <?php echo $user['roll']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="marks-div d-flex flex-column gap-3 mt-4">
            <?php if($has_marks) { ?>
                <?php
                $result_types_query = "SELECT DISTINCT resultType_id FROM marks";
                $result_types_result = mysqli_query($conn, $result_types_query);
                while ($result_type_row = mysqli_fetch_assoc($result_types_result)) {
                    $result_type_id = $result_type_row['resultType_id'];
                    $result_type_query = "SELECT type FROM result_types WHERE id = '$result_type_id'";
                    $result_type_result = mysqli_query($conn, $result_type_query);
                    $result_type_row = mysqli_fetch_assoc($result_type_result);
                    $result_type = $result_type_row['type'];
                ?>
                    <div class="marks-type-div">
                        <h4 class="text-decoration-underline">
                            <?php echo $result_type; ?>
                            <a href="actions/generate_marks_pdf.php?resultType_id=<?php echo $result_type_id; ?>&sUserId=<?php echo $sUserId; ?>" class="btn btn-sm btn-primary float-end">
                                <i class="fas fa-download"></i>
                            </a>
                        </h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Subject</th>
                                        <th scope="col">Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result_marks_query = "SELECT * FROM marks WHERE s_id = '$sUserId' AND resultType_id = '$result_type_id'";
                                    $result_marks_result = mysqli_query($conn, $result_marks_query);
                                    $total_marks = 0;
                                    while ($row = mysqli_fetch_assoc($result_marks_result)) {
                                        $subject_id = $row['subject_id'];
                                        if (isset($subjects[$subject_id]) && !empty($subjects[$subject_id])) {
                                            $subject_name = $subjects[$subject_id];
                                            $marks = is_null($row['marks'])? "-" : $row['marks'];
                                            $total_marks += is_numeric($marks) ? $marks : 0;
                                            echo "<tr>";
                                            echo "<td>$subject_name</td>";
                                            echo "<td>$marks</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong><?php echo $total_marks; ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-info mt-3">
                    No marks record found.
                </div>
            <?php } ?>
        </div>
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

    <script src="eruda.js" type="text/javascript" charset="utf-8"></script>
</body>

</html>