<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="favicon.ico" type="image/ico">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/jquery.dataTables.min.css">
    <link rel="stylesheet" href="static/select.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="static/jquery-ui.css">
    <link rel="stylesheet" href="printstyle.css">
    <style>
        .main-jumbo {
            background-image: url("img/1.jpg");
            background-size: cover;
        }
    </style>
    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = null;
    $isSessionExists = false;
    $cHandler = null;
    $bdHandler = null;
    $cBookings = null;
    if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            $isSessionExists = true;

            $cHandler = new CustomerHandler();
            $cHandler = $cHandler->getCustomerObj($_SESSION["customerEmail"]);

            $cAdmin = new Customer();
            $cAdmin->setEmail($cHandler->getEmail());
            $cAdmin->setIsAdmin((new CustomerHandler())->handleIsAdmin($cAdmin->getEmail()));
            $isAdmin = $cAdmin->isAdminSignedIn();

            $bdHandler = new BookingDetailHandler();
            $cBookings = $bdHandler->getCustomerBookings($cHandler);
        }

    ?>

    <title>Home</title>
</head>

<body>

    <header>
        <div class="bg-dark collapse" id="navbarHeader" style="">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8 col-md-7 py-4">
                        <h4 class="text-white">About</h4>
                        <p class="text-muted">Accommodation is provided to devotees visiting Srimatam at the Yatri Nivas on Sri Kamakshi Amman Sannadhi Street.</p>
                    </div>
                    <div class="col-sm-4 offset-md-1 py-4 text-right">
                        <?php if ($isSessionExists) { ?>
                            <h4 class="text-white"><?php echo $username; ?></h4>
                            <ul class="list-unstyled">
                                <?php if ($isAdmin) { ?>
                                    <li><a href="admin.php" class="text-white">Manage reservation<i class="far fa-address-book ml-2"></i></a></li>
                                <?php } else { ?>
                                    <li><a href="#" class="text-white my-reservations" id="my-reservations-id">View my bookings<i class="far fa-address-book ml-2"></i></a></li>
                                    <li>
                                        <a href="#" class="text-white" data-toggle="modal" data-target="#myProfileModal">Update profile<i class="fas fa-user ml-2"></i></a>
                                    </li>
                                <?php } ?>
                                <li><a href="#" id="sign-out-link" class="text-white">Sign out<i class="fas fa-sign-out-alt ml-2"></i></a></li>
                            </ul>
                        <?php } else { ?>
                            <h4>
                                <a class="text-white" href="sign-in.php">Sign in</a> <span class="text-white">or</span>
                                <a href="register.php" class="text-white">Register </a>
                            </h4>
                            <p class="text-muted">Log in to reserve rooms.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar navbar-dark bg-dark box-shadow">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex align-items-center">
                    <i class="fas fa-h-square mr-2"></i>
                    <strong>Yatri Niwas, Kanchi Mutt</strong>
                </a>
                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
        <div class="container my-3" id="my-reservations-div">
            <h4>Reservations</h4>
            <table border="0" cellspacing="5" cellpadding="5">
                <tbody>
                    <tr>
                        <td>Start:</td>
                        <td><input type="text" id="min" name="min"></td>
                    </tr>
                    <tr>
                        <td>End:</td>
                        <td><input type="text" id="max" name="max"></td>
                    </tr>
                </tbody>
            </table>
            <form action="" method="post">
                <input type="text" name="subject" id="subject" value="" placeholder="Enter YN number" />
                <button type="submit" name="ok" onclick="alertme()">OK</button>
                <input type='button' id='btn-print' value='Print Receipt' onclick="printDiv('#invoice-box-id');" />
            </form>

            <?php

            if (isset($_POST['ok'])) {
                echo "You entered: ", $_POST['subject'];
            };

            ?>
            <!--start print -->



            <div class="invoice-box" id="invoice-box-id">
                <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
                    <?php foreach ($cBookings as $k => $v) {

                        if (isset($_POST['ok'])) {
                            if ($v['id'] == $_POST['subject']) {
                                echo " ";
                            } else {
                                continue;
                            }
                        }

                        ?>
                        <div class="pagebreak" style="page-break-after: always;clear: both;">
                            <table cellpadding="0" cellspacing="0">
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr>
                                                <td class="small small-left">
                                                    JAYA JAYA SANKARA
                                                </td>

                                                <td class="small small-middle" style="padding-left: 49px;">
                                                    உ
                                                </td>
                                                <td class="small small-right">
                                                    <div>HARA HARA SHANKARA</div>
                                                    <div>Ph: (044) 27233010 Fax: (044) 27224305</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr>
                                                <td class="head">
                                                    <div class="head1">his holiness</div>
                                                    <div class="head2">sri jayendra saraswathi swamigal golden jubliee charitable trust</div>
                                                    <div class="head3">Administrative Office: No. 1, Salai Street, Kancheepuram-631 502.</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr class="pan">
                                                <td class="pan1">
                                                    No. <?php echo "YN" . $v["id"]; ?>
                                                </td>
                                                <td class="pan2" style="padding-left: 94px">
                                                    PAN: AAATH 0512 N
                                                </td>
                                                <td class="pan3">
                                                    Date: <span class="content"><?php echo date('dS F Y'); ?></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="information">
                                    <td colspan="3">
                                        <table>
                                            <tr>
                                                <td style="line-height: 29px;">
                                                    <div class="ibodyname" style="float: left">Recieved with thanks from</div>
                                                    <div class="" style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 66%;">&nbsp;<?php echo explode(',', $v["requests"])[0]; ?></div>

                                                    <div class="ibodyaddress" style="float: left;margin-right: 5px;">Address</div>
                                                    <span style="word-break: break-all;text-decoration: underline; float: left; width: 91%;">&nbsp;<?php $a = explode(',', $v["requests"]);
                                                                                                                                                    array_shift($a);
                                                                                                                                                    echo implode(" ", $a); ?></span>

                                                    <div class="ibodyaddress" style="float: left">the sum of rupees</div>
                                                    <div style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 83%;">
                                                        <?php
                                                        if ($v["requirement"] == 'Ac' || $v["requirement"] == 'ac') {
                                                                echo 'One Thousand Only';
                                                            } else {
                                                                echo 'Five Hundred Only';
                                                            };
                                                        ?>
                                                    </div>

                                                    <div class="ibodyaddress" style="float: left">by cash/Cheque/Draft No.</div>
                                                    <div style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 40%;">&nbsp;</div>
                                                    <div class="ibodyaddress" style="float: left">Dated</div>
                                                    <div style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 25%;">&nbsp;</div>
                                                    <div class="ibodyaddress" style="float: left">On</div>
                                                    <!--<div style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 35%;"></div>
                                        <div class="ibodyaddress" style="float: left;">On</div>-->

                                                    <div style="border-bottom: 1px solid;float: left;margin-left: 5px;width: 40%;">&nbsp;</div>
                                                    <div class="ibodyaddress" style="float: left">Bank being Donation for General/Corpus Fund/Capital</div>
                                                    <!--<div style="border-bottom: 1px solid; float: left;" style=""></div>-->

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr class="">
                                                <td class="">
                                                    <div style="font-size: 25px;float: left; border: 1px solid; width: 30%; padding: 12px;"><span style="border-right: 1px solid; padding: 9px;">Rs.</span> <span>
                                                            <?php
                                                            if ($v["requirement"] == 'Ac' || $v["requirement"] == 'ac') {
                                                                    echo '1000/-';
                                                                } else {
                                                                    echo '500/-';
                                                                };
                                                            ?>
                                                        </span></div>
                                                    <div style="text-align: -webkit-center;line-height: 1;font-size: 13px;float: left;border: 1px solid; width: 45%; padding: 5px;margin-left: 10px;">
                                                        <span style="display: block;">
                                                            Exemption u/s 80G Vide
                                                        </span>
                                                        <span style="display: block;">
                                                            DIT (E) No. 1146 (10) / 84, dt. 26-10-2009
                                                        </span>
                                                        <span style="display: block;">
                                                            Valid from 1-4-2009 onwards.
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr class="">
                                                <td class="">
                                                    <div style="font-size: 13px;float: left;width: 254px;padding-bottom:30px">
                                                        <span style="display: block;line-height: 1;">
                                                            I declare that the above donation has been given by me to be used only towards corpus/Capital fund of the trust and will not form part of general donations.
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="top">
                                    <td colspan="3">
                                        <table>
                                            <tr class="pan">
                                                <td class="pan1">
                                                    Remitter's Signature
                                                </td>
                                                <td class="pan3">
                                                    Authorised Signatory
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <!--end print-->
            <table id="myReservationsTbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th class="text-hide p-0" data-bookId="12">12</th>
                        <th scope="col">Start</th>
                        <th scope="col">End</th>
                        <th scope="col">Room</th>
                        <th scope="col">Requirements</th>
                        <th scope="col">Adults</th>
                        <th scope="col">Children</th>
                        <th scope="col">Requests</th>
                        <th scope="col">Timestamp</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
                        <?php foreach ($cBookings as $k => $v) { ?>
                            <tr>
                                <th scope="row"><?php echo "YN" . $v["id"]; ?></th>
                                <td class="text-hide p-0"><?php echo $v["id"]; ?></td>
                                <td><?php echo $v["start"]; ?></td>
                                <td><?php echo $v["end"]; ?></td>
                                <td><?php echo $v["type"]; ?></td>
                                <td><?php echo $v["requirement"]; ?></td>
                                <td><?php echo $v["adults"]; ?></td>
                                <td><?php echo $v["children"]; ?></td>
                                <td><?php echo $v["requests"]; ?></td>
                                <td><?php echo $v["timestamp"]; ?></td>
                                <td><?php echo $v["status"]; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </header>

    <main role="main">

        <section class="jumbotron text-center main-jumbo">
            <div class="container pt-lg-5 pl-5 px-5">
                <h1 class="display-3">A spritual accommodation beyond ordinary</h1>
                <p class="lead" style="color:black">Book your exuding serene rooms now.</p>
                <p>
                    <?php if ($isSessionExists) { ?>
                        <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".book-now-modal-lg">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                    <?php } else { ?>
                        <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".sign-in-to-book-modal">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                    <?php } ?>
                </p>
            </div>
        </section>

        <div class="container">
            <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
                <h1 class="display-4">Details</h1>
                <p class="lead">Contact Tamil- 044-27231115, Other languages- 7708525128. Devotees travelling to pilgrimage centres like Rameshwaram, Kashi, Haridwar etc. can stay at the respective Srimatam branches. Please visit <a href="http://www.kamakoti.org">kamakoti.org </a>for more details. </p>
            </div>
        </div>

        <div class="album py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4 box-shadow">
                            <div class="card-header">
                                <h5 class="my-0 font-weight-normal">Deluxe Room</h5>
                            </div>
                            <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="img/2.jpg" data-holder-rendered="true">
                            <div class="card-body">
                                <p class="card-text">Inexpensive and located near to goddess Kamakshi of the Shaktism tradition, along with a shrine for the Hindu philosopher Adi Sankara.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <?php if ($isSessionExists) { ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                                Book
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                                Book
                                            </button>
                                        <?php } ?>
                                    </div>
                                    <small class="text-muted">650 / night</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4 box-shadow">
                            <div class="card-header">
                                <h5 class="my-0 font-weight-normal">Double Room</h5>
                            </div>
                            <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="img/3.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                            <div class="card-body">
                                <p class="card-text">Yatri Nivas is adjacent to Kamakshi Amman Temple and is in walkable distance to Sankara mutt and bus station. Neat and air conditioned rooms.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($isSessionExists) { ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                            Book
                                        </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                            Book
                                        </button>
                                    <?php } ?>
                                    <small class="text-muted">500 / night</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4 box-shadow">
                            <div class="card-header">
                                <h5 class="my-0 font-weight-normal">Single Room</h5>
                            </div>
                            <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="img/4.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                            <div class="card-body">
                                <p class="card-text">Very well located, a couple of buildings away from the Kanchi Kamakshiamman Temple. Small, but clean rooms, with a geyser in the bathroom for hot water.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($isSessionExists) { ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                            Book
                                        </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                            Book
                                        </button>
                                    <?php } ?>
                                    <small class="text-muted">400 / night</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade book-now-modal-lg" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLarge" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reservation form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="reservationModalBody">
                        <?php if ($isSessionExists && !$isAdmin) { ?>
                            <form role="form" autocomplete="off" id="reservation-form" method="post">
                                <?php if ($isSessionExists) { ?>
                                    <input type="number" id="cid" name="cid" value="<?php echo $cHandler->getId() ?>" hidden>
                                <?php } ?>
                                <div class="form-group row">
                                    <label for="startDate" class="col-sm-3 col-form-label">From
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control" id="startDate" name="startDate" min="<?php echo Util::dateToday(); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="endDate" class="col-sm-3 col-form-label">To
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomType">Room type
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2" id="roomType" name="roomType">
                                            <option value="deluxe">Deluxe room</option>
                                            <option value="double">Double room</option>
                                            <option value="single">Single room</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomRequirement">Room requirements</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2" id="roomRequirement" name="roomRequirement">
                                            <option value="no preference" selected>No preference</option>
                                            <option value="non ac">Non Ac</option>
                                            <option value="ac">Ac</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="adults">Adults
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2" id="adults" name="adults">
                                            <option selected value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="children">Children</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2" id="children" name="children">
                                            <option selected value="0">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="specialRequests">Name, address & special requirements</label>
                                    <div class="col-sm-9">
                                        <textarea rows="3" maxlength="500" id="specialRequests" name="specialRequests" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <input type="submit" class="btn btn-primary float-right" name="reservationSubmitBtn" value="Submit">
                                    </div>
                                </div>
                            </form>
                        <?php } else { ?>
                            <p>Booking is reserved for customers.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal sign-in-to-book-modal" tabindex="-1" role="dialog" aria-labelledby="signInToBookModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Sign in required</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h4>You have to <a href="sign-in.php">sign in</a> in order to reserve a room.</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="myProfileModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <?php if ($isSessionExists) { ?>
                                    <form class="form" role="form" autocomplete="off" id="update-profile-form" method="post">
                                        <input type="number" id="customerId" hidden name="customerId" value="<?php echo $cHandler->getId(); ?>">
                                        <div class="form-group">
                                            <label for="updateFullName">Full name</label>
                                            <input type="text" class="form-control" id="updateFullName" name="updateFullName" value="<?php echo $cHandler->getFullName(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="updatePhoneNumber">Phone number</label>
                                            <input type="text" class="form-control" id="updatePhoneNumber" name="updatePhoneNumber" value="<?php echo $cHandler->getPhone(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="updateEmail">Email</label>
                                            <input type="email" class="form-control" id="updateEmail" name="updateEmail" value="<?php echo $cHandler->getEmail(); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="updatePassword">New password</label>
                                            <input type="password" class="form-control" id="updatePassword" name="updatePassword" title="At least 4 characters with letters and numbers">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-md float-right" name="updateProfileSubmitBtn" value="Update">
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="container">
        <p>&copy; <a href="http://www.jayasankaramsoft.in">Jayasankaram</a> >2019</p>
    </footer>

    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/popper.js/dist/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script defer src="static/all.js" crossorigin="anonymous"></script>
    <script src="static/jquery.dataTables.min.js"></script>
    <script src="static/dataTables.select.min.js"></script>
    <script src="js/form-submission.js"></script>
    <script src="static/jquery-ui.js"></script>
    <script src="static/dataTables.buttons.min.js"></script>
    <script src="static/buttons.print.min.js"></script>

    <script>
        function alertme() {
            setTimeout(function() {
                let reservationDiv = $("#my-reservations-div");

                reservationDiv.slideToggle("slow");
                reservationDiv.slideToggle("slow");
            }, 3000);

        }

        function printDiv(elem) {
            Popup($('<div/>').append($(elem).clone()).html());
        }

        function Popup(data) {
            var mywindow = window.open('', 'invoice-box', 'height=1000,width=1000');
            mywindow.document.write('<html><head><title>invoice-box</title>');
            mywindow.document.write('<link rel="stylesheet" href="printstyle.css" type="text/css" />');
            mywindow.document.write('</head><body >');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');


            setTimeout(function() {
                mywindow.print();
                mywindow.close();
            }, 1000)
            return true;
        }

        $(document).ready(function() {

            let reservationDiv = $("#my-reservations-div");
            reservationDiv.hide();
            $(".my-reservations").click(function() {
                reservationDiv.slideToggle("slow");
            });

            var table = $('#myReservationsTbl').DataTable({
                paging: true,
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false
                }],
                dom: '<"row"<"col-sm-6"Bl><"col-sm-6"f>>' +
                    '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
                    '<"row"<"col-sm-5"i><"col-sm-7"p>>',
                fixedHeader: {
                    header: true
                },
                buttons: {
                    buttons: [{
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        title: "Yatri Nivas, Kanchi Kamakoti Peetham. 1, Kamarajar St, Periya, Kanchipuram, Tamil Nadu 631502",
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        footer: true,
                        autoPrint: true
                    }, {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        title: $('h1').text(),
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        footer: true
                    }],
                    dom: {
                        container: {
                            className: 'dt-buttons'
                        },
                        button: {
                            className: 'btn btn-default'
                        }
                    }
                }
            });

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $('#min').datepicker("getDate");
                    var max = $('#max').datepicker("getDate");
                    var startDate = new Date(data[2]);
                    var endDate = new Date(data[3]);
                    if (min != null) {
                        min.setHours(0, 0, 0, 0)
                    }
                    if (max != null) {
                        max.setHours(0, 0, 0, 0)
                    }
                    if (startDate != null) {
                        startDate.setHours(0, 0, 0, 0)
                    }
                    if (endDate != null) {
                        endDate.setHours(0, 0, 0, 0)
                    }
                    //alert(startDate + " hi "+ endDate + " hi " + min + " hi " + max)
                    if (min == null && max == null) {
                        return true;
                    }
                    if (min == null && startDate < max) {
                        return true;
                    }
                    if (max == null && endDate > min) {
                        return true;
                    }
                    if (startDate < max && endDate > min) {
                        return true;
                    }
                    return false;
                }
            );
            //var table = $('#myReservationsTbl').DataTable();

            $("#min").datepicker({
                onSelect: function() {
                    table.draw();
                },
                changeMonth: true,
                changeYear: true
            });
            $("#max").datepicker({
                onSelect: function() {
                    table.draw();
                },
                changeMonth: true,
                changeYear: true
            });


            // Event listener to the two range filtering inputs to redraw on input
            $('#min, #max').change(function() {
                table.draw();
            });
        });

        $('#example_filter').hide();
        $('#example').hide();
        $("#example_filter").css("display", "none");
        $('#example_info').hide();
        $('.dataTables_info').hide();
        $('#example_paginate').hide();
    </script>
</body>

</html>