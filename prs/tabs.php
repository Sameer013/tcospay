
<div class="nav gen_tab" style="padding: 10px; margin: 12px;">
    <ul class="nav nav-tabs">
        <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_general.php') == true ? 'active' : ''; ?>" href="index_general.php" id="generalLink">General</a>
        </li>

        <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_finance.php') !== false ? 'active' : ''; ?>" id="financeLink" href="index_finance.php">Finance</a>
        </li>
          <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_leave.php') !== false ? 'active' : ''; ?>" id="leaveLink" href="index_leave.php">Leave</a>
        </li>
        <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_loan.php') !== false ? 'active' : ''; ?>" id="loanLink" href="index_loan.php">Loan</a>
        </li>

        <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_incentive.php') !== false ? 'active' : ''; ?>" id="incentiveLink" href="index_incentive.php">Incentives/Increment Details</a>
        </li>

        <li class="nav-item">
        <a class="nav-link linkempno <?php echo strpos($_SERVER['REQUEST_URI'], 'index_other_details.php') !== false ? 'active' : ''; ?>" id="otherLink" href="index_other_details.php">Other Details</a>
        </li>

    </ul>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
        $(document).ready(function() {
            $('.nav-link').click(function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                var link = $(this).attr("href");
                setTimeout(function() {
                    window.location.href = link;
                }, 100);
            });
        });
    </script>




