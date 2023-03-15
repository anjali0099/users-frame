
<div class="container">
  <nav class="navbar navbar-expand-lg  navbar-light" style="background-color: #e3f2fd;">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href=<?=base_url()."User"?>>Home</a>
        </li>
      </ul>

      <ul class="navbar-nav mr-right">
        <?php if(isset($_SESSION['Auth']['User']['email']) && $_SESSION['Auth']['User']['email'] == true):?>
          <li class="nav-item active">
            <a class="nav-link">
              <?php
                $firstname = $_SESSION['Auth']['User']['name'];
                $email = $_SESSION['Auth']['User']['email'];
                $file = "Assets/document_$firstname.txt";
                if(file_exists($file))
                {
                  $myfile = file_get_contents($file);
                  $convertedarray = json_decode($myfile,true);
                  echo 'Welcome ' . $convertedarray["name"] .'<br>'.'Email' ."\r". $convertedarray["email"];
                }
             ?></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href=<?=base_url()."Log/Logout"?>>Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item active">
            <a class="nav-link" href=<?=base_url()."Log/login"?>>Login</a>
          </li>
       <?php endif; ?>
    </ul>
  </div>
</nav>
</div>