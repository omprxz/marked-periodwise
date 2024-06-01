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

 $('.changePassword').click(function () {
    var currentPass = $('#currentPass').val();
    var newPass1 = $('#newPass1').val();
    var newPass2 = $('#newPass2').val();

    if (currentPass === '' || newPass1 === '' || newPass2 === '') {
      Toast.fire({
        icon: 'error',
        title: 'Please fill in all fields!'
      });
      return;
    }

    if (newPass1 !== newPass2) {
      Toast.fire({
        icon: 'error',
        title: 'New passwords do not match!'
      });
      return;
    }

    $.ajax({
      type: 'POST',
      url: 'actions/change_password.php',
      data: {
        currentPass: currentPass,
        newPass: newPass1
      },
      dataType:'json',
      success: function (cpData) {
        if (cpData['status'] === 'success') {
          Toast.fire({
            icon: 'success',
            title: cpData['result']
          })
            $('#changePasswordDiv').modal('hide');
          document.getElementById('changePasswordForm').reset()
        } else {
          Toast.fire({
            icon: 'error',
            title: cpData['result']
          });
        }
      },
      error: function () {
        Toast.fire({
          icon: 'error',
          title: 'Something Went Wrong!'
        });
      }
    });
  });

 $('.logoutBtn').click(function () {
    Swal.fire({
  title: "Are you sure to logout?",
  icon: "warning",
  showCancelButton: true,
  confirmButtonColor: "#d63030",
  cancelButtonColor: "#767676",
  confirmButtonText: "Yes, Logout!"
}).then((result) => {
  if (result.isConfirmed) {
    window.location.href = "logout.php";
  }
});
  })