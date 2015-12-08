<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vulcun Coding Challenge</title>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
	<span class="hidden" id="uuid"><?= $uuid; ?></span>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">PVP Backend</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Just</a></li>
            <li><a href="#">Placeholder</a></li>
            <li><a href="#">Links</a></li>
          </ul>
          <form class="navbar-form navbar-right" action="/stats" method="post">
            <input type="text" class="form-control" placeholder="Search for character..." name="name">
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a></li>
            <li class="disabled"><a href="#">Download History <small>(JSON)</small></a></li>
          </ul>
		
		<!-- online player section -->
		<div class="panel panel-primary">
			<div class="panel-heading">Challenge Players</div>
			<ul class="list-group" id="live-players">
				<li class="list-group-item"><a href='/stats/teemo'>Teemo</a></li>
				<?php foreach($randPlayers as $playerName) { ?>
				<li class="list-group-item"><a href="/challenge/<?= $name; ?>/<?= $playerName['name']; ?>"><?= $playerName['name']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
          <!--<ul class="nav nav-sidebar">
            <li><a href="">Nav item</a></li>
            <li><a href="">Nav item again</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="">Another nav item</a></li>
            <li><a href="">More navigation</a></li>
          </ul>-->
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Character "<?php echo $name; ?>" Stats</h1>

          <div class="row placeholders">
			<?php foreach($stats as $key => $val) { ?>
            <div class="col-xs-6 col-sm-2 placeholder">
              <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="100" height="100" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4><?php echo ucfirst($key); ?></h4><h4 class="text-muted"><?php echo $val; ?></h4>
              <!--<span class="text-muted">$val</span>-->
            </div>
			<?php }?>
		  </div>

          <h2 class="sub-header">Challenge History</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Challenger</th>
                  <th>Opponent</th>
                  <th>Your Outcome</th>
                </tr>
              </thead>
              <tbody>
				  <?php if($history !== false) { foreach($history as $match){ ?>
					<tr>
					  <td><?= $match['challenger']; ?></td>
					  <td><?= $match['opponent']; ?></td>
					  <td>
						<?php 
							if($match['challenger'] == $name) {
								if($match['outcome'] == 1) {
									echo "Victory";
								} else {
									echo "Defeat";
								}
							} else {
								if($match['outcome'] == 0) {
									echo "Victory";
								} else {
									echo "Defeat";
								}
							}
						?>
					  </td>
                	</tr>		
				  <?php }}?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
