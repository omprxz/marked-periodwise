<?php
require('../libs/fpdf/fpdf.php');
require('conn.php');
require_once('vars.php');

if($_GET['type'] == 'student'){
  if (isset($_GET['resT_id']) && isset($_GET['sUserId'])) {
    $resT_id = $_GET['resT_id'];
    $sUserId = $_GET['sUserId'];

    $user_query = "SELECT name, branch, semester, session, roll FROM users WHERE id = '$sUserId'";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);

    $marks_query = "SELECT * FROM marks WHERE s_id = '$sUserId' AND resT_id = '$resT_id'";
    $marks_result = mysqli_query($conn, $marks_query);

    $subject_query = "SELECT id, name FROM subjects";
    $subject_result = mysqli_query($conn, $subject_query);
    $subjects = array();
    while ($row = mysqli_fetch_assoc($subject_result)) {
        $subjects[$row['id']] = $row['name'];
    }

    $result_type_query = "SELECT type FROM result_types WHERE id = '$resT_id'";
    $result_type_result = mysqli_query($conn, $result_type_query);
    $result_type_row = mysqli_fetch_assoc($result_type_result);
    $result_type = $result_type_row['type'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Result: ' . $result_type);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, 'Name: ' . $user['name']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Branch: ' . $user['branch']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Semester: ' . ordinal($user['semester']));
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Session: ' . $user['session']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Roll No.: ' . $user['roll']);
    $pdf->Ln(20);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Subject', 1);
    $pdf->Cell(30, 10, 'Marks', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $total_marks = 0;
    while ($row = mysqli_fetch_assoc($marks_result)) {
        $subject_id = $row['sub_id'];
        if (isset($subjects[$subject_id]) && !empty($subjects[$subject_id])) {
            $subject_name = $subjects[$subject_id];
            $marks = is_null($row['marks']) ? "-" : $row['marks'];
            $total_marks += is_numeric($marks) ? $marks : 0;
            $pdf->Cell(100, 10, $subject_name, 1);
            $pdf->Cell(30, 10, $marks, 1);
            $pdf->Ln();
        }
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Total', 1);
    $pdf->Cell(30, 10, $total_marks, 1);

    $filename = $user['name'] . '-' . $result_type . '.pdf';
    $pdf->Output('D', $filename);
}
}elseif ($_GET['type'] == 'faculty') {
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

if (!$id) {
    echo "Invalid ID.";
    exit();
}

$query = "
    SELECT m.s_id, m.marks, m.sub_id, u.name, u.semester, u.session, u.branch, u.roll, 
           s.name AS subject, rt.type AS result_type
    FROM marks m
    INNER JOIN users u ON m.s_id = u.id
    INNER JOIN subjects s ON m.sub_id = s.id
    INNER JOIN result_types rt ON m.resT_id = rt.id
    WHERE m.resT_id = '$id'
    ORDER BY u.id, m.sub_id
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "No data found for the given ID.";
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[$row['s_id']]['name'] = $row['name'];
    $data[$row['s_id']]['roll'] = $row['roll'];
    $data[$row['s_id']]['branch'] = $row['branch'];
    $data[$row['s_id']]['semester'] = $row['semester'];
    $data[$row['s_id']]['session'] = $row['session'];
    $data[$row['s_id']]['marks'][$row['subject']] = $row['marks'];
    $data[$row['s_id']]['result_type'] = $row['result_type'];
    if (!isset($data[$row['s_id']]['total'])) {
        $data[$row['s_id']]['total'] = 0;
    }
    $data[$row['s_id']]['total'] += $row['marks'];
}

$subjects = array_keys($data[array_key_first($data)]['marks']);
$resultType = $data[array_key_first($data)]['result_type'];

if ($format === 'pdf') {
    class PDF extends FPDF {
        function Header() {
            global $resultType;
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, $resultType . ' Marks', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Table header
    $pdf->Cell(30, 10, 'Name', 1);
    $pdf->Cell(20, 10, 'Roll', 1);
    $pdf->Cell(30, 10, 'Branch', 1);
    $pdf->Cell(20, 10, 'Semester', 1);
    $pdf->Cell(30, 10, 'Session', 1);
    foreach ($subjects as $subject) {
        $pdf->Cell(20, 10, $subject, 1);
    }
    $pdf->Cell(20, 10, 'Total', 1);
    $pdf->Ln();

    // Table data
    foreach ($data as $student) {
        $pdf->Cell(30, 10, $student['name'], 1);
        $pdf->Cell(20, 10, $student['roll'], 1);
        $pdf->Cell(30, 10, $student['branch'], 1);
        $pdf->Cell(20, 10, $student['semester'], 1);
        $pdf->Cell(30, 10, $student['session'], 1);
        foreach ($subjects as $subject) {
            $marks = isset($student['marks'][$subject]) ? $student['marks'][$subject] : 0;
            $pdf->Cell(20, 10, $marks, 1);
        }
        $pdf->Cell(20, 10, $student['total'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', "$resultType Marks.pdf");
} elseif ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $resultType . ' Marks.csv"');

    $output = fopen('php://output', 'w');
    $header = array_merge(['Name', 'Roll', 'Branch', 'Semester', 'Session'], $subjects, ['Total']);
    fputcsv($output, $header);

    foreach ($data as $student) {
        $row = [
            $student['name'],
            $student['roll'],
            $student['branch'],
            $student['semester'],
            $student['session']
        ];
        foreach ($subjects as $subject) {
            $row[] = isset($student['marks'][$subject]) ? $student['marks'][$subject] : 0;
        }
        $row[] = $student['total'];
        fputcsv($output, $row);
    }

    fclose($output);
} elseif ($_GET['type'] == 'faculty') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $format = isset($_GET['format']) ? $_GET['format'] : 'csv';

    if (!$id) {
        echo "Invalid ID.";
        exit();
    }

    $query = "
        SELECT m.s_id, m.marks, m.sub_id, u.name, u.semester, u.session, u.branch, u.roll, 
               s.name AS subject, rt.type AS result_type
        FROM marks m
        INNER JOIN users u ON m.s_id = u.id
        INNER JOIN subjects s ON m.sub_id = s.id
        INNER JOIN result_types rt ON m.resT_id = rt.id WHERE m.f_id = '$id'
        ORDER BY u.id, m.sub_id
    ";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "No data found for the given ID.";
        exit();
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['s_id']]['name'] = $row['name'];
        $data[$row['s_id']]['roll'] = $row['roll'];
        $data[$row['s_id']]['branch'] = $row['branch'];
        $data[$row['s_id']]['semester'] = $row['semester'];
        $data[$row['s_id']]['session'] = $row['session'];
        $data[$row['s_id']]['marks'][$row['subject']] = $row['marks'];
        $data[$row['s_id']]['result_type'] = $row['result_type'];
        if (!isset($data[$row['s_id']]['total'])) {
            $data[$row['s_id']]['total'] = 0;
        }
        $data[$row['s_id']]['total'] += $row['marks'];
    }

    $subjects = array_keys($data[array_key_first($data)]['marks']);
    $resultType = $data[array_key_first($data)]['result_type'];

    if ($format === 'pdf') {
        class PDF extends FPDF {
            function Header() {
                global $resultType;
                $this->SetFont('Arial', 'B', 14);
                $this->Cell(0, 10, $resultType . ' Marks', 0, 1, 'C');
                $this->Ln(5);
            }

            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
            }
        }

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Table header
        $pdf->Cell(30, 10, 'Name', 1);
        $pdf->Cell(20, 10, 'Roll', 1);
        $pdf->Cell(30, 10, 'Branch', 1);
        $pdf->Cell(20, 10, 'Semester', 1);
        $pdf->Cell(30, 10, 'Session', 1);
        foreach ($subjects as $subject) {
            $pdf->Cell(20, 10, $subject, 1);
        }
        $pdf->Cell(20, 10, 'Total', 1);
        $pdf->Ln();

        // Table data
        foreach ($data as $student) {
            $pdf->Cell(30, 10, $student['name'], 1);
            $pdf->Cell(20, 10, $student['roll'], 1);
            $pdf->Cell(30, 10, $student['branch'], 1);
            $pdf->Cell(20, 10, $student['semester'], 1);
            $pdf->Cell(30, 10, $student['session'], 1);
            foreach ($subjects as $subject) {
                $marks = isset($student['marks'][$subject]) ? $student['marks'][$subject] : 0;
                $pdf->Cell(20, 10, $marks, 1);
            }
            $pdf->Cell(20, 10, $student['total'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', "$resultType Marks.pdf");
    } elseif ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $resultType . ' Marks.csv"');

        $output = fopen('php://output', 'w');
        $header = array_merge(['Name', 'Roll', 'Branch', 'Semester', 'Session'], $subjects, ['Total']);
        fputcsv($output, $header);

        foreach ($data as $student) {
            $row = [
                $student['name'],
                $student['roll'],
                $student['branch'],
                $student['semester'],
                $student['session']
            ];
            foreach ($subjects as $subject) {
                $row[] = isset($student['marks'][$subject]) ? $student['marks'][$subject] : 0;
            }
            $row[] = $student['total'];
            fputcsv($output, $row);
        }

        fclose($output);
    } else {
        echo "Invalid format specified.";
    }
} else {
    echo "Invalid type specified.";
}
}

function ordinal($number) {
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}
?>