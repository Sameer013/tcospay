<?php
$requestData = $_REQUEST;
$columns = array(
    0 => 'HolidayDate',
    1 => 'HolidayDescription',
    2 => 'Category',
    3 => 'action'
);
$data = array();

try {
    require_once 'includes/dbconn.php';

    $month = isset($_GET['month']) ? intval($_GET['month']) : null;
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;

    $sql = "SELECT HolidayDate, HolidayDescription,Category ,descr FROM holidaymaster h
            JOIN Category c ON h.Category = c.catcode";

    // Add conditions to filter by month and year if available
    if ($month !== null) {
        $sql .= " AND MONTH(HolidayDate) = $month";
    }

    if ($year !== null) {
        $sql .= " AND YEAR(HolidayDate) = $year";
    }

    $totalData = $db->query("SELECT COUNT(*) FROM ($sql) b")->fetchColumn();
    $totalFiltered = $totalData;

    if (!empty($requestData['search']['value'])) {
        $rd = $requestData['search']['value'];
        $sql .= " WHERE (HolidayDescription LIKE '%$rd%' OR HOLIDAYDATE LIKE '%$rd%')";
        $totalFiltered = $db->query("SELECT COUNT(*) FROM ($sql) b")->fetchColumn();
    }

    $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . " LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
    $data = $db->query($sql)->fetchAll();

    $objArr = array();
    foreach ($data as $row) {
        $object = new stdClass();

        $date = $row['HolidayDate'];
        $catcode = $row['Category'];
        $action = "<div class='flex justify-left items-center'>
                        <a class='flex items-center mr-5' id=\"edit-button\" href='javascript:;' onclick=\"load_data('$date','$catcode')\" data-tw-toggle=\"modal\" data-tw-target=\"#header-footer-modal-preview-view\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"check-square\" data-lucide=\"check-square\" class=\"lucide lucide-check-square w-4 h-4 mr-1\">
                                <polyline points=\"9 11 12 14 22 4\"></polyline>
                                <path d=\"M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11\"></path>
                            </svg>
                        </a>
                        <a class=\"flex items-center text-danger\" href=\"javascript:;\" onclick=\"remove_data('$date','$catcode')\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" icon-name=\"trash-2\" data-lucide=\"trash-2\" class=\"lucide lucide-trash-2 w-4 h-4 mr-1\">
                                <polyline points=\"3 6 5 6 21 6\"></polyline>
                                <path d=\"M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2\"></path>
                                <line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line>
                                <line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line>
                            </svg>
                        </a>
                    </div>";

        $object->HolidayDate = $row['HolidayDate'];
        $object->HolidayDescription = $row['HolidayDescription'];
        $object->Category = $row['descr'];
        $object->action = $action;

        array_push($objArr, $object);
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$json_data = array(
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $objArr
);

echo json_encode($json_data);
?>
