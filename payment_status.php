<!DOCTYPE html>
<html lang="en">
<head>
  <title>eLaundry - Subscription Notice</title>
  <?php include("components/links.php"); ?>
  <style>
    body{
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .subscription-notice {
      min-height: 70vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .subscription-notice a {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 20px;
      text-decoration: none;
      color: var(--primary-color, #007bff);
      font-weight: 500;
    }
    .subscription-notice h1 {
      font-size: 4rem;
      margin-bottom: 15px;
      line-height: 1;
    }
    .subscription-notice h4 {
      color: #555;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include("components/nav.php"); ?>

  <br><br>
  <div class="container subscription-notice">
    <a onclick="history.back()" href="javascript:void(0)">
      <i class="fa fa-arrow-left"></i> Back
    </a>

    <!-- Friendly error icon -->
    <h1>🚫</h1>
    <h3 class='mt-2'>This user has not yet subscribed</h3>
  </div>
 
  <br><br><br>
  <!-- Footer -->
  <?php include("components/footer.php"); ?> 

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
