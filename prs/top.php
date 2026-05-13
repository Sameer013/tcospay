<style>
    @media only screen and (max-width: 375px) {
        .profile {
            width: auto;
        }

        .name {
            font-size: 12px;
        }
    }
</style>
<div class="top-bar">
    <!-- BEGIN: Breadcrumb -->
    <nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $menu_title; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $currentPage; ?>
            </li>
        </ol>
    </nav>
    <!-- END: Breadcrumb -->

    <div class="logo" style="margin-right: auto;">
        <a href="index.php" class="intro-x flex items-center">
            <!-- <img alt=""  class="h-12 rounded-lg text-white" src="dist/images/interlinkfoods.png"/> -->
            <!-- <span class="hidden xl:block font-bold text-white text-3xl ml-3 ">Interlink Foods</span> -->
        </a>
    </div>


    <!-- BEGIN: Account Menu -->
    <?php
    $userid = $_SESSION['user'];
    $uid = isset($_SESSION['uID']) ? $_SESSION['uID'] : null;
  // echo htmlspecialchars($uid);
   // echo htmlspecialchars($userid);
    include('includes/dbconn.php');
    $photoPath = 'data:image/png;base64,';
    $displayName = 'User';

    if (isset($uid) && isset($userid)) {
        $name_query = $db->prepare("SELECT UNAME FROM usertb WHERE UID = :uid");
        $name_query->bindParam(':uid', $uid);
        $name_query->execute();
        $user = $name_query->fetch(PDO::FETCH_ASSOC);
        $displayName = htmlspecialchars($user['UNAME']);
    } else {
        $name_qu = $db->prepare("SELECT Name, PHOTO FROM empmast WHERE EMPNO=:empno");
        $name_qu->bindParam(':empno', $userid);
        $name_qu->execute();
        $employee = $name_qu->fetch(PDO::FETCH_ASSOC);
        if ($employee) {
            $displayName = htmlspecialchars($employee['Name']);
            if ($employee['PHOTO']) {
                $photoPath .= base64_encode($employee['PHOTO']);
            }
        }
    }
    ?>
    <div class="intro-x dropdown w-74 h-14 profile" style="border:solid 1px black; border-radius:50px;">
        <div class="dropdown-toggle w-12 h-12 float-right rounded-full overflow-hidden shadow-lg image-fit zoom-in"
            role="button" aria-expanded="false" data-tw-toggle="dropdown">
                <?php if (!empty($photoPath) && $photoPath !== 'data:image/png;base64,'): ?>
                    <img alt="Profile Picture" src="<?= $photoPath ?>" style="padding: 3px; border-radius: 50%;" />
                <?php else: ?>
                    <img alt="No Profile Picture" src="dist/images/blank.png" style="padding: 3px; border-radius: 50%;" />
                <?php endif; ?>
            </div>
        <div class="float-right mx-3">
            <div class="pt-2 font-semibold text-xl name" style="color:black;">
                <?= $displayName ?>
            </div>
        </div>

    <div class="dropdown-menu w-56">
        <ul class="dropdown-content bg-primary text-white">
            <li class="p-2">
                <div class="font-medium">
                </div>
                <div class="text-xs text-white/70 mt-0.5 dark:text-slate-500"></div>
            </li>
            <li>
                <hr class="dropdown-divider border-white/[0.08]">
            </li>
            <li>
                <a href="change_password.php" class="dropdown-item hover:bg-white/5"> <i data-lucide="lock"
                        class="w-4 h-4 mr-2"></i> Reset Password </a>
            </li>
            <!-- <li>
                    <a href="" class="dropdown-item hover:bg-white/5"> <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Add Account </a>
                </li> -->
            <!-- <li>
                    <a href="changepswd.php" class="dropdown-item hover:bg-white/5"> <i data-lucide="lock" class="w-4 h-4 mr-2"></i> Reset Password </a>
                </li> -->
            <!-- <li>
                    <a href="" class="dropdown-item hover:bg-white/5"> <i data-lucide="help-circle" class="w-4 h-4 mr-2"></i> Help </a>
                </li> -->
            <li>
                <hr class="dropdown-divider border-white/[0.08]">
            </li>
            <li>
                <a href="logout.php" class="dropdown-item hover:bg-white/5"> <i data-lucide="toggle-right"
                        class="w-4 h-4 mr-2"></i> Logout </a>
            </li>
        </ul>
    </div>
    </div>
<!-- END: Account Menu -->
</div>