<nav class="main-header navbar navbar-expand navbar-primary navbar-dark ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_Pf_Number'])){?>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
            </li>
        <?php } ?>
      <li>
        <span class="nav-link text-white" role="button" onclick="window.location.reload();">
            <large>
                <b>Employee Performance Evaluation System</b>
            </large>
        </span>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <div id="maximize" class="bg-transparent" onclick="openFullscreen();"><i id="maximize" class="fas fa-expand-arrows-alt"></i></div>
          <div id="minimize" class="bg-transparent d-none" onclick="closeFullscreen();"><i class="fas fa-compress-arrows-alt"></i></div>
        </a>
      </li>
        <li class="nav-item dropdown">
            <a class="nav-link"  data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
              <span>
                <div class="d-felx badge-pill">
                  <span>
                    <?php
                      if (empty($_SESSION['avatar']) == true || $_SESSION['avatar']=='notSet' || empty($_SESSION['avatar']) == false && is_file('userProfilePictures/'.$_SESSION['avatar']) == false){
                    ?>
                      <!-- <span>
                          <?php
                              echo strtoupper(substr($_SESSION['first_name'], 0,1).substr($_SESSION['last_name'], 0,1));
                          ?>
                      </span> -->
                    <?php }else{ ?>
                      <img src="userProfilePictures/<?php echo $_SESSION['avatar'] ?>" alt="" class="user-img border">
                    <?php } ?>
                  </span>
                  <span><b><?php echo $_SESSION['user_Pf_Number'].' '.ucwords($_SESSION['first_name']) ?></b></span>
                  <span class="fa fa-angle-down ml-2"></span>
                </div>
              </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
              <a class="dropdown-item" href="change_password"><i class="fa fa-cog"></i> Change password</a>
              <a class="dropdown-item" href="logout"><i class="text-danger fa fa-power-off"></i> Logout</a>
            </div>
      </li>
    </ul>
</nav>

<script>
    $('#manage_account').click(function(){uni_modal('Manage Account','manage_user?id=<?php echo $_SESSION['user_Pf_Number'] ?>')})
</script>

<script>
    var elem = document.documentElement;
    /* Open fullscreen */
    function openFullscreen()
    {
      document.getElementById('maximize').className = 'd-none';
      document.getElementById('minimize').className = 'd-block';

      if (elem.requestFullscreen)
      {
          elem.requestFullscreen();
      }
      else
      if (elem.webkitRequestFullscreen) 
      { /* Safari */
          elem.webkitRequestFullscreen();
      }
      else
      if (elem.msRequestFullscreen)
      { /* IE11 */
          elem.msRequestFullscreen();
      }
    }
</script>

<script>
  var elem = document.documentElement;
  /* Close fullscreen */
  function closeFullscreen()
  {
    document.getElementById('minimize').className = 'd-none';
    document.getElementById('maximize').className = 'd-block';

    if (document.exitFullscreen)
    {
        document.exitFullscreen();
    }
    else
    if (document.webkitExitFullscreen)
    { /* Safari */
        document.webkitExitFullscreen();
    }
    else
    if (document.msExitFullscreen)
    { /* IE11 */
        document.msExitFullscreen();
    }
  }
</script>

<style>
    .user-img
    {
        border-radius: 50%;
        height: 25px;
        width: 25px;
        object-fit: cover;
    }
</style>