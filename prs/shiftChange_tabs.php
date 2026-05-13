
<div class="nav gen_tab" style="padding: 10px; margin: 12px;">
    <ul class="nav nav-tabs">
        <li class="nav-item">
        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'index_cat_shift_change.php') == true ? 'active' : ''; ?>" href="index_cat_shift_change.php" id="catShiftLink">Category Shift Change</a>
        </li>

        <li class="nav-item">
        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'index_emp_shift_change.php') !== false ? 'active' : ''; ?>" id="empShiftLink" href="index_emp_shift_change.php">Employee Shift Change</a>
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




