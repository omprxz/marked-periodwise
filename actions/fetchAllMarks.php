<?php
require 'conn.php';
  $role = $userSql['role'];
  if($role == 'faculty'){
$data = array();

$query = "SELECT DISTINCT resT_id FROM marks";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $resultType = $row['resT_id'];
    
    $resultTypeNameQuery = "SELECT type FROM result_types WHERE id = '$resultType'";
    $resultTypeNameResult = mysqli_query($conn, $resultTypeNameQuery);
    $resultTypeNameRow = mysqli_fetch_assoc($resultTypeNameResult);
    $resultTypeName = $resultTypeNameRow['type'];

    $resultTypeData = array(
        'resultType' => $resultTypeName,
        'resultTypeId' => $resultType,
        'details' => array()
    );

    $subjectsQuery = "SELECT DISTINCT sub_id FROM marks WHERE resT_id = '$resultType'";
    $subjectsResult = mysqli_query($conn, $subjectsQuery);
    $subjectIds = array();
    $subjectNames = array();
    while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
        $subjectId = $subjectRow['sub_id'];
        $subjectNameQuery = "SELECT name FROM subjects WHERE id = '$subjectId'";
        $subjectNameResult = mysqli_query($conn, $subjectNameQuery);
        $subjectNameRow = mysqli_fetch_assoc($subjectNameResult);
        if ($subjectNameRow && !empty($subjectNameRow['name'])) {
            $subjectIds[] = $subjectId;
            $subjectNames[$subjectId] = $subjectNameRow['name'];
        }
    }

    $marksQuery = "SELECT * FROM marks WHERE resT_id = '$resultType'";
    $marksResult = mysqli_query($conn, $marksQuery);

    $studentMarks = array();
    while ($marksRow = mysqli_fetch_assoc($marksResult)) {
        $studentId = $marksRow['s_id'];
        $subjectId = $marksRow['sub_id'];

        $studentDetailsQuery = "SELECT name, semester, session, branch, roll FROM users WHERE id = '$studentId'";
        $studentDetailsResult = mysqli_query($conn, $studentDetailsQuery);
        $studentDetailsRow = mysqli_fetch_assoc($studentDetailsResult);

        if (!isset($studentMarks[$studentId])) {
            $studentMarks[$studentId] = array(
                'name' => $studentDetailsRow['name'],
                'semester' => $studentDetailsRow['semester'],
                'session' => $studentDetailsRow['session'],
                'branch' => $studentDetailsRow['branch'],
                'roll' => $studentDetailsRow['roll'],
                'marks' => array(),
                'Total Marks' => 0 // Updated column name to "Total Marks"
            );

            foreach ($subjectIds as $subId) {
                $studentMarks[$studentId]['marks'][$subjectNames[$subId]] = "-";
            }
        }

        if (isset($subjectNames[$subjectId])) {
            $studentMarks[$studentId]['marks'][$subjectNames[$subjectId]] = $marksRow['marks'];
            $studentMarks[$studentId]['Total Marks'] += $marksRow['marks']; // Updated column name to "Total Marks"
        }
    }

    $resultTypeData['details'] = array_values($studentMarks);
    $data[] = $resultTypeData;
}

$jsonData = json_encode($data, JSON_PRETTY_PRINT);
echo $jsonData;

mysqli_close($conn);
}else{
  echo(json_encode(['icon' => 'error', 'message' => 'Access denied. Only faculties can view it.']));
  exit();
}
?>