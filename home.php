<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">

            <!-- Main content -->
            <section class="content">
                <?php
                $parse = parse_ini_file('admin/config.ini', FALSE, INI_SCANNER_RAW);
                $title = $parse['election_title'];
                ?>
                <h1 class="page-header text-center title"><b><?php echo strtoupper($title); ?></b></h1>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="text-center">
                            <h3>Time remaining: <span id="timer"></span></h3>
                        </div>
                        <?php
                        if (isset($_SESSION['error'])) {
                            ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <ul>
                                    <?php
                                    foreach ($_SESSION['error'] as $error) {
                                        echo "
                                            <li>" . $error . "</li>
                                        ";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                            unset($_SESSION['error']);
                        }
                        if (isset($_SESSION['success'])) {
                            echo "
                                <div class='alert alert-success alert-dismissible'>
                                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                    <h4><i class='icon fa fa-check'></i> Success!</h4>
                                    " . $_SESSION['success'] . "
                                </div>
                            ";
                            unset($_SESSION['success']);
                        }
                        ?>

                        <div class="alert alert-danger alert-dismissible" id="alert" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <span class="message"></span>
                        </div>

                        <?php
                        $sql = "SELECT * FROM votes WHERE voters_id = '" . $voter['id'] . "'";
                        $vquery = $conn->query($sql);
                        if ($vquery->num_rows > 0) {
                            ?>
                            <div class="text-center">
                                <h3>You have already voted for this election.</h3>
                                <a href="#view" data-toggle="modal" class="btn btn-flat btn-primary btn-lg">View Ballot</a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <!-- Voting Ballot -->
                            <form method="POST" id="ballotForm" action="submit_ballot.php">
                                <?php
                                include 'includes/slugify.php';

                                $candidate = '';
                                $sql = "SELECT * FROM positions ORDER BY priority ASC";
                                $query = $conn->query($sql);
                                while ($row = $query->fetch_assoc()) {
                                    $sql = "SELECT * FROM candidates WHERE position_id='" . $row['id'] . "'";
                                    $cquery = $conn->query($sql);
                                    while ($crow = $cquery->fetch_assoc()) {
                                        $slug = slugify($row['description']);
                                        $checked = '';
                                        if (isset($_SESSION['post'][$slug])) {
                                            $value = $_SESSION['post'][$slug];

                                            if (is_array($value)) {
                                                foreach ($value as $val) {
                                                    if ($val == $crow['id']) {
                                                        $checked = 'checked';
                                                    }
                                                }
                                            } else {
                                                if ($value == $crow['id']) {
                                                    $checked = 'checked';
                                                }
                                            }
                                        }
                                        $input = ($row['max_vote'] > 1) ? '<input type="checkbox" class="flat-red ' . $slug . '" name="' . $slug . "[]" . '" value="' . $crow['id'] . '" ' . $checked . '>' : '<input type="radio" class="flat-red ' . $slug . '" name="' . slugify($row['description']) . '" value="' . $crow['id'] . '" ' . $checked . '>';
                                        $image = (!empty($crow['photo'])) ? 'images/' . $crow['photo'] : 'images/profile.jpg';
                                        $candidate .= '
                                            <li>
                                                ' . $input . '<button type="button" class="btn btn-primary btn-sm btn-flat clist platform" data-platform="' . $crow['platform'] . '" data-fullname="' . $crow['firstname'] . ' ' . $crow['lastname'] . '"><i class="fa fa-search"></i> Platform</button><img src="' . $image . '" height="100px" width="100px" class="clist"><span class="cname clist">' . $crow['firstname'] . ' ' . $crow['lastname'] . '</span>
                                            </li>
                                        ';
                                    }

                                    $instruct = ($row['max_vote'] > 1) ? 'You may select up to ' . $row['max_vote'] . ' candidates' : 'Select only one candidate';

                                    echo '
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box box-solid" id="' . $row['id'] . '">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title"><b>' . $row['description'] . '</b></h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <p>' . $instruct . '
                                                            <span class="pull-right">
                                                                <button type="button" class="btn btn-success btn-sm btn-flat reset" data-desc="' . slugify($row['description']) . '"><i class="fa fa-refresh"></i> Reset</button>
                                                            </span>
                                                        </p>
                                                        <div id="candidate_list">
                                                            <ul>
                                                                ' . $candidate . '
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ';

                                    $candidate = '';
                                }
                                ?>
                                <div class="text-center">
                                    <button type="button" class="btn btn-warning btn-flat" id="sendOTP"><i class="fa fa-envelope"></i> Send OTP</button>
                                    <div id="otpSection" style="display:none;">
                                        <input type="text" id="otp" class="form-control" placeholder="Enter OTP">
                                        <button type="button" class="btn btn-success btn-flat" id="verifyOTP"><i class="fa fa-check"></i> Verify OTP</button>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-flat" id="submitBallot" name="vote" disabled><i class="fa fa-check-square-o"></i> Submit</button>
                                </div>
                            </form>
                            <!-- End Voting Ballot -->
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/ballot_modal.php'; ?>
</div>

<?php
if (!isset($voter['id'])) {
    echo "<script>console.error('Voter ID is not set in PHP.');</script>";
}
?>

<?php include 'includes/scripts.php'; ?>
<script>
$(function () {
    // Timer setup
    var timerInterval;
    var timeLeft = 180;

    function startTimer() {
        timerInterval = setInterval(function () {
            timeLeft--;
            $('#timer').text(formatTime(timeLeft));
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                window.location.href = 'logout.php';
            }
        }, 1000);
    }

    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
    }

    startTimer();


    // OTP Handling
		$('#sendOTP').click(function () {
			$.ajax({
				url: 'send_otp.php',
				method: 'POST',
				data: { voters_id: <?php echo json_encode($voter['id']); ?> },
				success: function (response) {
					const result = JSON.parse(response);
					if (result.success) {
						alert(result.message);
						$('#otpSection').show();
					} else {
						alert(result.message);
					}
				},
				error: function (xhr, status, error) {
					alert("Failed to send OTP. Please try again later.");
					console.error("AJAX error: " + status + ": " + error);
				}
			});
		});

		$('#verifyOTP').click(function () {
			var otp = $('#otp').val();
			$.ajax({
				url: 'verify_otp.php',
				method: 'POST',
				data: { otp: otp },
				success: function (response) {
					const result = JSON.parse(response);
					if (result.success) {
						alert(result.message);
						$('#submitBallot').removeAttr('disabled');
					} else {
						alert(result.message);
					}
				},
				error: function (xhr, status, error) {
					alert("OTP verification failed. Try again.");
					console.error("AJAX error: " + status + ": " + error);
				}
			});
		});
	});


</script>
</body>
</html>
