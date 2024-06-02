<div class="table-responsive mb-2 auto">
          <div class="alert alert-warning mt-4 mx-2 noStudentAlert" role="alert">
        No students in this class.
    </div>
        <table id="studentsTable" class="d-none table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Session</th>
                    <th>Branch</th>
                    <th>Semester</th>
                    <th>Roll</th>
                    <th>Email</th>
                    <th>Joined On</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table body content will be dynamically populated -->
            </tbody>
        </table>
    </div>
    
    
<script>
  $(document).ready(function() {
    const classId = <?php echo($_GET['id']) ?>;
    $.ajax({
        url: "actions/fetch_students_by_class.php",
        type: "GET",
        data: {
            classId: classId
        },
        dataType: "json",
        success: function(data) {
          if (data.icon === 'error') {
                      
                        return;
                    }else{
                      $('.noStudentAlert').addClass('d-none')
                      $('#studentsTable').removeClass('d-none')
                    }
            $('#studentsTable').DataTable({
                data: data,
                columns: [
                    { "data": "name" },
                    { "data": "session" },
                    { "data": "branch" },
                    { "data": "semester" },
                    { "data": "roll" },
                    { "data": "email" },
                    { "data": "joinedon" }
                ],
                scrollX: true
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error fetching data: ", textStatus, errorThrown);
        }
    });
});
</script>