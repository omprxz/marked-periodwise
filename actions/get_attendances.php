<?php
session_start();

if (!isset($_SESSION["userid"])) {
  header("Location: login.php");
  exit();
} else {
  $sUserId = $_SESSION["userid"];
}

require "vars.php";
require_once "conn.php";

$classId = $_GET["classId"];
$classResult = mysqli_query($conn, "SELECT session, branch FROM classes WHERE id = '$classId'");
if(mysqli_num_rows($classResult) > 0){
  $classData = mysqli_fetch_assoc($classResult);
  $userSession = $classData['session'];
  $userBranch = $classData['branch'];
} else {
  echo json_encode(['icon' => 'error', 'message' => 'Invalid Class ID', 'id' => $classId]);
  exit();
}

$attendanceDetails = [];

$getSubjects = "SELECT id, name FROM subjects WHERE c_id = '$classId'";
$subjectResult = mysqli_query($conn, $getSubjects);

if ($subjectResult && mysqli_num_rows($subjectResult) > 0) {
  while ($subjectData = mysqli_fetch_assoc($subjectResult)) {
    $subjectId = $subjectData["id"];
    $subjectName = $subjectData["name"];

    $totalPeriods = 0;
    $getRoutines = "SELECT id, day, fromTime, toTime FROM routines WHERE c_id = '$classId' AND sub_id = '$subjectId'";
    $routineResult = mysqli_query($conn, $getRoutines);

    if ($routineResult && mysqli_num_rows($routineResult) > 0) {
      while ($routineData = mysqli_fetch_assoc($routineResult)) {
        if ($routineData["day"] != 0) {
          $totalPeriods++;
        }
      }
    }

    $subjectAttendance = [];
    $getStudents = "SELECT id, name, semester, roll FROM users WHERE session = '$userSession' AND branch = '$userBranch' AND role = 'student'";
    $studentResult = mysqli_query($conn, $getStudents);

    if ($studentResult && mysqli_num_rows($studentResult) > 0) {
      while ($studentData = mysqli_fetch_assoc($studentResult)) {
        $studentId = $studentData["id"];
        $studentName = $studentData["name"];
        $semester = $studentData["semester"];
        $roll = $studentData["roll"];

        $getAttendance = "SELECT DATE_FORMAT(date, '%Y-%m-%d') AS date, TIME_FORMAT(routines.fromTime, '%h:%i %p') AS fromTime, TIME_FORMAT(routines.toTime, '%h:%i %p') AS toTime FROM attendance INNER JOIN routines ON attendance.r_id = routines.id WHERE routines.c_id = '$classId' AND routines.sub_id = '$subjectId' AND attendance.s_id = '$studentId'";
        $attendanceResult = mysqli_query($conn, $getAttendance);
        $studentAttendance = [];
        $totalPresent = 0;
        if ($attendanceResult && mysqli_num_rows($attendanceResult) > 0) {
          while ($row = mysqli_fetch_assoc($attendanceResult)) {
            $date = $row["date"];
            $fromTime = $row["fromTime"];
            $toTime = $row["toTime"];
            $studentAttendance[$date][] = [
              "title" => $subjectName,
              "timing" => "$fromTime - $toTime"
            ];
            $totalPresent++;
          }
        }

        $percentage = ($totalPeriods > 0) ? ($totalPresent / $totalPeriods) * 100 : 0;

        $subjectAttendance[] = [
          "studentName" => $studentName,
          "semester" => $semester,
          "roll" => $roll,
          "totalPresent" => $totalPresent,
          "percentage" => number_format($percentage, 2),
          "attendance" => $studentAttendance
        ];
      }
    }

    $attendanceDetails[] = [
      "subjectName" => $subjectName,
      "totalPeriods" => $totalPeriods,
      "attendance" => $subjectAttendance
    ];
  }
} else {
  echo json_encode(['icon' => 'error', 'message' => 'No subjects found for the class']);
  exit();
}

foreach ($attendanceDetails as &$subject) {
  foreach ($subject["attendance"] as &$student) {
    $mergedAttendance = [];
    foreach ($student["attendance"] as $date => $periods) {
      $mergedAttendance[] = [
        "date" => $date,
        "periods" => $periods
      ];
    }
    $student["attendance"] = $mergedAttendance;
  }
}

header('Content-Type: application/json');
echo json_encode($attendanceDetails, JSON_PRETTY_PRINT);
?>