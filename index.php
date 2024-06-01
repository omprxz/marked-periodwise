<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>MarkEd</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" type="text/css"
    media="all" />
    <link href="/components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <style>
      .background-image{
        z-index: -1;
      }
      .home-title{
        font-family: Roboto;
        color:rgb(0, 0, 0);
        font-size: 40px;
      }
    </style>
</head>

<body>
  <?php include 'nav.php'; ?>
  <img src="resources/home.jpg" alt="" class="w-100 h-100 position-fixed top-0 start-0 background-image object-fit-cover " />
  <div class="container p-0 d-flex flex-column  align-items-center mt-5 position-relative px-3 text-center vh-100 ">
    <img src="resources/logo.png" class="w-75" alt="" />
    <div class="content">
      <h1 class=" fw-bold  text-center home-title">Online attendance marking made simple</h1>

      <a href="signup.php" class="btn btn-outline-primary mt-3 rounded-5 ">Get Started <i class="fas fa-arrow-right"></i></a>

    </div>
  </div>
  <script src="eruda.js" type="text/javascript" charset="utf-8"></script>
</body>

</html>