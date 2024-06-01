<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}

require('actions/conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>View Marks</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
          type="text/css" media="all"/>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
  
  <style>
.dataTables_wrapper .custBtn-primary {
  display: inline-block;
  font-weight: 400;
  color: #212529;
  text-align: center;
  vertical-align: middle;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  background-color: transparent;
  border: 1px solid transparent;
  padding: 0.375rem 0.75rem;
  font-size: 0.9rem;
  line-height: 1.5;
  border-radius: 0.25rem;
  transition: color 0.15s ease-in-out,
  background-color 0.15s ease-in-out,
  border-color 0.15s ease-in-out,
  box-shadow 0.15s ease-in-out;
}
.dataTables_wrapper .custBtn-primary:hover {
  color: #212529;
  text-decoration: none;
}
.dataTables_wrapper .custBtn-primary {
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
}
.dataTables_wrapper .custBtn-primary:hover {
  color: #fff;
  background-color: #0069d9 !important;
  border-color: #0062cc;
}

.dataTables_wrapper .custBtn-primary:focus {
  box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
}
.dataTables_wrapper .custBtn-primary:not(:disabled):not(.disabled):active {
  color: #fff;
  background-color: #0062cc;
  border-color: #005cbf;
}
.dataTables_wrapper .custBtn-primary:not(:disabled):not(.disabled):active:focus {
  box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
}
  table.dataTable{
    font-size: 0.8rem;
    border-top: 1px solid #DEE2E6;
    border-bottom: 1px solid #DEE2E6;
    border-radius: 3px;
    margin-top: 10px;
    text-align: center;
  }
  #tables-container h3{
    font-weight: bold;
    margin: 10px auto;
    text-align: center;
    text-decoration: underline;
    margin-top: 15px;
  }
  table.dataTable thead th, table.dataTable tbody td{
    min-width: 60px;
    max-width: 150px;
    overflow: auto;
  }
    table.dataTable thead th {
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 1;
        text-transform: capitalize;
    }
    table.dataTable thead th:first-child {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 10;
    }
    table.dataTable tbody td:first-child {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 10;
    }
    .dataTables_wrapper .dataTables_info{
      font-size: 0.8rem;
    }
    .dataTables_wrapper .dataTables_filter input{
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <?php include 'nav.php'; ?>
   <?php 
  $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
  $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
  $role = $roleE['role'];
  if($role == 'faculty'){
  ?>
    <div class="container my-4">
        <div id="tables-container"></div>
    </div>
<?php
}else{
?>
<div class="alert alert-danger m-4">
  This page is only for faculties.
</div>
<?php
}
?>
    <script>
     $(document).ready(function() {
        $.ajax({
            url: 'actions/fetchAllMarks.php',
            type: 'GET',
            dataType: 'json',
            success: function(jsonData) {
              if(jsonData.length > 0){
                jsonData.forEach(data => {
                    createTable(data.resultType, data.details);
                });
              }else{
                $('#tables-container').html('<h3 class="text-center fw-bold">View Marks</h3><div class="alert alert-info mt-3">No marks record found.</div>')
              }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data: ", status, error);
            }
        });

     function createTable(resultType, details) {
            let tableHtml = `<div class="table-responsive">
                <table id="table-${resultType}" class="display nowrap table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>`;
            
            Object.keys(details[0]).forEach(key => {
                if (key === 'marks') {
                    Object.keys(details[0][key]).forEach(subKey => {
                        tableHtml += `<th>${subKey}</th>`;
                    });
                } else {
                    tableHtml += `<th>${key}</th>`;
                }
            });

            tableHtml += `</tr>
                        <tr>`;
            
            Object.keys(details[0]).forEach(key => {
                if (key === 'marks') {
                    Object.keys(details[0][key]).forEach(subKey => {
                        tableHtml += `<th><input type="text" class="form-control form-control-sm" placeholder="Search ${subKey}"></th>`;
                    });
                } else {
                    tableHtml += `<th><input type="text" class="form-control form-control-sm" placeholder="Search ${key}"></th>`;
                }
            });

            tableHtml += `</tr>
                    </thead>
                    <tbody>`;

            details.forEach(detail => {
                tableHtml += `<tr>`;
                Object.values(detail).forEach(value => {
                    if (typeof value === 'object' && value !== null) {
                        Object.values(value).forEach(subValue => {
                            tableHtml += `<td>${subValue}</td>`;
                        });
                    } else {
                        tableHtml += `<td>${value}</td>`;
                    }
                });
                tableHtml += `</tr>`;
            });

            tableHtml += `</tbody></table></div>`;

            $("#tables-container").append(`<h3>${resultType}</h3>`);
            $("#tables-container").append(tableHtml);
            
$("#tables-container").append('<hr>');

            
            let table = $(`#table-${resultType}`).DataTable({
                dom: 'Bfrtip',
                buttons: [
        {
            extend: 'csvHtml5',
            text: 'Download CSV',
            className: 'downloadCsv custBtn-primary'
        },
    ],
                paging: false,
                fixedHeader: true,
                fixedColumns: {
                    leftColumns: 1
                },
                scrollX: true
            });

            $(`#table-${resultType}_filter`).addClass('main-search-div');
            $(`#table-${resultType}_filter input`)
                .addClass('form-control rounded')
                .prop('placeholder', `Search ${resultType}`);
            $(`#table-${resultType}_filter label`).contents().filter(function() {
                return this.nodeType === 3;
            }).remove();
            
table.columns().every(function (index) {
    let column = this;
    $('input', this.header()).on('keyup change', function () {
        if (column.search() !== this.value) {
            column
                .search(this.value)
                .draw();
        }
    });
});
        }
    });
    </script>
    <script src="eruda.js"></script>
</body>
</html>