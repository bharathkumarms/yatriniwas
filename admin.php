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
    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/models/StatusEnum.php';
    require 'app/models/RequirementEnum.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = null;
    $isSessionExists = $isAdmin = false;
    $pendingReservation = $confirmedReservation = $totalCustomers = $totalReservations = null;
    $allBookings = $cCommon = $allCustomer = null;
    if (isset($_SESSION["username"]))
    {
        $username = $_SESSION["username"];
        $isSessionExists = true;

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["customerEmail"]);

        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());
        $cAdmin->setIsAdmin((new CustomerHandler())->handleIsAdmin($cAdmin->getEmail()));
        $isAdmin = $cAdmin->isAdminSignedIn();

        // display all reservations
        $bdHandler = new BookingDetailHandler();
        $allBookings = $bdHandler->getAllBookings();
        $cCommon = new CustomerHandler();
        $allCustomer = $cCommon->getAllCustomer();

        // reservation stats
        $pendingReservation = $bdHandler->getPending();
        $confirmedReservation = $bdHandler->getConfirmed();
        $totalCustomers = $cCommon->totalCustomersCount();
        $totalReservations = count($bdHandler->getAllBookings());
    }

    ?>

    <title>Manage Booking</title>
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
                    <!-- User full name or email if logged in -->
                    <?php if ($isSessionExists) { ?>
                    <h4 class="text-white"><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
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
</header>

<main role="main">

    <?php if ($isSessionExists && $isAdmin) { ?>
    <div class="container my-3">
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div class="mr-5"><?php echo $totalReservations; ?> Reservations</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="#reservation">
                        <span class="float-left">View Details</span>
                        <span class="float-right"><i class="fa fa-angle-right"></i></span>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-warning o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-users ml-2"></i>
                        </div>
                        <div class="mr-5"><?php echo $totalCustomers; ?> Customers</div>
                    </div>
                    <a class="card-footer text-white clearfix small z-1" href="#customers">
                        <span class="float-left">View Details</span>
                        <span class="float-right"><i class="fa fa-angle-right"></i></span>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-success o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="mr-4"><?php echo $confirmedReservation; ?> Confirmed Reservations</div>
                    </div>
                    <div class="card-footer text-white clearfix small z-1"></div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-danger o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fa fa-fw fa-support"></i>
                        </div>
                        <div class="mr-5"><?php echo $pendingReservation; ?> Pending Reservations</div>
                    </div>
                    <div class="card-footer text-white clearfix small z-1"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="tableContainer">
        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="reservation-tab" data-toggle="tab" href="#reservation" role="tab"
                   aria-controls="reservation" aria-selected="true">Reservation</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="customers-tab" data-toggle="tab" href="#customers" role="tab"
                   aria-controls="customers" aria-selected="false">Customers</a>
            </li>
        </ul>
        <div class="tab-content py-3" id="adminTabContent">
            <div class="tab-pane fade show active" id="reservation" role="tabpanel" aria-labelledby="reservation-tab">
            <table border="0" cellspacing="5" cellpadding="5">
            <tbody><tr>
                <td>Start:</td>
                <td><input type="text" id="min" name="min"></td>
            </tr>
            <tr>
                <td>End:</td>
                <td><input type="text" id="max" name="max"></td>
            </tr>
            </tbody></table>
                <table id="reservationDataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th class="text-hide p-0" data-bookId="12">12</th>
                        <!--<th scope="col">Email</th>-->
                        <th scope="col">Start</th>
                        <th scope="col">End</th>
                        <th scope="col">Room</th>
                        <!--<th scope="col">Timestamp</th>-->
                        <th scope="col">Status</th>
                        <th scope="col">Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allBookings)) { ?>
                        <?php   foreach ($allBookings as $k => $v) { ?>
                            <tr>
                                <th scope="row"><?php echo "YN".$v["id"]; ?></th>
                                <td class="text-hide p-0" data-id="<?php echo $v["id"]; ?>">
                                    <?php echo $v["id"]; ?>
                                </td>
                                <?php $cid = $v["cid"]; ?>
                                <!--<td><?php echo $cCommon->getCustomerObjByCid($cid)->getEmail(); ?></td>-->
                                <td><?php echo $v["start"]; ?></td>
                                <td><?php echo $v["end"]; ?></td>
                                <td><?php echo $v["type"]; ?></td>
                                <!--<td><?php echo $v["timestamp"]; ?></td>-->
                                <td><?php echo $v["status"]; ?></td>
                                <td><?php echo $cCommon->getCustomerObjByCid($cid)->getPhone().". ".$cCommon->getCustomerObjByCid($cid)->getEmail().". ".$v["timestamp"].". ".$v["requirement"].". ".$v["requests"]; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="my-3">
                    <div class="row">
                        <div class="col-6">
                            <label class="text-secondary font-weight-bold">With selected:</label>
                            <button type="button" id="confirm-booking" class="btn btn-outline-success btn-sm">Confirm
                            </button>
                            <button type="button" id="cancel-booking" class="btn btn-outline-danger btn-sm">Cancel
                            </button>
                        </div>
                        <div class="col-6 text-right">
                            View:
                            <input type="radio" name="viewOption" value="confirmed">&nbsp;Confirmed&nbsp;
                            <input type="radio" name="viewOption" value="pending">&nbsp;Pending
                            <input type="radio" name="viewOption" value="all">&nbsp;All
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                <table id="customerTable" class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Full name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allCustomer)) { ?>
                        <?php foreach ($cCommon->getAllCustomer() as $key => $value) { ?>
                        <tr>
                            <td scope="row"><?php echo ($key + 1); ?></td>
                            <td><?php echo $value->getFullName(); ?></td>
                            <td><?php echo $value->getEmail(); ?></td>
                            <td><?php echo $value->getPhone(); ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm selected reservation(s)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to proceed with this action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmTrue">Yes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel selected reservation(s)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to proceed with this action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="cancelTrue">Yes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <?php } ?>

</main>

<footer class="container">
    <p>&copy; <a href="www.jayasankaramsoft.in">Jayasankaram</a> >2019</p>
</footer>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/popper.js/dist/popper.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script defer src="static/all.js"
        crossorigin="anonymous"></script>
<script src="static/jquery.dataTables.min.js"></script>
<script src="static/dataTables.select.min.js"></script>
<script src="js/form-submission.js"></script>
<script src="js/admin.js"></script>
<script src="static/jquery-ui.js"></script>
<script src="static/dataTables.buttons.min.js"></script>
<script src="static/buttons.print.min.js"></script>
<script>
    $(document).ready(function () {

        $.fn.dataTable.ext.search.push(
          function (settings, data, dataIndex) {
        var min = $('#min').datepicker("getDate");
        var max = $('#max').datepicker("getDate");
        var startDate = new Date(data[2]);
        var endDate = new Date(data[3]);
        if(min != null){min.setHours(0,0,0,0)}
        if(max != null){max.setHours(0,0,0,0)}
        if(startDate != null){startDate.setHours(0,0,0,0)}
        if(endDate != null){endDate.setHours(0,0,0,0)}
        //alert(startDate + " hi "+ endDate + " hi " + min + " hi " + max)
        if (min == null && max == null) { return true; }
        if (min == null && startDate < max) { return true;}
        if(max == null && endDate > min) {return true;}
        if (startDate < max && endDate > min) { return true; }
        return false;
    }
    );

        $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
        $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
        var table = $('#reservationDataTable').DataTable();

        // Event listener to the two range filtering inputs to redraw on input
        $('#min, #max').change(function () {
            table.draw();
        });
    });
</script>
</body>
</html>