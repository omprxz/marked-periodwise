<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        type="text/css" media="all" />
    <link
        href="/components/libs/font-awesome-pro/css/all.min.css"
        rel="stylesheet">
</head>

<body>
  <?php include 'nav.php'; ?>
    <div class="container-fluid ">
        <h1 class="text-center mt-3">Forgot Password</h1>
        
        <form method="post" class="m-4 ">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="name@example.com">
                <label for="email">Email address</label>
            </div>
            <div class="mt-4 text-center send-otp-div">
                <button class="btn btn-outline-primary send" type="button">Send OTP</button>
            </div>
            
            <div class="post-otp d-none my-2">
              <div class="form-floating mb-3">
                <input type="number" name="otp" class="form-control" id="otp" placeholder="">
                <label for="otp">OTP</label>
            </div>
              <div class="input-group mb-3">
                <input type="password" name="pass" class="form-control" id="pass" placeholder="New Password"
                    aria-label="New Password">
                <label for="pass" class="visually-hidden">New Password</label>
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass">
                    <i class="fad fa-eye"></i>
                </button>
            </div>
              <div class="input-group mb-3">
                <input type="password" name="pass2" class="form-control" id="pass2" placeholder="Confirm New Password"
                    aria-label="Confirm New Password">
                <label for="pass2" class="visually-hidden">Confirm New Password</label>
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass2">
                    <i class="fad fa-eye"></i>
                </button>
            </div>
              <div class="mt-4 text-center">
                <button class="btn btn-outline-success reset" type="button">Reset Password</button>
               </div>
            </div>
            
        </form>
        <div class="text-center my-3">Proceed to <a href="login.php" class="link-primary">Log in</a></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="eruda.js" type="text/javascript" charset="utf-8"></script>
    <script>
    const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter',
      Swal.stopTimer)
    toast.addEventListener('mouseleave',
      Swal.resumeTimer)
  }
})

       $(document).ready(function () {
    $(".toggle-password").click(function () {
        var targetId = $(this).data("target");
        var passwordField = $("#" + targetId);
        var fieldType = passwordField.attr("type") === "password" ? "text" : "password";
        passwordField.attr("type", fieldType);
        $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });

    $('.send').click(function(){
        var email = $('#email').val();

        if (!isValidEmail(email)) {
            Toast.fire({
                icon: 'error',
                title: 'Invalid Email'
            });
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'actions/forgotAction.php',
            data: {email: email, action: 'send'},
            dataType: 'json',
            beforeSend: function() {
                $('.send').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                $('.send').attr('disabled', true);
                $('#email').attr('disabled', true);
            },
            success: function(response) {
                $('#email').attr('disabled', false);
                if(response['status'] == 'success'){
                  $('#email').attr('disabled', true);
                  $('.send-otp-div').addClass('d-none');
                  $('.post-otp').removeClass('d-none');
                }
                Toast.fire({
                    icon: response['status'],
                    text: response['message']
                });
            },
            error: function(xhr, status, error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to send OTP.'
                });
            },
            complete: function() {
                $('.send').html('Send OTP');
                $('.send').attr('disabled', false);
            }
        }); 
    });

    $('.reset').click(function(){
    var email = $('#email').val();
    var otp = $('#otp').val();
    var password = $('#pass').val();
    var confirmPassword = $('#pass2').val();

    if (password !== confirmPassword) {
        Toast.fire({
            icon: 'error',
            title: 'Passwords do not match'
        });
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'actions/forgotAction.php',
        dataType: 'json',
        data: {email: email, otp: otp, pass: password, action: 'verify'},
        beforeSend: function() {
            $('.reset').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            $('.reset').attr('disabled', true);
            $('#otp').attr('disabled', true);
            $('#pass').attr('disabled', true);
            $('#pass2').attr('disabled', true);
        },
        success: function(response) {
            if (response.status === 'success') {
                Toast.fire({
                    icon: 'success',
                    title: response.message
                }).then((result) => {
                    $('#email').val('');
                    $('#otp').val('');
                    $('#pass').val('');
                    $('#pass2').val('');
                    window.location.href = 'login.php';
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Toast.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to change password'
            });
        },
        complete: function() {
            $('.reset').html('Reset Password');
            $('.reset').attr('disabled', false);
            $('#otp').attr('disabled', false);
            $('#pass').attr('disabled', false);
            $('#pass2').attr('disabled', false);
        }
    });
});
});

function isValidEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}
        
    </script>
</body>

</html>